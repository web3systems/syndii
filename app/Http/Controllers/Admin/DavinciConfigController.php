<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Service;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Vendor;
use App\Models\ApiKey;
use App\Models\Setting;
use App\Models\MainSetting;
use App\Models\FineTune;
use App\Models\FineTuneModel;
use App\Models\ImageCredit;
use App\Models\ApiManagement;
use Yajra\DataTables\DataTables;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;
use DB;


class DavinciConfigController extends Controller
{
    /**
     * Display TTS configuration settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = new Service();
        $verify = $api->verify_license();
        $notification = $verify['status'];

        $languages = Language::orderBy('languages.language', 'asc')->get();
        $filters = Setting::where('name', 'words_filter')->first();

        # Set Voice Types
        $voiceover_languages = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->join('voiceover_languages', 'voices.language_code', '=', 'voiceover_languages.language_code')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->select('voiceover_languages.id', 'voiceover_languages.language', 'voices.language_code', 'voiceover_languages.language_flag')                
            ->distinct()
            ->orderBy('voiceover_languages.language', 'asc')
            ->get();

        $voices = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->orderBy('voices.voice_type', 'desc')
            ->orderBy('voices.voice', 'asc')
            ->get();
        
        
        $models = FineTuneModel::all();
        $type = (isset($verify['type'])) ? $verify['type'] : '';
        $vendors = explode(',', config('settings.voiceover_free_tier_vendors'));
        $all_models = explode(',', config('settings.free_tier_models'));
        $settings = MainSetting::first();
        $images = explode(',', $settings->image_vendors);



        return view('admin.davinci.configuration.index', compact('languages', 'voiceover_languages', 'voices', 'filters', 'models', 'notification', 'type', 'vendors', 'all_models', 'images'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {  

            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_STORAGE', request('default-storage'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_MODEL_ADMIN', request('default-model-admin'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_EMBEDDING_MODEL', request('default-embedding-model'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_LANGUAGE', request('default-language'));
            $this->storeConfiguration('DAVINCI_SETTINGS_TEMPLATES_ACCESS_ADMIN', request('templates-admin'));
            $this->storeConfiguration('DAVINCI_SETTINGS_MAX_RESULTS_LIMIT_ADMIN', request('max-results-admin'));
            $this->storeConfiguration('DAVINCI_SETTINGS_WIZARD_IMAGE_VENDOR', request('wizard-image-vendor'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CHAT_DEFAULT_VOICE', request('chat-default-voice'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CUSTOM_CHATS', request('custom-chats'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CUSTOM_TEMPLATES', request('custom-templates'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_SSML_EFFECT', request('set-ssml-effects'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_MAX_CHAR_LIMIT', request('set-max-chars'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_MAX_VOICE_LIMIT', request('set-max-voices'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_DEFAULT_STORAGE', request('set-storage-option'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_DEFAULT_LANGUAGE', request('language'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_DEFAULT_VOICE', request('voice'));
            $this->storeConfiguration('DAVINCI_SETTINGS_WHISPER_MAX_AUDIO_SIZE', request('set-max-audio-size'));
            $this->storeConfiguration('DAVINCI_SETTINGS_WHISPER_DEFAULT_STORAGE', request('set-whisper-storage-option'));

            $image_vendors = '';
            if (!is_null(request('image_vendors'))) {
                foreach (request('image_vendors') as $key => $value) {
                    if ($key === array_key_last(request('image_vendors'))) {
                        $image_vendors .= $value; 
                    } else {
                        $image_vendors .= $value . ', '; 
                    }   
                }
            }

            $this->storeValues($image_vendors, 'image_vendors');
            $this->storeValues(request('realtime-engine'), 'realtime_data_engine');

            Setting::where('name', 'words_filter')->update(['value' => request('words-filter')]);

            
            # Enable/Disable Main Features
            #=================================================================================
            if (request('vision-for-chat-user') == 'on') {
                $this->storeConfiguration('DAVINCI_SETTINGS_VISION_FOR_CHAT_FEATURE_USER', 'allow'); 
            } else {
                $this->storeConfiguration('DAVINCI_SETTINGS_VISION_FOR_CHAT_FEATURE_USER', 'deny');
            }


            $this->storeCheckbox(request('code-feature-user'), 'code_feature');
            $this->storeCheckbox(request('vision-feature-user'), 'vision_feature');
            $this->storeCheckbox(request('wizard-feature-user'), 'wizard_feature');
            $this->storeCheckbox(request('team-members-feature'), 'team_member_feature');
            $this->storeCheckbox(request('chat-feature-user'), 'chat_feature');
            $this->storeCheckbox(request('image-feature-user'), 'images_feature');
            $this->storeCheckbox(request('whisper-feature-user'), 'transcribe_feature');
            $this->storeCheckbox(request('voiceover-feature-user'), 'voiceover_feature');
            $this->storeCheckbox(request('chat-file-feature-user'), 'file_chat_feature');
            $this->storeCheckbox(request('chat-web-feature-user'), 'web_chat_feature');
            $this->storeCheckbox(request('chat-image-feature-user'), 'image_chat_feature');
            $this->storeCheckbox(request('smart-editor-feature-user'), 'smart_editor_feature');
            $this->storeCheckbox(request('rewriter-feature-user'), 'rewriter_feature');
            $this->storeCheckbox(request('writer-feature-user'), 'writer_feature');
            $this->storeCheckbox(request('youtube-feature'), 'youtube_feature');
            $this->storeCheckbox(request('rss-feature'), 'rss_feature');
            $this->storeCheckbox(request('integration-feature'), 'integration_feature');                  
            $this->storeCheckbox(request('brand-voice-feature'), 'brand_voice_feature');


            $data['status'] = 200;                 
            return $data;      
        }          
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTrial(Request $request)
    {
        if ($request->ajax()) {  

            $this->storeConfiguration('DAVINCI_SETTINGS_TEMPLATES_ACCESS_USER', request('templates-user'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CHATS_ACCESS_USER', request('chat-user'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_MODEL_USER', request('default-model-user-bot'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DEFAULT_MODEL_USER_TEMPLATE', request('default-model-user-template'));           
            $this->storeConfiguration('DAVINCI_SETTINGS_MAX_RESULTS_LIMIT_USER', request('max-results-user'));
            $this->storeConfiguration('DAVINCI_SETTINGS_TEAM_MEMBERS_QUANTITY', request('team-members-quantity'));
            $this->storeConfiguration('DAVINCI_SETTINGS_FILE_RESULT_DURATION_USER', request('file-result-duration'));
            $this->storeConfiguration('DAVINCI_SETTINGS_DOCUMENT_RESULT_DURATION_USER', request('document-result-duration'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CHAT_CSV_FILE_SIZE_USER', request('max-csv-size'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CHAT_PDF_FILE_SIZE_USER', request('max-pdf-size'));
            $this->storeConfiguration('DAVINCI_SETTINGS_CHAT_WORD_FILE_SIZE_USER', request('max-word-size'));
            $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_FREE_TIER_WELCOME_CHARS', request('set-free-chars'));
            $this->storeConfiguration('DAVINCI_SETTINGS_WHISPER_FREE_TIER_WELCOME_MINUTES', request('set-free-minutes'));

            $this->storeValues(request('token_credits'), 'token_credits');
            $this->storeValues(request('image_credits'), 'image_credits');

            $voiceover_vendors = '';
            if (!is_null(request('voiceover_vendors'))) {
                foreach (request('voiceover_vendors') as $key => $value) {
                    if ($key === array_key_last(request('voiceover_vendors'))) {
                        $voiceover_vendors .= $value; 
                    } else {
                        $voiceover_vendors .= $value . ', '; 
                    }
                    
                }
                $vendors = "'". $voiceover_vendors . "'";
                $this->storeWithQuotes('DAVINCI_SETTINGS_VOICEOVER_FREE_TIER_VENDORS', $vendors);
            }


            $selected_models = '';
            if (!is_null(request('models_list'))) {
                foreach (request('models_list') as $key => $value) {
                    if ($key === array_key_last(request('models_list'))) {
                        $selected_models .= $value; 
                    } else {
                        $selected_models .= $value . ', '; 
                    }
                    
                }
                $models = "'". $selected_models . "'";
                $this->storeWithQuotes('DAVINCI_SETTINGS_FREE_TIER_MODELS', $models);
            }

            
            # Enable/Disable Main Features
            #=================================================================================
            if (request('internet-user-access') == 'on') {
                $this->storeConfiguration('DAVINCI_SETTINGS_INTERNET_ACCESS_FREE_TIER_USER', 'allow'); 
            } else {
                $this->storeConfiguration('DAVINCI_SETTINGS_INTERNET_ACCESS_FREE_TIER_USER', 'deny');
            }

            $this->storeCheckbox(request('chat-user-access'), 'chat_feature_free_tier');
            $this->storeCheckbox(request('voiceover-user-access'), 'voiceover_feature_free_tier');
            $this->storeCheckbox(request('transcribe-user-access'), 'transcribe_feature_free_tier');
            $this->storeCheckbox(request('images-user-access'), 'images_feature_free_tier');
            $this->storeCheckbox(request('writer-user-access'), 'writer_feature_free_tier');
            $this->storeCheckbox(request('vision-user-access'), 'vision_feature_free_tier');
            $this->storeCheckbox(request('wizard-user-access'), 'wizard_feature_free_tier');
            $this->storeCheckbox(request('chat-file-user-access'), 'file_chat_feature_free_tier');
            $this->storeCheckbox(request('chat-image-user-access'), 'image_chat_feature_free_tier');
            $this->storeCheckbox(request('chat-web-user-access'), 'web_chat_feature_free_tier');
            $this->storeCheckbox(request('smart-editor-user-access'), 'smart_editor_feature_free_tier');
            $this->storeCheckbox(request('rewriter-user-access'), 'rewriter_feature_free_tier');
            $this->storeCheckbox(request('brand-voice-user-access'), 'brand_voice_feature_free_tier');
            $this->storeCheckbox(request('youtube-user-access'), 'youtube_feature_free_tier');
            $this->storeCheckbox(request('rss-user-access'), 'rss_feature_free_tier');
            $this->storeCheckbox(request('code-user-access'), 'code_feature_free_tier');
            $this->storeCheckbox(request('integration-user-access'), 'integration_feature_free_tier');
            $this->storeCheckbox(request('team-member-user-access'), 'team_member_feature_free_tier');

            $data['status'] = 200;                 
            return $data;      
        }          
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPICredit(Request $request)
    {
        $models = ApiManagement::get();
        $config = MainSetting::first();

        return view('admin.davinci.configuration.api', compact('models', 'config'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPICredit(Request $request)
    {
        $this->storeValues(request('model_charge_type'), 'model_charge_type');
        $this->storeValues(request('model_credit_name'), 'model_credit_name');
        $this->storeValues(request('model_disabled_vendors'), 'model_disabled_vendors');

        $values = array_slice($request->all(), 4);

        $this->storeCreditValues($values);

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIOpenai(Request $request)
    {
        return view('admin.davinci.configuration.vendors.openai.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIOpenai(Request $request)
    {
        $this->storeConfiguration('OPENAI_SECRET_KEY', request('secret-key'));
        $this->storeConfiguration('DAVINCI_SETTINGS_OPENAI_KEY_USAGE', request('openai-key-usage'));
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_OPENAI_STANDARD', request('enable-openai-std'));
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_OPENAI_NEURAL', request('enable-openai-nrl'));
        $this->storeConfiguration('DAVINCI_SETTINGS_PERSONAL_OPENAI_API_KEY', request('personal-openai-api'));

         # Enable/Disable Openai Voices
         if (request('enable-openai-nrl') == 'on') {
            $openai_nrl = Vendor::where('vendor_id', 'openai_nrl')->first();
            $openai_nrl->enabled = 1;
            $openai_nrl->save();
        } else {
            $openai_nrl = Vendor::where('vendor_id', 'openai_nrl')->first();
            $openai_nrl->enabled = 0;
            $openai_nrl->save();
        }

        if (request('enable-openai-std') == 'on') {
            $openai_std = Vendor::where('vendor_id', 'openai_std')->first();
            $openai_std->enabled = 1;
            $openai_std->save();

        } else {
            $openai_std = Vendor::where('vendor_id', 'openai_std')->first();
            $openai_std->enabled = 0;
            $openai_std->save();
        }

        if (request('enable-openai-std') == 'on') {
            DB::table('voices')->where('vendor_id', 'openai_std')->update(array('status' => 'active'));    
        } else {
            DB::table('voices')->where('vendor_id', 'openai_std')->update(array('status' => 'deactive'));
        }

        if (request('enable-openai-nrl') == 'on') {
            DB::table('voices')->where('vendor_id', 'openai_nrl')->update(array('status' => 'active'));    
        } else {
            DB::table('voices')->where('vendor_id', 'openai_nrl')->update(array('status' => 'deactive'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIAnthropic(Request $request)
    {
        return view('admin.davinci.configuration.vendors.anthropic.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIAnthropic(Request $request)
    {
        $this->storeConfiguration('ANTHROPIC_API_KEY', request('anthropic-api-key'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIDeepseek(Request $request)
    {
        $config = MainSetting::first();

        return view('admin.davinci.configuration.vendors.deepseek.setting', compact('config'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIDeepseek(Request $request)
    {
        $this->storeValues(request('deepseek_api'), 'deepseek_api');
        $this->storeValues(request('deepseek_base_url'), 'deepseek_base_url');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIxAI(Request $request)
    {
        $config = MainSetting::first();

        return view('admin.davinci.configuration.vendors.xai.setting', compact('config'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIxAI(Request $request)
    {
        $this->storeValues(request('xai_api'), 'xai_api');
        $this->storeValues(request('xai_base_url'), 'xai_base_url');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIGoogle(Request $request)
    {
        return view('admin.davinci.configuration.vendors.google.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIGoogle(Request $request)
    {
        $this->storeConfiguration('GEMINI_API_KEY', request('gemini-api-key'));
        $this->storeConfiguration('GOOGLE_APPLICATION_CREDENTIALS', request('gcp-configuration-path'));
        $this->storeConfiguration('GOOGLE_STORAGE_BUCKET', request('gcp-bucket'));
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_GCP', request('enable-gcp'));

        # Enable/Disable GCP Voices
        #==================================================================================
        if (request('enable-gcp') == 'on') {
            $gcp_nrl = Vendor::where('vendor_id', 'gcp_nrl')->first();
            $gcp_nrl->enabled = 1;
            $gcp_nrl->save();

        } else {
            $gcp_nrl = Vendor::where('vendor_id', 'gcp_nrl')->first();
            $gcp_nrl->enabled = 0;
            $gcp_nrl->save();
        }


        if (request('enable-gcp') == 'on') {
            DB::table('voices')->where('vendor_id', 'gcp_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'gcp_nrl')->update(array('status' => 'deactive'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIStability(Request $request)
    {
        return view('admin.davinci.configuration.vendors.stablediffusion.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIStability(Request $request)
    {
        $this->storeConfiguration('STABLE_DIFFUSION_API_KEY', request('stable-diffusion-key'));
        $this->storeConfiguration('DAVINCI_SETTINGS_SD_KEY_USAGE', request('sd-key-usage'));      
        $this->storeConfiguration('DAVINCI_SETTINGS_PERSONAL_SD_API_KEY', request('personal-sd-api'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIAzure(Request $request)
    {
        return view('admin.davinci.configuration.vendors.azure.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIAzure(Request $request)
    {
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_AZURE', request('enable-azure'));
        $this->storeConfiguration('AZURE_SUBSCRIPTION_KEY', request('set-azure-key'));
        $this->storeConfiguration('AZURE_DEFAULT_REGION', request('set-azure-region'));

        # Enable/Disable Azure Voices
        if (request('enable-azure') == 'on') {
            $azure_nrl = Vendor::where('vendor_id', 'azure_nrl')->first();
            $azure_nrl->enabled = 1;
            $azure_nrl->save();

        } else {
            $azure_nrl = Vendor::where('vendor_id', 'azure_nrl')->first();
            $azure_nrl->enabled = 0;
            $azure_nrl->save();
        }

        if (request('enable-azure') == 'on') {
            DB::table('voices')->where('vendor_id', 'azure_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'azure_nrl')->update(array('status' => 'deactive'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIElevenlabs(Request $request)
    {
        return view('admin.davinci.configuration.vendors.elevenlabs.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIElevenlabs(Request $request)
    {
        $this->storeConfiguration('ELEVENLABS_API_KEY', request('set-elevenlabs-api'));
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_ELEVENLABS', request('enable-elevenlabs'));

         # Enable/Disable Elevenlabs Voices
         if (request('enable-elevenlabs') == 'on') {
            $elevenlabs_nrl = Vendor::where('vendor_id', 'elevenlabs_nrl')->first();
            $elevenlabs_nrl->enabled = 1;
            $elevenlabs_nrl->save();

        } else {
            $elevenlabs_nrl = Vendor::where('vendor_id', 'elevenlabs_nrl')->first();
            $elevenlabs_nrl->enabled = 0;
            $elevenlabs_nrl->save();
        }


        if (request('enable-elevenlabs') == 'on') {
            DB::table('voices')->where('vendor_id', 'elevenlabs_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'elevenlabs_nrl')->update(array('status' => 'deactive'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIAWS(Request $request)
    {
        return view('admin.davinci.configuration.vendors.aws.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIAWS(Request $request)
    {
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_AWS_STANDARD', request('enable-aws-std'));
        $this->storeConfiguration('DAVINCI_SETTINGS_VOICEOVER_ENABLE_AWS_NEURAL', request('enable-aws-nrl'));
        $this->storeConfiguration('AWS_ACCESS_KEY_ID', request('set-aws-access-key'));
        $this->storeConfiguration('AWS_SECRET_ACCESS_KEY', request('set-aws-secret-access-key'));
        $this->storeConfiguration('AWS_DEFAULT_REGION', request('set-aws-region'));
        $this->storeConfiguration('AWS_BUCKET', request('set-aws-bucket'));
        
        # Enable/Disable AWS Voices
        if (request('enable-aws-nrl') == 'on') {
            $aws_nrl = Vendor::where('vendor_id', 'aws_nrl')->first();
            $aws_nrl->enabled = 1;
            $aws_nrl->save();
        } else {
            $aws_nrl = Vendor::where('vendor_id', 'aws_nrl')->first();
            $aws_nrl->enabled = 0;
            $aws_nrl->save();
        }

        if (request('enable-aws-std') == 'on') {
            $aws_std = Vendor::where('vendor_id', 'aws_std')->first();
            $aws_std->enabled = 1;
            $aws_std->save();

        } else {
            $aws_std = Vendor::where('vendor_id', 'aws_std')->first();
            $aws_std->enabled = 0;
            $aws_std->save();
        }


        if (request('enable-aws-std') == 'on') {
            DB::table('voices')->where('vendor_id', 'aws_std')->update(array('status' => 'active'));    
        } else {
            DB::table('voices')->where('vendor_id', 'aws_std')->update(array('status' => 'deactive'));
        }

        if (request('enable-aws-nrl') == 'on') {
            DB::table('voices')->where('vendor_id', 'aws_nrl')->update(array('status' => 'active'));    
        } else {
            DB::table('voices')->where('vendor_id', 'aws_nrl')->update(array('status' => 'deactive'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIStorj(Request $request)
    {
        return view('admin.davinci.configuration.vendors.storj.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIStorj(Request $request)
    {
        $this->storeConfiguration('STORJ_ACCESS_KEY_ID', request('set-storj-access-key'));
        $this->storeConfiguration('STORJ_SECRET_ACCESS_KEY', request('set-storj-secret-access-key'));
        $this->storeConfiguration('STORJ_BUCKET', request('set-storj-bucket')); 

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIDropbox(Request $request)
    {
        return view('admin.davinci.configuration.vendors.dropbox.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIDropbox(Request $request)
    {
        $this->storeConfiguration('DROPBOX_APP_KEY', request('set-dropbox-app-key'));
        $this->storeConfiguration('DROPBOX_APP_SECRET', request('set-dropbox-secret-key'));
        $this->storeConfiguration('DROPBOX_ACCESS_TOKEN', request('set-dropbox-access-token'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIWasabi(Request $request)
    {
        return view('admin.davinci.configuration.vendors.wasabi.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIWasabi(Request $request)
    {
        $this->storeConfiguration('WASABI_ACCESS_KEY_ID', request('set-wasabi-access-key'));
        $this->storeConfiguration('WASABI_SECRET_ACCESS_KEY', request('set-wasabi-secret-access-key'));
        $this->storeConfiguration('WASABI_DEFAULT_REGION', request('set-wasabi-region'));
        $this->storeConfiguration('WASABI_BUCKET', request('set-wasabi-bucket'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPICloudflare(Request $request)
    {
        return view('admin.davinci.configuration.vendors.cloudflare.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPICloudflare(Request $request)
    {
        $this->storeConfiguration('CLOUDFLARE_R2_ACCESS_KEY_ID', request('set-r2-access-key'));
        $this->storeConfiguration('CLOUDFLARE_R2_SECRET_ACCESS_KEY', request('set-r2-secret-access-key'));
        $this->storeConfiguration('CLOUDFLARE_R2_BUCKET', request('set-r2-bucket'));
        $this->storeConfiguration('CLOUDFLARE_R2_ENDPOINT', request('set-r2-endpoint'));
        $this->storeConfiguration('CLOUDFLARE_R2_URL', request('set-r2-url'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPISerper(Request $request)
    {
        return view('admin.davinci.configuration.vendors.serper.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPISerper(Request $request)
    {
        $this->storeConfiguration('SERPER_API_KEY', request('set-serper-api'));

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPIYoutube(Request $request)
    {
        return view('admin.davinci.configuration.vendors.youtube.setting');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPIYoutube(Request $request)
    {
        $this->storeValues(request('youtube-api'), 'youtube_api');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();  
    }


    /**
     * Record in .env file
     */
    private function storeConfiguration($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));

        }
    }


    private function storeWithQuotes($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . '\'' . env($key) . '\'', $key . '=' . $value, file_get_contents($path)
            ));

        }
    }


    private function storeCheckbox($checkbox, $field_name)
    {
        if ($checkbox == 'on') {
            $status = true; 
        } else {
            $status = false;
        }

        $settings = MainSetting::first();
        $settings->update([
            $field_name => $status
        ]);
    }


    private function storeValues($value, $field_name)
    {
        $settings = MainSetting::first();
        $settings->update([
            $field_name => $value
        ]);
    }


    private function storeCreditValues($values)
    {
        foreach ($values as $key => $value) {
            $key = explode('__', $key);
            $model = str_replace('_', '.', $key[0]);
            $api = ApiManagement::where('model', $model)->first();

            if ($api) {
                if (end($key) != 'new') {
                    $api->update([
                        end($key) => $value
                    ]);
                } else {
                    $status = ($value == 'on') ? true : false;
    
                    $api->update([
                        end($key) => $status
                    ]);
                }
            }       
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showKeys(Request $request)
    {
        if ($request->ajax()) {
            $data = ApiKey::orderBy('engine', 'asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function($row){
                    $actionBtn = '<div>      
                                    <a class="editButton" id="' . $row["id"] . '" href="#"><i class="fa fa-edit table-action-buttons view-action-button" title="Update API Key"></i></a>          
                                    <a class="activateButton" id="' . $row["id"] . '" href="#"><i class="fa fa-check table-action-buttons request-action-button" title="Activate or Deactivate API Key"></i></a>
                                    <a class="deleteButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="Delete API Key"></i></a> 
                                </div>';     
                    return $actionBtn;
                })
                ->addColumn('created-on', function($row){
                    $created_on = '<span class="font-weight-bold">'.date_format($row["created_at"], 'd M Y').'</span><br><span>'.date_format($row["created_at"], 'H:i A').'</span>';
                    return $created_on;
                })
                ->addColumn('engine-name', function($row){
                    $name = ($row['engine'] == 'openai') ? 'OpenAI' : 'Stable Diffusion';
                    $user = '<span class="font-weight-bold">'. ucfirst($name) .'</span>';
                    return $user;
                }) 
                ->addColumn('status', function($row){
                    $status = ($row['status']) ? 'active' : 'deactive';
                    $user = '<span class="cell-box status-'.$status.'">'. ucfirst($status) .'</span>';
                    return $user;
                })
                ->rawColumns(['actions', 'created-on', 'engine-name', 'status'])
                ->make(true);
                    
        }

        return view('admin.davinci.configuration.keys');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createKeys(Request $request)
    {
        return view('admin.davinci.configuration.create');
    }


     /**
     * Store review post properly in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeKeys(Request $request)
    {
        request()->validate([
            'engine' => 'required',
            'api_key' => 'required',
            'status' => 'required',
        ]);  

        ApiKey::create([
            'engine' => $request->engine,
            'api_key' => $request->api_key,
            'status' => $request->status,
        ]);

        toastr()->success(__('API Key successfully stored'));
        return redirect()->route('admin.davinci.configs.keys');
    }


    /**
     * Update the api key
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   
        if ($request->ajax()) {

            $template = ApiKey::where('id', request('id'))->firstOrFail();
            
            $template->update(['api_key' => request('name')]);
            return  response()->json('success');
        } 
    }


    /**
     * Activate the api key
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {   
        if ($request->ajax()) {

            $template = ApiKey::where('id', request('id'))->firstOrFail();
            
            if ($template->status) {
                $template->update(['status' => false]);
                return  response()->json('deactive');
            } else {
                $template->update(['status' => true]);
                return  response()->json('active');
            }   
        } 
    }


    /**
     * Delete the api key
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {   
        if ($request->ajax()) {

            $name = ApiKey::find(request('id'));

            if($name) {

                $name->delete();

                return response()->json('success');

            } else{
                return response()->json('error');
            } 
        } 
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showImageCredits(Request $request)
    {
        $credits = ImageCredit::first();
        return view('admin.davinci.configuration.image', compact('credits'));
    }


    /**
     * Store photo studio costs in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeImageCredits(Request $request)
    {
        $prices = ImageCredit::first();
        $prices->update([
            'sd_ultra' => request('sd_ultra'),
            'sd_core' => request('sd_core'),
            'sd_3_medium' => request('sd_3_medium'),
            'sd_3_large' => request('sd_3_large'),
            'sd_3_large_turbo' => request('sd_3_large_turbo'),
            'sd_v16' => request('sd_v16'),
            'sd_xl_v10' => request('sd_xl_v10'),
            'openai_dalle_3_hd' => request('openai_dalle_3_hd'),
            'openai_dalle_3' => request('openai_dalle_3'),
            'openai_dalle_2' => request('openai_dalle_2'),
            'flux_pro' => request('flux_pro'),
            'flux_dev' => request('flux_dev'),
            'flux_schnell' => request('flux_schnell'),
            'flux_realism' => request('flux_realism'),
            'midjourney_fast' => request('midjourney_fast'),
            'midjourney_turbo' => request('midjourney_turbo'),
            'midjourney_relax' => request('midjourney_relax'),
            'clipdrop' => request('clipdrop'),
        ]);

        toastr()->success(__('Image credits were updated successfully'));
        return redirect()->route('admin.davinci.configs.image.credits');
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFineTune(Request $request)
    {
        if ($request->ajax()) {
            $data = FineTune::orderBy('created_at', 'desc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function($row){
                    $actionBtn = '<div>              
                                    <a class="deleteButton" id="'. $row["task_id"] .'" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="Delete Fine Tune Model"></i></a> 
                                </div>';     
                    return $actionBtn;
                })
                ->rawColumns(['actions'])
                ->make(true);
                    
        }

        $this->checkModels();

        $models = FineTuneModel::all();

        return view('admin.davinci.configuration.fine-tune.index', compact('models'));
    }


    public function createFineTune(Request $request)
    {
        if($request->hasFile('file')){ 

            $file_extension = $request->file('file')->getClientOriginalExtension();
            $path = $request->file('file')->getRealPath();

            if ($file_extension != 'jsonl') {
                toastr()->error(__('Only jsonl file are allowed to be uploaded for training'));
                return redirect()->back();
            }

            try {
                $upload = OpenAI::files()->upload([
                    'purpose' => 'fine-tune',
                    'file' => fopen($path, 'r'),
                ]);

                $result = OpenAI::fineTuning()->createJob([
                    "model" => $request->model,
                    "training_file" => $upload->id,
                    'validation_file' => null,
                ]);

                FineTune::create([
                    'task_id' => $result->id,
                    'base_model' => $request->model,
                    'bytes' => $upload->bytes,
                    'model_name' => ucfirst($request->name),
                    'file_name' => $upload->filename,
                    'file_id' => $upload->id,
                    'description' => $request->description,
                    'status' => 'processing',
                ]);

                toastr()->success(__('Fine Tune task has been successfully created'));
                return redirect()->back();
           
            } catch(Exception $e) {
                \Log::info($e->getMessage());
                toastr()->error($e->getMessage());
                return redirect()->back();
            }

        } else {
            toastr()->error(__('JSONL training file is required'));
            return redirect()->back();
        }

    }


    public function checkModels() 
    {
        $jobs = FineTune::where('status', 'processing')->get();

        foreach ($jobs as $job) {

            try {

                $response = OpenAI::fineTuning()->retrieveJob($job->task_id);

                if ($response->status == 'succeeded') {
                    $job->update([
                        'status' => 'succeeded',
                        'result_model' => $response->fineTunedModel
                    ]);
    
                    FineTuneModel::create([
                        'model' => $response->fineTunedModel,
                        'name' => $job->model_name,
                        'description' => $job->description,
                    ]);
                }
            } catch(Exception $e) {
                \Log::info($e->getMessage());
                toastr()->error($e->getMessage());
                return redirect()->back();
            }
           
        }
        
    }


    public function deleteFineTune(Request $request)
    {
        $model = FineTune::where('task_id', $request->id)->first();
        OpenAI::files()->delete($model->file_id);
        FineTuneModel::where('model', $model->result_model)->delete();
        $model->delete();

        return response()->json('success');
    }


}


