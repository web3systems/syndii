<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use App\Services\HelperService;
use App\Models\SubscriptionPlan;
use App\Models\FavoriteChat;
use App\Models\ChatConversation;
use App\Models\ChatCategory;
use App\Models\ChatHistory;
use App\Models\ChatPrompt;
use App\Models\ApiKey;
use App\Models\Chat;
use App\Models\User;
use GuzzleHttp\Client;


class ChatImageController extends Controller
{

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        # Check user permission to use the feature
        if (auth()->user()->group == 'user') {
            if (config('settings.chat_image_user_access') != 'allow') {
               toastr()->warning(__('Chat Image feature is not available for free tier users, subscribe to get a proper access'));
               return redirect()->route('user.plans');
            } else {
                if (session()->has('conversation_id')) {
                    session()->forget('conversation_id');
                }
        
                $chat = Chat::where('chat_code', 'IMAGE')->first(); 
                $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'IMAGE')->orderBy('updated_at', 'desc')->get(); 
        
                $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
                $prompts = ChatPrompt::where('status', true)->get();
        
                return view('user.chat_image.index', compact('chat', 'messages', 'categories', 'prompts'));
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->chat_image_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for Chat Image feature'));
                return redirect()->back();                   
            } else {
                if (session()->has('conversation_id')) {
                    session()->forget('conversation_id');
                }
        
                $chat = Chat::where('chat_code', 'IMAGE')->first(); 
                $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'IMAGE')->orderBy('updated_at', 'desc')->get(); 
        
                $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
                $prompts = ChatPrompt::where('status', true)->get();
        
