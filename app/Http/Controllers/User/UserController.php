<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Statistics\DavinciUsageService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Models\SubscriptionPlan;
use App\Models\Subscriber;
use App\Models\Language;
use App\Models\MainSetting;
use App\Models\User;
use App\Models\GiftCard;
use App\Models\GiftCardUsage;
use App\Models\GiftCardTransfer;
use App\Mail\WalletSender;
use App\Mail\WalletReceiver;
use Carbon\Carbon;
use DataTables;
use Exception;
use DB;


class UserController extends Controller
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

        $davinci = new DavinciUsageService($month, $year);

        $data = [
            'words' => $davinci->userTotalWordsGenerated(),
            'images' => $davinci->userTotalImagesGenerated(),
            'contents' => $davinci->userTotalContentsGenerated(),
            'synthesized' => $davinci->userTotalSynthesizedText(),
            'transcribed' => $davinci->userTotalTranscribedAudio(),
            'codes' => $davinci->userTotalCodesCreated(),
        ];
        
        $chart_data['word_usage'] = json_encode($davinci->userMonthlyWordsChart());
        $chart_data['image_usage'] = json_encode($davinci->userMonthlyImagesChart());
        
        $subscription = Subscriber::where('status', 'Active')->where('user_id', auth()->user()->id)->first();
        if ($subscription) {
             if(Carbon::parse($subscription->active_until)->isPast()) {
                 $subscription = false;
             } 
        } else {
            $subscription = false;
        }

        $user_subscription = ($subscription) ? SubscriptionPlan::where('id', auth()->user()->plan_id)->first() : null;

        $check_api_feature = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();

        $progress = [
            'words' => (auth()->user()->total_words > 0) ? ((auth()->user()->available_words / auth()->user()->total_words) * 100) : 0,
        ];

        return view('user.profile.index', compact('chart_data', 'data', 'subscription', 'user_subscription', 'progress', 'check_api_feature'));           
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {   
        $check_api_feature = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();

        $storage['available'] = $this->formatSize(auth()->user()->storage_total * 1000000);

        return view('user.profile.edit', compact('storage', 'check_api_feature'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editDefaults($id = null)
    {   
        if (is_null(auth()->user()->plan_id)) {
            $vendors = explode(', ', config('settings.voiceover_free_tier_vendors'));
        } else {
           $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
           $vendors = explode(', ', $plan->voiceover_vendors);
        }

        # Apply proper model based on role and subsciption
        if (auth()->user()->group == 'user') {
            $models = explode(',', config('settings.free_tier_models'));
        } elseif (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $models = explode(',', $plan->model);
        } else {            
            $models = explode(',', config('settings.free_tier_models'));
        }

        # Set Voice Types
        $languages = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->join('voiceover_languages', 'voices.language_code', '=', 'voiceover_languages.language_code')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->whereIn('voices.vendor', $vendors)
            ->select('voiceover_languages.id', 'voiceover_languages.language', 'voices.language_code', 'voiceover_languages.language_flag')                
            ->distinct()
            ->orderBy('voiceover_languages.language', 'asc')
            ->get();

        $voices = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->whereIn('voices.vendor', $vendors)
            ->orderBy('voices.voice_type', 'desc')
            ->orderBy('voices.voice', 'asc')
            ->get();

        $template_languages = Language::orderBy('languages.language', 'asc')->get();

        $check_api_feature = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();

        $settings = MainSetting::first();

        if (auth()->user()->plan_id) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan) {
                if (!is_null($plan->image_vendors)) {
                    $vendors = explode(',', $plan->image_vendors); 
                } else {
                    $vendors = ['openai'];
                }
            } else {
                $vendors = ['openai'];
            }
        } else {
            if (!is_null($settings->image_vendors)) {
                $vendors = explode(',', $settings->image_vendors);
            } else {
                $vendors = ['openai'];
            }
        }     

        return view('user.profile.default', compact('languages', 'voices', 'template_languages', 'check_api_feature', 'models', 'vendors'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {           
        $user = User::where('id', auth()->user()->id)->first();
        $user->update(request()->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user)],
            'job_role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'phone_number' => 'nullable|max:20',
            'address' => 'nullable|string|max:255',            
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]));
        
        if (request()->has('profile_photo')) {
        
            try {
                request()->validate([
                    'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048'
                ]);
                
            } catch (\Exception $e) {
                toastr()->error($e->getMessage());
                return redirect()->back();
            }
            
            $image = request()->file('profile_photo');

            $name = Str::random(20);
            
            $folder = '/uploads/img/users/';
            
            $filePath = $folder . $name . '.' . $image->getClientOriginalExtension();

            $imageTypes = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                toastr()->error(__('Avatar image must be in png, jpeg or webp formats'));
                return redirect()->back();
            } else {
                $this->uploadImage($image, $folder, 'public', $name);

                $user->profile_photo_path = $filePath;

                $user->save();
            }
            
        }

        toastr()->success(__('Profile Successfully Updated'));
        return redirect()->route('user.profile.edit', compact('user'));
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDefaults(Request $request)
    {           
        $user = User::where('id', auth()->user()->id)->first();
        $user->update(request()->validate([
            'default_voiceover_voice' => 'nullable|string|max:255',
            'default_voiceover_language' => 'nullable|string|max:255',
            'default_template_language' => 'nullable|string|max:255',
            'default_model_template' => 'nullable|string|max:255',
            'default_model_chat' => 'nullable|string|max:255',
            'default_image_model' => 'nullable|string|max:255',
        ]));

        $user->save();

        toastr()->success(__('Default settings successfully updated'));
        return redirect()->route('user.profile.defaults');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDelete($id = null)
    {   
        $check_api_feature = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();

        return view('user.profile.delete', compact('check_api_feature'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPI()
    {   
        $check_api_feature = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();

        return view('user.profile.api', compact('check_api_feature'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPI(Request $request)
    {           
        $user = User::where('id', auth()->user()->id)->first();
        $user->update([
            'personal_openai_key' => request('openai-key'),
            'personal_claude_key' => request('claude-key'),
            'personal_gemini_key' => request('gemini-key'),
            'personal_sd_key' => request('sd-key'),
        ]);

        $user->save();

        toastr()->success(__('Your personal api keys have been saved successfully'));
        return redirect()->route('user.profile.api');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accountDelete(Request $request)
    {   
        if ($request->concent) {

            $user = User::where('id', auth()->user()->id)->first();
            $user->delete();

            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            toastr()->success(__('Your account has been successfully deleted'));
            return redirect('/');
            
        } else {
            toastr()->warning(__('Please activate the checkbox to confirm account deletion'));
            return redirect()->back();
        }
          
    }


    /**
     * Upload user profile image
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $image = $file->storeAs($folder, $name .'.'. $file->getClientOriginalExtension(), $disk);

        return $image;
    }


    /**
     * Format storage space to readable format
     */
    private function formatSize($size, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $size = max($size, 0); 
        $pow = floor(($size ? log($size) : 0) / log(1000)); 
        $pow = min($pow, count($units) - 1); 
        
        $size /= pow(1000, $pow);

        return round($size, $precision) . $units[$pow]; 
    }


    public function updateReferral(Request $request)
    {
        if ($request->ajax()) {

            $check = User::where('referral_id', $request->value)->first();

            if ($check) {
                $data['status'] = 'error';
                $data['message'] = __('This Referral ID is already in use by another user, please enter another one');
                return $data;
            } else {
                $user = User::where('id', auth()->user()->id)->first();
                $user->referral_id = $request->value;
                $user->save();
            }

            $data['status'] = 'success';
            return $data;
        } 
    }


     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function themeSetting(Request $request)
    {           
        $user = User::where('id', auth()->user()->id)->first();
        $user->update(['theme' => $request->theme]);

        $user->save();
    }


    public function emailNewsletter(Request $request)
    {
        if ($request->ajax()) {
   
            $status = ($request->status == 'true') ? 1 : 0;
            $user = User::where('id', auth()->user()->id)->first();
            $user->email_opt_in = $status;
            $user->save();

            $data['status'] = 200;
            return $data;
        }  
    }


    public function showWallet(Request $request)
    {           
        if ($request->ajax()) {
            $data = GiftCardUsage::where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->get();        
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'M d Y').'</span><br><span class="text-muted">'.date_format($row["created_at"], 'H:i A').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-code', function($row){
                        $name = '<span class="font-weight-bold text-info">'.$row['code'].'</span>';
                        return $name;
                    })
                    ->addColumn('custom-value', function($row){
                        $name = '<span class="font-weight-bold">'.$row['amount']. config('payment.default_system_currency') . '</span>';
                        return $name;
                    })
                    ->addColumn('custom-status', function($row){
                        $status = ($row['status']) ? 'redeemed' : 'failed';
                        $custom_priority = '<span class="cell-box gift-'.strtolower($status).'">'.ucfirst($status).'</span>';
                        return $custom_priority;
                    })
                    ->rawColumns(['custom-status', 'custom-code', 'created-on', 'custom-value'])
                    ->make(true);
                    
        }

        $total = GiftCardUsage::where('user_id', auth()->user()->id)->count();
        $data = [];
        $data['total'] = $total;
        $data['amount'] = $total;

        return view('user.profile.wallet', compact('data'));  
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeWallet(Request $request)
    {           
        $gift_code = GiftCard::where('code', $request->code)->first();

        if($gift_code) {
            if (!$gift_code->status) {
                toastr()->warning(__('This gift code is currently disabled, please use another one'));
                return redirect()->back();
            }

            if ($gift_code->valid_until->isPast()) {
                toastr()->warning(__('This gift code is already expired and cannot be used, please provide a valid one'));
                return redirect()->back();
            }

            if ($gift_code->usages_left == 0) {
                toastr()->warning(__('This gift code is already depleted, please provide a valid one'));
                return redirect()->back();
            }

            if (!$gift_code->reusable) {
                $usage = GiftCardUsage::where('user_id', auth()->user()->id)->where('code', $request->code)->first();
                if($usage) {
                    toastr()->warning(__('You have already used this gift code, please provide a new one'));
                    return redirect()->back();
                }
            }

            GiftCardUsage::create([
                'user_id' => auth()->user()->id,
                'username' => auth()->user()->name,
                'email' => auth()->user()->email,
                'code' => $gift_code->code,
                'amount' => $gift_code->amount,
                'status' => true
            ]);

            $user = User::where('id', auth()->user()->id)->first();
            $user->wallet = $user->wallet + $gift_code->amount;
            $user->save();

            $gift_code->usages_left = $gift_code->usages_left - 1;
            $gift_code->save();

            toastr()->success(__('Gift code has been successfully redeemed!'));
            return redirect()->back();

        } else {
            toastr()->error(__('Invalid gift code provided, please provide a valid one'));
            return redirect()->back();
        }
           
    }


    public function transferWallet(Request $request)
    {        
        if ($request->ajax()) {   
            request()->validate([
                'email' => 'required|string|email',
                'amount' => 'required|numeric|min:1'
            ]);

            $target_user = User::where('email', $request->email)->first();

            if($target_user) {

                if ($target_user->id == auth()->user()->id) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('You cannot transfer to yourself')
                    ]); 
                }

                if ($request->amount > auth()->user()->wallet) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('You are trying to transfer more than what you have, make sure to set a lower value')
                    ]); 
                }

                $target_user->wallet = $target_user->wallet + $request->amount;
                $target_user->save();
                
                $user = User::where('id', auth()->user()->id)->first();
                $user->wallet = $user->wallet - $request->amount;
                $user->save();

                $transfer_id = strtoupper(Str::random(10));

                GiftCardTransfer::create([
                    'sender_user_id' => auth()->user()->id,
                    'sender_username' => auth()->user()->name,
                    'sender_email' => auth()->user()->email,
                    'amount' => $request->amount,
                    'transfer_id' => $transfer_id,
                    'receiver_user_id' => $target_user->id,
                    'receiver_username' => $target_user->name,
                    'receiver_email' => $target_user->email,
                ]);

                try {
                    Mail::to($user)->send(new WalletSender($target_user, $request->amount, config('payment.default_system_currency')));
                    Mail::to($target_user)->send(new WalletReceiver($user, $request->amount, config('payment.default_system_currency')));


                } catch (Exception $e) {
                    Log::info('SMTP settings are not setup to send transfer statuses via email: '. $e->getMessage());
                }

                return response()->json([
                    'status' => 200,
                    'message' => __('You have successfully transfered funds to your friend!')
                ]); 
                
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => __('Looks like your friend did not yet register with us, let him know to sign up soon')
                ]); 
            }
        }
           
    }


    public function transferList(Request $request)
    {
        if ($request->ajax()) {
            $data = GiftCardTransfer::where('sender_user_id', auth()->user()->id)->orWhere('receiver_user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->get();        
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'M d Y').'</span><br><span class="text-muted">'.date_format($row["created_at"], 'H:i A').'</span>';
                        return $created_on;
                    })
                    ->addColumn('receiver', function($row){
                        $user = '<div class="d-flex">
                                <div class="widget-user-name"><span class="font-weight-bold">'. $row['receiver_username'] .'</span> <br> <span class="text-muted">'.$row["receiver_email"].'</span></div>
                            </div>';                        
                        
                        return $user;
                    })
                    ->addColumn('sender', function($row){
                        $user = '<div class="d-flex">
                                <div class="widget-user-name"><span class="font-weight-bold">'. $row['sender_username'] .'</span> <br> <span class="text-muted">'.$row["sender_email"].'</span></div>
                            </div>';                        
                        
                        return $user;
                    })
                    ->addColumn('custom-value', function($row){
                        $name = '<span class="font-weight-bold">'.$row['amount']. config('payment.default_system_currency') . '</span>';
                        return $name;
                    })
                    ->addColumn('custom-status', function($row){
                        $status = ($row['status']) ? 'transfered' : 'failed';
                        $custom_priority = '<span class="cell-box gift-'.strtolower($status).'">'.ucfirst($status).'</span>';
                        return $custom_priority;
                    })
                    ->rawColumns(['custom-status', 'created-on', 'custom-value', 'receiver', 'sender'])
                    ->make(true);
                    
        }
    
    }
    
}
