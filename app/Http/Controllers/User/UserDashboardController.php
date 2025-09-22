<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use App\Services\Statistics\DavinciUsageService;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteTemplate;
use App\Models\CustomTemplate;
use App\Models\Template;
use App\Models\SubscriptionPlan;
use App\Models\CustomChat;
use App\Models\Chat;
use App\Models\Content;
use App\Models\SupportTicket;
use App\Models\FavoriteChat;
use App\Models\MainSetting;
use App\Models\Image;

class UserDashboardController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                         
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $davinci_usage = new DavinciUsageService($month, $year);

        $configs = MainSetting::first();

        $data = [
            'words' => $davinci_usage->userTotalWordsGenerated(),
            'documents' => $davinci_usage->userTotalContentsGenerated(),
            'images' => $davinci_usage->userTotalImagesGenerated(),
            'synthesized' => $davinci_usage->userTotalSynthesizedText(),
            'transcribed' => $davinci_usage->userTotalTranscribedAudio(),
        ];

        $chart_data['user_monthly_usage'] = json_encode($davinci_usage->userHoursSavedChart());

        $template_quantity = FavoriteTemplate::where('user_id', auth()->user()->id)->count();
        $templates = Template::select('templates.*', 'favorite_templates.*')->where('favorite_templates.user_id', auth()->user()->id)->join('favorite_templates', 'favorite_templates.template_code', '=', 'templates.template_code')->where('status', true)->orderBy('professional', 'asc')->get();       
        $custom_templates = CustomTemplate::select('custom_templates.*', 'favorite_templates.*')->where('favorite_templates.user_id', auth()->user()->id)->join('favorite_templates', 'favorite_templates.template_code', '=', 'custom_templates.template_code')->where('status', true)->orderBy('professional', 'asc')->get();    
        
        $chat_quantity = FavoriteChat::where('user_id', auth()->user()->id)->count();
        $favorite_chats = Chat::select('chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'chats.chat_code')->where('status', true)->orderBy('category', 'asc')->get();       
        $custom_chats = CustomChat::select('custom_chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'custom_chats.chat_code')->where('status', true)->get();       

        $plan = (auth()->user()->plan_id) ? SubscriptionPlan::where('id', auth()->user()->plan_id)->first() : '';
        $subscription = ($plan) ? $plan->plan_name : ''; 
        $term = ($plan) ? $plan->payment_frequency : null;

        $documents = Content::where('user_id', auth()->user()->id)->whereNotNull('title')->latest()->paginate(7);
        $tickets = SupportTicket::where('user_id', auth()->user()->id)->latest()->paginate(8);
        $notifications = Auth::user()->notifications->where('type', 'App\Notifications\GeneralNotification')->all();
        $total_words = $davinci_usage->userTotalWordsGenerated() / 300;

        $total_documents = $davinci_usage->userTotalContentsGenerated() + $davinci_usage->userTotalImagesGenerated() + $davinci_usage->userTotalSynthesizedText() + $davinci_usage->userTotalTranscribedAudio();

        if ($total_documents != 0) {
            $content_documents = $davinci_usage->userTotalContentsGenerated() / $total_documents;
            $content_images = $davinci_usage->userTotalImagesGenerated() / $total_documents;
            $content_voiceovers = $davinci_usage->userTotalSynthesizedText() / $total_documents;
            $content_transcripts = $davinci_usage->userTotalTranscribedAudio() / $total_documents;
        } else {
            $content_documents = 0; $content_images = 0; $content_voiceovers = 0; $content_transcripts = 0;
        }

        if (is_null(auth()->user()->plan_id)) {
            if (auth()->user()->tokens == -1) {
                $remaining_tokens = 999999999;
                $used_tokens = 0;
                $balance = __('Unlimited');
            } else {
                $remaining_tokens = auth()->user()->tokens;
                $used_tokens = (($configs->token_credits - auth()->user()->tokens) > 0) ? ($configs->token_credits - auth()->user()->tokens) : 0;
                $balance = $remaining_tokens;
            }
        } else {
            SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if (auth()->user()->tokens == -1) {
                $remaining_tokens = 999999999;
                $used_tokens = 0;
                $balance = __('Unlimited');
            } else {
                $remaining_tokens = auth()->user()->tokens;
                $used_tokens = (($plan->token_credits - auth()->user()->tokens) > 0) ? ($plan->token_credits - auth()->user()->tokens) : 0;
                $balance = $remaining_tokens;
            }
        }

       $latest_images = Image::where('user_id', auth()->user()->id)->latest()->take(10)->get();
        

        return view('user.dashboard.index', compact('latest_images', 'remaining_tokens', 'used_tokens', 'balance', 'data', 'configs', 'chart_data', 'template_quantity', 'templates', 'subscription', 'custom_templates', 'chat_quantity', 'favorite_chats', 'custom_chats', 'term', 'documents', 'tickets', 'notifications', 'total_words', 'content_documents', 'content_images', 'content_voiceovers', 'content_transcripts'));           
    }


    /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favorite(Request $request) 
    {
        if ($request->ajax()) {

            $template = Template::where('template_code', request('id'))->first(); 

            $favorite = FavoriteTemplate::where('template_code', $template->template_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteTemplate();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->template_code = $template->template_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


    
    /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favoriteCustom(Request $request) 
    {
        if ($request->ajax()) {

            $template = CustomTemplate::where('template_code', request('id'))->first(); 

            $favorite = FavoriteTemplate::where('template_code', $template->template_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteTemplate();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->template_code = $template->template_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


}