                return view('user.chat_image.index', compact('chat', 'messages', 'categories', 'prompts'));
            }
        } else {
            if (session()->has('conversation_id')) {
                session()->forget('conversation_id');
            }
    
            $chat = Chat::where('chat_code', 'IMAGE')->first(); 
            $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'IMAGE')->orderBy('updated_at', 'desc')->get(); 
    
            $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
            $prompts = ChatPrompt::where('status', true)->get();
    
            return view('user.chat_image.index', compact('chat', 'messages', 'categories', 'prompts'));
        }
    }


    /**
	*
	* Process Input Text
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {       
        if ($request->ajax()) {

            # Check personal API keys
            if (config('settings.personal_openai_api') == 'allow') {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                    return $data; 
                } else {
                    $open_ai = new OpenAi(auth()->user()->personal_openai_key);
                } 

            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    if (is_null(auth()->user()->personal_openai_key)) {
                        $data['status'] = 'error';
                        $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                        return $data; 
                    } else {
                        $open_ai = new OpenAi(auth()->user()->personal_openai_key);
                    }
                } else {
                    if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $open_ai = new OpenAi($api_keys[$key]);
                } else {
                    $open_ai = new OpenAi(config('services.openai.key'));
                }
            }

            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $open_ai = new OpenAi($api_keys[$key]);
                } else {
                    $open_ai = new OpenAi(config('services.openai.key'));
                }
            }


            # Check if user has sufficient words available to proceed
            $credit_status = $this->checkCredits('dall-e-3');
            if (!$credit_status) {
                $data['status'] = 'error';
                $data['message'] = __('Not enough media credits to proceed, subscribe or top up your media credit balance and try again');
                return $data;
            }


            $chat = new ChatHistory();
            $chat->user_id = auth()->user()->id;
            $chat->conversation_id = $request->conversation_id;
            $chat->prompt = $request->input('message');
            $chat->save();

            $complete = $open_ai->image([
                'model' => 'dall-e-3',
                'prompt' => $request->input('message'),
                'size' => '1024x1024',
                'n' => 1,
                "response_format" => "url",
                'quality' => "standard",
            ]);

            $response = json_decode($complete , true);

            if (isset($response['data'])) {

                $url = $response['data'][0]['url'];

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_URL, $url);
                $contents = curl_exec($curl);
                curl_close($curl);


                $name = 'chat-image-' . Str::random(10) . '.png';

                if (config('settings.default_storage') == 'local') {
                    Storage::disk('public')->put('images/' . $name, $contents);
                    $image_url = 'images/' . $name;
                    $image_url = URL::asset($image_url);
                } elseif (config('settings.default_storage') == 'aws') {
                    Storage::disk('s3')->put('images/' . $name, $contents, 'public');
                    $image_url = Storage::disk('s3')->url('images/' . $name);
                } elseif (config('settings.default_storage') == 'r2') {
                    Storage::disk('r2')->put('images/' . $name, $contents, 'public');
                    $image_url = Storage::disk('r2')->url('images/' . $name);
                } elseif (config('settings.default_storage') == 'wasabi') {
                    Storage::disk('wasabi')->put('images/' . $name, $contents);
                    $image_url = Storage::disk('wasabi')->url('images/' . $name);
                } elseif (config('settings.default_storage') == 'storj') {
                    Storage::disk('storj')->put('images/' . $name, $contents, 'public');
                    Storage::disk('storj')->setVisibility('images/' . $name, 'public');
                    $image_url = Storage::disk('storj')->temporaryUrl('images/' . $name, now()->addHours(167));                      
                } elseif (config('settings.default_storage') == 'dropbox') {
                    Storage::disk('dropbox')->put('images/' . $name, $contents);
                    $image_url = Storage::disk('dropbox')->url('images/' . $name);
                }

                $chat->response = $image_url;
                $chat->words = 0;
                $chat->save();

                # Update credit balance
                $this->updateBalance('dall-e-3');  

                $chat_conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first(); 
                $chat_conversation->words = 0;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();

        
                $data['status'] = 'success';
                $data['url'] = $image_url;
                $data['old'] = auth()->user()->images + auth()->user()->images_prepaid;
                $data['current'] = auth()->user()->images + auth()->user()->images_prepaid - 1;
                $data['balance'] = (auth()->user()->images == -1) ? 'unlimited' : 'counted';
                return $data; 

            } else {
                if ($response['error']['code'] == 'invalid_api_key') {
                    $message = 'Please try again, Dalle 3 model limit has been reached for today.';
                } else {
                    $message = $response['error']['message'];
                }    

                $data['status'] = 'error';
                $data['message'] = $message;
                return $data;
            }
        }

	}


    /**
	*
	* Clear Session
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function clear(Request $request) 
    {
        if (session()->has('conversation_id')) {
            session()->forget('conversation_id');
        }

        return response()->json(['status' => 'success']);
	}


    public function checkCredits($model) 
    {
        $status = true;
        
        switch ($model) {
            case 'dall-e-2':
                $status = HelperService::checkMediaCredits('openai_dalle_2');
                break;
            case 'dall-e-3':
                $status = HelperService::checkMediaCredits('openai_dalle_3');
                break;
            case 'dall-e-3-hd':
                $status = HelperService::checkMediaCredits('openai_dalle_3_hd');
                break;
            case 'stable-diffusion-v1-6':
                $status = HelperService::checkMediaCredits('sd_v16');
                break;
            case 'stable-diffusion-xl-1024-v1-0':
                $status = HelperService::checkMediaCredits('sd_xl_v10');
                break;
            case 'sd3.5-medium':
                $status = HelperService::checkMediaCredits('sd_3_medium');
                break;
            case 'sd3.5-large':
                $status = HelperService::checkMediaCredits('sd_3_large');
                break;
            case 'sd3.5-large-turbo':
                $status = HelperService::checkMediaCredits('sd_3_large_turbo');
                break;
            case 'core':
                $status = HelperService::checkMediaCredits('sd_core');
                break;
            case 'ultra':
                $status = HelperService::checkMediaCredits('sd_ultra');
                break;
            case 'flux/dev':
                $status = HelperService::checkMediaCredits('flux_dev');
                break;
            case 'flux/schnell':
                $status = HelperService::checkMediaCredits('flux_schnell');
                break;
            case 'flux-pro/new':
                $status = HelperService::checkMediaCredits('flux_pro');
                break;
            case 'flux-realism':
                $status = HelperService::checkMediaCredits('flux_realism');
                break;
            case 'midjourney/fast':
                $status = HelperService::checkMediaCredits('midjourney_fast');
                break;
            case 'midjourney/relax':
                $status = HelperService::checkMediaCredits('midjourney_relax');
                break;
            case 'midjourney/turbo':
                $status = HelperService::checkMediaCredits('midjourney_turbo');
                break;
            case 'clipdrop':
                $status = HelperService::checkMediaCredits('clipdrop');
                break;
        }

        return $status;
    }



    /**
	*
	* Update user image balance`
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function updateBalance($model) {

        switch ($model) {
            case 'dall-e-2':
    
                HelperService::updateMediaBalance('openai_dalle_2', 1);
                break;
            case 'dall-e-3':
                HelperService::updateMediaBalance('openai_dalle_3', 1);
                break;
            case 'dall-e-3-hd':
                HelperService::updateMediaBalance('openai_dalle_3_hd', 1);
                break;
            case 'stable-diffusion-v1-6':
                HelperService::updateMediaBalance('sd_v16', 1);
                break;
            case 'stable-diffusion-xl-1024-v1-0':
                HelperService::updateMediaBalance('sd_xl_v10', 1);
                break;
            case 'sd3.5-medium':
                HelperService::updateMediaBalance('sd_3_medium', 1);
                break;
            case 'sd3.5-large':
                HelperService::updateMediaBalance('sd_3_large', 1);
                break;
            case 'sd3.5-large-turbo':
                HelperService::updateMediaBalance('sd_3_large_turbo', 1);
                break;
            case 'core':
                HelperService::updateMediaBalance('sd_core', 1);
                break;
            case 'ultra':
                HelperService::updateMediaBalance('sd_ultra', 1);
                break;
            case 'flux/dev':
                HelperService::updateMediaBalance('flux_dev', 1);
                break;
            case 'flux/schnell':
                HelperService::updateMediaBalance('flux_schnell', 1);
                break;
            case 'flux-pro/new':
                HelperService::updateMediaBalance('flux_pro', 1);
                break;
            case 'flux-realism':
                HelperService::updateMediaBalance('flux_realism', 1);
                break;
            case 'midjourney/fast':
                HelperService::updateMediaBalance('midjourney_fast', 1);
                break;
            case 'midjourney/relax':
                HelperService::updateMediaBalance('midjourney_relax', 1);
                break;
            case 'midjourney/turbo':
                HelperService::updateMediaBalance('midjourney_turbo', 1);
                break;
            case 'clipdrop':
                HelperService::updateMediaBalance('clipdrop', 1);
                break;
        }
        
    }


    /**
	*
	* Chat conversation
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function conversation(Request $request) {

        if ($request->ajax()) {

            $chat = new ChatConversation();
            $chat->user_id = auth()->user()->id;
            $chat->title = 'Chat Image Conversation';
            $chat->chat_code = $request->chat_code;
            $chat->conversation_id = $request->conversation_id;
            $chat->messages = 0;
            $chat->words = 0;
            $chat->save();

            $data = 'success';
            return $data;
        }   
    }


    /**
	*
	* Chat history
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function history(Request $request) {

        if ($request->ajax()) {

            $messages = ChatHistory::where('user_id', auth()->user()->id)->where('conversation_id', $request->conversation_id)->get();
            return $messages;
        }   
    }


    /**
	*
	* Rename conversation
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function rename(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatConversation::where('conversation_id', request('conversation_id'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->title = request('name');
                    $chat->save();
    
                    $data['status'] = 'success';
                    $data['conversation_id'] = request('conversation_id');
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while changing the conversation title');
                    return $data;
                }
            } 
              
        }
	}


    /**
	*
	* Delete chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function delete(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatConversation::where('conversation_id', request('conversation_id'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->delete();

                    if (session()->has('conversation_id')) {
                        session()->forget('conversation_id');
                    }
    
                    $data['status'] = 'success';
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while deleting the chat history');
                    return $data;
                }
            } else {
                $data['status'] = 'empty';
                return $data;
            }
              
        }
	}


}
