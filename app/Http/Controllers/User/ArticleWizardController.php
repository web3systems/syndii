<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\VoiceToneTrait;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Template;
use App\Models\Content;
use App\Models\Workbook;
use App\Models\Language;
use App\Models\ApiKey;
use App\Models\User;
use App\Models\ArticleWizard;
use App\Models\FineTuneModel;
use App\Services\HelperService;
use OpenAI\Client;
use Exception;


class ArticleWizardController extends Controller
{
    use VoiceToneTrait;

    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        # Apply proper model based on role and subsciption
        if (auth()->user()->group == 'user') {
            $models = explode(',', config('settings.free_tier_models'));
        } elseif (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $models = explode(',', $plan->model);
        } else {            
            $models = explode(',', config('settings.free_tier_models'));
        }

        $fine_tunes = FineTuneModel::all();
        $default_model = auth()->user()->default_model_template;

        # Check user permission to use the feature
        if (auth()->user()->group == 'user') {
            if (config('settings.wizard_access_user') != 'allow') {
               toastr()->warning(__('AI Article Wizard feature is not available for free tier users, subscribe to get a proper access'));
               return redirect()->route('user.plans');
            } else {
                $languages = Language::orderBy('languages.language', 'asc')->get();

                $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();

                $wizard = ArticleWizard::where('user_id', auth()->user()->id)->where('current_step', '!=', 5)->first();

                if (!$wizard) {
                    $wizard = new ArticleWizard();
                    $wizard->user_id = auth()->user()->id;
                    $wizard->save();
                }

                $wizard = ArticleWizard::find($wizard->id)->toArray();

                return view('user.templates.wizard.index', compact('languages', 'workbooks', 'wizard', 'models', 'fine_tunes', 'default_model'));
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->wizard_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for AI Article Wizard feature'));
                return redirect()->back();                   
            } else {
                $languages = Language::orderBy('languages.language', 'asc')->get();

                $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();

                $wizard = ArticleWizard::where('user_id', auth()->user()->id)->where('current_step', '!=', 5)->first();

                if (!$wizard) {
                    $wizard = new ArticleWizard();
                    $wizard->user_id = auth()->user()->id;
                    $wizard->save();
                }

                $wizard = ArticleWizard::find($wizard->id)->toArray();

                return view('user.templates.wizard.index', compact('languages', 'workbooks', 'wizard', 'models', 'fine_tunes', 'default_model'));
            }
        } else {
            $languages = Language::orderBy('languages.language', 'asc')->get();

            $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();

            $wizard = ArticleWizard::where('user_id', auth()->user()->id)->where('current_step', '!=', 5)->first();

            if (!$wizard) {
                $wizard = new ArticleWizard();
                $wizard->user_id = auth()->user()->id;
                $wizard->save();
            }

            $wizard = ArticleWizard::find($wizard->id)->toArray();

            return view('user.templates.wizard.index', compact('languages', 'workbooks', 'wizard', 'models', 'fine_tunes', 'default_model'));
        }
        
    }


    /**
	*
	* Generate keywords
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function keywords(Request $request) 
    {
        if ($request->ajax()) {
            $max_tokens = 50;

           
            # Check Openai APIs
            $key = '';
            $key = $this->getOpenai();
            if ($key == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                return $data; 
            }

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }
  
            try {

                $message = [[
                        'role' => 'user',
                        'content' => "Generate $request->keywords_numbers keywords (simple words or 2 words, not phrase, not person name) about '$request->topic'. Must resut as a comma separated string without any extra details. Result format is: keyword1, keyword2, ..., keywordN. Must not write ```json."
                    ]];

                $openai_client = \OpenAI::client($key);
                
                if (in_array($request->model, ['o1', 'o1-mini', 'o3-mini'])) {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                    ]);
                } else {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$request->creativity                       
                    ]);
                }

                $result = $response->choices[0]->message->content;
                $tokens = [
                    'prompt_tokens' => $response->usage->promptTokens,
                    'completion_tokens' => $response->usage->completionTokens,
                ];
                    

                # Update credit balance
                $words = count(explode(' ', $result));
                HelperService::updateBalance($words, $request->model); 

                $flag = Language::where('language_code', $request->language)->first();

                $wizard = ArticleWizard::where('id', $request->wizard)->first();
                if (is_null($wizard->keywords)) {
                    $wizard->keywords = $result;
                } else {
                    $wizard->keywords .= ', ' . $result;
                }          
                $wizard->language = $flag->language;          
                $wizard->tone = $request->tone;          
                $wizard->creativity = (float)$request->creativity;          
                $wizard->view_point = $request->view_point;          
                $wizard->max_words = $request->words;          
                $wizard->input_tokens = $tokens['prompt_tokens'];          
                $wizard->output_tokens = $tokens['completion_tokens'];          
                $wizard->save();

                $data['old'] = auth()->user()->tokens + auth()->user()->tokens_prepaid;
                $data['current'] = auth()->user()->tokens + auth()->user()->tokens_prepaid - $words;
                $data['type'] = (auth()->user()->tokens == -1) ? 'unlimited' : 'counted';

                return response()->json(['result' => $result, 'balance' => $data]);

            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an issue with keywords generation, please try again') . $e->getMessage();
                return $data; 
            }
        }
	}


    /**
	*
	* Generate ideas
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function ideas(Request $request) 
    {
        if ($request->ajax()) {
            $max_tokens = 50;

           
            # Check Openai APIs
            $key = $this->getOpenai();
            if ($key == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                return $data; 
            }

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }

            try {

                if (!is_null($request->keywords) || $request->keywords != '') {
                    $prompt = "Generate $request->topics_number titles. Titles must be about topic:  $request->topic . Use following keywords in the titles: $request->keywords. (Without number for order). Must not write any description. Strictly create in array json data. Every title is sentence or phrase string. The depth is 1. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->topic_length. Must not write ```json.";
                } else {
                    $prompt = "Generate $request->topics_number titles. Titles must be about topic:  $request->topic . (Without number for order, titles are not keywords). Must not write any description. Strictly create in array json data. Every title is sentence or phrase string. The depth is 1. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->topic_length. Must not write ```json.";
                }

                $message = [[
                        'role' => 'user',
                        'content' => $prompt,
                    ]];

                $openai_client = \OpenAI::client($key);
                
                if (in_array($request->model, ['o1', 'o1-mini', 'o3-mini'])) {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                    ]);
                } else {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$request->creativity                       
                    ]);
                }

                $result = json_decode($response->choices[0]->message->content);
                $tokens = [
                    'prompt_tokens' => $response->usage->promptTokens,
                    'completion_tokens' => $response->usage->completionTokens,
                ];
       
                $main_string = '';
                $numItems = count($result);
                $i = 0;
                foreach ($result as $key => $value) {
                    if (++$i == $numItems) {
                        $main_string .= $value;
                    } else {
                        $main_string .= $value . ', ';
                    }
                }

                # Update credit balance
                $words = count(explode(' ', ($response->choices[0]->message->content)));
                HelperService::updateBalance($words, $request->model); 

                $wizard = ArticleWizard::where('id', $request->wizard)->first();
                if (is_null($wizard->titles)) {
                    $wizard->titles = $main_string;
                } else {
                    $wizard->titles .= ', ' . $main_string;
                }
                $flag = Language::where('language_code', $request->language)->first();
                $wizard->language = $flag->language;          
                $wizard->tone = $request->tone;          
                $wizard->creativity = (float)$request->creativity;          
                $wizard->view_point = $request->view_point;  
                $wizard->max_words = $request->words;  
                $wizard->input_tokens = $wizard->input_tokens + $tokens['prompt_tokens'];          
                $wizard->output_tokens = $wizard->output_tokens + $tokens['completion_tokens']; 
                $wizard->current_step = 1;
                $wizard->save();

                $data['old'] = auth()->user()->tokens + auth()->user()->tokens_prepaid;
                $data['current'] = auth()->user()->tokens + auth()->user()->tokens_prepaid - $words;
                $data['type'] = (auth()->user()->tokens == -1) ? 'unlimited' : 'counted';

                return response()->json(['result' => $main_string, 'balance' => $data]);

            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an issue with ideas generation, please try again') . $e->getMessage();
                return $data; 
            }
        }
	}


    /**
	*
	* Generate outlines
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function outlines(Request $request) 
    {
        if ($request->ajax()) {
            $max_tokens = 50;

           
            # Check Openai APIs
            $key = $this->getOpenai();
            if ($key == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                return $data; 
            }

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }

            try {

                if (!is_null($request->keywords) || $request->keywords != '') {
                    $prompt = "The keywords of article are $request->keywords. Generate different outlines related to $request->title (Each outline must has only $request->outline_subtitles subtitles (Without number for order, subtitles are not keywords)) $request->outline_number times. Provide response in the exat same language as the title. Use $request->tone writing tone. The depth is 1.  Must not write any description. Result must be array json data. Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->outline_subtitles(string)]]. Must not write ```json.";
                } else {
                    $prompt = "Generate different outlines related to $request->title (Each outline must has only $request->outline_subtitles subtitles (Without number for order, subtitles are not keywords)) $request->outline_number times. Provide response in the exat same language as the title. Use $request->tone writing tone. The depth is 1.  Must not write any description. Result must be array json data. Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->outline_subtitles(string)]]. Must not write ```json.";
                }

                $message = [[
                    'role' => 'user',
                    'content' => $prompt,
                ]];

                $openai_client = \OpenAI::client($key);
                
                if (in_array($request->model, ['o1', 'o1-mini', 'o3-mini'])) {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                    ]);
                } else {
                    $response = $openai_client->chat()->create([
                        'model' => $request->model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$request->creativity                       
                    ]);
                }

                $tokens = [
                    'prompt_tokens' => $response->usage->promptTokens,
                    'completion_tokens' => $response->usage->completionTokens,
                ];

                $temp = str_replace('```json', '', $response->choices[0]->message->content);
                $temp = str_replace('```', '', $temp);
                
                # Update credit balance
                $words = count(explode(' ', ($response->choices[0]->message->content)));
                HelperService::updateBalance($words, $request->model); 

                $flag = Language::where('language_code', $request->language)->first();

                $wizard = ArticleWizard::where('id', $request->wizard)->first();
                $wizard->selected_title = $request->title;
                $wizard->selected_keywords = $request->keywords;
                $wizard->language = $flag->language;          
                $wizard->tone = $request->tone;          
                $wizard->creativity = (float)$request->creativity;          
                $wizard->view_point = $request->view_point;  
                $wizard->max_words = $request->words;  
                $wizard->input_tokens = $wizard->input_tokens + $tokens['prompt_tokens'];          
                $wizard->output_tokens = $wizard->output_tokens + $tokens['completion_tokens']; 
                $wizard->current_step = 2;
                $wizard->save();

                $data['old'] = auth()->user()->tokens + auth()->user()->tokens_prepaid;
                $data['current'] = auth()->user()->tokens + auth()->user()->tokens_prepaid - $words;
                $data['type'] = (auth()->user()->tokens == -1) ? 'unlimited' : 'counted';

                return response()->json(['result' => json_decode($response->choices[0]->message->content), 'balance' => $data]);

            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an issue with ideas generation, please try again') . $e->getMessage();
                return $data; 
            }
        }
	}


    /**
	*
	* Generate talking points
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function talkingPoints(Request $request) 
    {
        if ($request->ajax()) {
            $max_tokens = 50;

           
            # Check Openai APIs
            $openai_key = $this->getOpenai();
            if ($openai_key == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                return $data; 
            }

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }

            try {

                $outlines = json_decode($request->target_outlines);
                $results = [];
                $input = [];
                $total_words = 0;

                foreach ($outlines as $key=>$outline) {
                    if ($outline == '') {
                        continue;
                    } else {
                        if (!is_null($request->keywords)) {
                            $prompt = "Generate $request->points_number talking points for this outline: $outline. It must be also relevant to this title: $request->title. Provide talking points in the exact same language as the outline. Use following keywords in the talking points: $request->keywords. The depth is 1.  Must not write any description. Use $request->tone writing tone. Strictly create in json array of objects. This is result format: [talking_point1(string), talking_point2(string), talking_point3(string), ...]. Maximum length of each talking point must be $request->points_length words. Must not write ```json.";
                        } else {
                            $prompt = "Generate $request->points_number talking points for this outline: $outline. It must be also relevant to this title: $request->title. Provide talking points in the exact same language as the outline. The depth is 1.  Must not write any description. Use $request->tone writing tone. Strictly create in json array of objects. This is result format: [talking_point1(string), talking_point2(string), talking_point3(string), ...]. Maximum length of each talking point must be $request->points_length words. Must not write ```json.";
                        }
    
                        $message = [[
                            'role' => 'user',
                            'content' => $prompt,
                        ]];
        
                        $openai_client = \OpenAI::client($openai_key);
                        
                        if (in_array($request->model, ['o1', 'o1-mini', 'o3-mini'])) {
                            $response = $openai_client->chat()->create([
                                'model' => $request->model,
                                'messages' => $message,
                                'frequency_penalty' => 0,
                                'presence_penalty' => 0,
                            ]);
                        } else {
                            $response = $openai_client->chat()->create([
                                'model' => $request->model,
                                'messages' => $message,
                                'frequency_penalty' => 0,
                                'presence_penalty' => 0,
                                'temperature' => (float)$request->creativity                       
                            ]);
                        }
        
                        $tokens = [
                            'prompt_tokens' => $response->usage->promptTokens,
                            'completion_tokens' => $response->usage->completionTokens,
                        ];

                        $temp = str_replace('```json', '', $response->choices[0]->message->content);
                        $temp = str_replace('```', '', $temp);

                        # Update credit balance
                        $words = count(explode(' ', ($response->choices[0]->message->content)));
                        $total_words += $words;

                        $results[$key] = json_decode($temp);
                        $input[$key] = $outline;
                    }                    
                }

                HelperService::updateBalance($total_words, $request->model);

                $flag = Language::where('language_code', $request->language)->first();

                $wizard = ArticleWizard::where('id', $request->wizard)->first();
                $wizard->selected_title = $request->title;
                $wizard->outlines = $request->target_outlines;
                $wizard->selected_keywords = $request->keywords;
                $wizard->language = $flag->language;          
                $wizard->tone = $request->tone;          
                $wizard->creativity = (float)$request->creativity;          
                $wizard->view_point = $request->view_point;  
                $wizard->max_words = $request->words;  
                $wizard->input_tokens = $wizard->input_tokens + $tokens['prompt_tokens'];          
                $wizard->output_tokens = $wizard->output_tokens + $tokens['completion_tokens']; 
                $wizard->current_step = 3;
                $wizard->save();

                $data['old'] = auth()->user()->tokens + auth()->user()->tokens_prepaid;
                $data['current'] = auth()->user()->tokens + auth()->user()->tokens_prepaid - $words;
                $data['type'] = (auth()->user()->tokens == -1) ? 'unlimited' : 'counted';
                
                return response()->json(['result' => json_encode($results), 'input' => json_encode($input), 'balance' => $data]);

            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an issue with talking points generation, please try again') . $e->getMessage();
                return $data; 
            }
        }
	}


    /**
	*
	* Generate images
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function images(Request $request) 
    {
        if ($request->ajax()) {

            if ($request->image_size == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('Image generation is disabled for AI Article Wizard, please proceed with the next step');
                return $data;           
            }
           
            # Check Openai APIs
            $key = $this->getOpenai();
            if ($key == 'none') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                return $data; 
            }
            
            $vendor = '';
            # Verify if user has enough credits
            $credit_status = $this->checkCredits(config('settings.wizard_image_vendor'));
            if (!$credit_status) {
                $data['status'] = 'error';
                $data['message'] = __('Not enough media credits to proceed, subscribe or top up your media credit balance and try again');
                return $data;
            }
            

            $response = '';
            $storage = '';
            $image_url = '';
            

            if (!is_null($request->image_description) || $request->image_description != '') {
                $prompt = $request->image_description;
            } else {
                $prompt = $request->title;
            }


            try {
                if (config('settings.wizard_image_vendor') == 'dall-e-2' || config('settings.wizard_image_vendor') == 'dall-e-3') {
                    $client = \OpenAI::client(config('services.openai.key'));

                    $response = $client->images()->create([
                        'model' => config('settings.wizard_image_vendor'),
                        'prompt' => $prompt,
                        'size' => $request->image_size,
                        'n' => 1,
                        'response_format' => 'url'
                    ]);

                } elseif(config('settings.wizard_image_vendor') == 'dall-e-3-hd') {
                    $client = \OpenAI::client(config('services.openai.key'));

                    $response = $client->images()->create([
                        'model' => 'dall-e-3',
                        'prompt' => $prompt,
                        'size' => $request->image_size,
                        'n' => 1,
                        'response_format' => 'url',
                        'quality' => "hd",
                    ]);

                } elseif(config('settings.wizard_image_vendor') == 'stable-diffusion-v1-6' || config('settings.wizard_image_vendor') == 'stable-diffusion-xl-1024-v1-0') {
                    $url = 'https://api.stability.ai/v1/generation/' . config('settings.wizard_image_vendor') . '/text-to-image';

                    $headers = [
                        'Authorization:' . config('services.stable_diffusion.key'),
                        'Content-Type: application/json',
                    ];

                    $resolutions = explode('x', $request->image_size);
                    $width = $resolutions[0];
                    $height = $resolutions[1];
                    $data['text_prompts'][0]['text'] = $prompt;
                    $data['text_prompts'][0]['weight'] = 1;
                    $data['height'] = (int)$height; 
                    $data['width'] = (int)$width;
                    $postdata = json_encode($data);

                    $ch = curl_init($url); 
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($result , true);

                    if (isset($response['artifacts'])) {
                        foreach ($response['artifacts'] as $key => $value) {
    
                            $image = base64_decode($value['base64']);
    
                            $name = 'sd-' . Str::random(10) . '.png';
    
                            if (config('settings.default_storage') == 'local') {
                                Storage::disk('public')->put('images/' . $name, $image);
                                $image_url = 'images/' . $name;
                                $storage = 'local';
                            } elseif (config('settings.default_storage') == 'aws') {
                                Storage::disk('s3')->put('images/' . $name, $image, 'public');
                                $image_url = Storage::disk('s3')->url('images/' . $name);
                                $storage = 'aws';
                            } elseif (config('settings.default_storage') == 'r2') {
                                Storage::disk('r2')->put('images/' . $name, $image, 'public');
                                $image_url = Storage::disk('r2')->url('images/' . $name);
                                $storage = 'r2';
                            } elseif (config('settings.default_storage') == 'wasabi') {
                                Storage::disk('wasabi')->put('images/' . $name, $image);
                                $image_url = Storage::disk('wasabi')->url('images/' . $name);
                                $storage = 'wasabi';
                            } elseif (config('settings.default_storage') == 'gcp') {
                                Storage::disk('gcs')->put('images/' . $name, $image);
                                Storage::disk('gcs')->setVisibility('images/' . $name, 'public');
                                $image_url = Storage::disk('gcs')->url('images/' . $name);
                                $storage = 'gcp';
                            } elseif (config('settings.default_storage') == 'storj') {
                                Storage::disk('storj')->put('images/' . $name, $image, 'public');
                                Storage::disk('storj')->setVisibility('images/' . $name, 'public');
                                $image_url = Storage::disk('storj')->temporaryUrl('images/' . $name, now()->addHours(167));
                                $storage = 'storj';                        
                            } elseif (config('settings.default_storage') == 'dropbox') {
                                Storage::disk('dropbox')->put('images/' . $name, $image);
                                $image_url = Storage::disk('dropbox')->url('images/' . $name);
                                $storage = 'dropbox';
                            }   
                        }
    
                    } else {
    
                        if (isset($response['name'])) {
                            if ($response['name'] == 'insufficient_balance') {
                                $message = __('You do not have sufficent balance in your Stable Diffusion account to generate new images');
                            } else {
                                $message =  __('There was an issue generating your AI Image, please try again or contact support team');
                            }
                        } else {
                           $message = __('There was an issue generating your AI Image, please try again or contact support team');
                        }
    
                        $data['status'] = 'error';
                        $data['message'] = $message;
                        return $data;
                    }
    
                }

                if (config('settings.wizard_image_vendor') == 'dall-e-2' || config('settings.wizard_image_vendor') == 'dall-e-3' || config('settings.wizard_image_vendor') == 'dall-e-3-hd') {
                    if (isset($response->data)) {
                        foreach ($response->data as $data) {
                            if (isset($data->url)) {
        
                                $curl = curl_init();
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($curl, CURLOPT_URL, $data->url);
                                $contents = curl_exec($curl);
                                curl_close($curl);
        
        
                                $name = 'wizard-image-' . Str::random(10) . '.png';
        
                                if (config('settings.default_storage') == 'local') {
                                    Storage::disk('public')->put('images/' . $name, $contents);
                                    $image_url = 'images/' . $name;
                                    $storage = 'local';
                                } elseif (config('settings.default_storage') == 'aws') {
                                    Storage::disk('s3')->put('images/' . $name, $contents, 'public');
                                    $image_url = Storage::disk('s3')->url('images/' . $name);
                                    $storage = 'aws';
                                } elseif (config('settings.default_storage') == 'r2') {
                                    Storage::disk('r2')->put('images/' . $name, $contents, 'public');
                                    $image_url = Storage::disk('r2')->url('images/' . $name);
                                    $storage = 'r2';
                                } elseif (config('settings.default_storage') == 'wasabi') {
                                    Storage::disk('wasabi')->put('images/' . $name, $contents);
                                    $image_url = Storage::disk('wasabi')->url('images/' . $name);
                                    $storage = 'wasabi';
                                } elseif (config('settings.default_storage') == 'gcp') {
                                    Storage::disk('gcs')->put('images/' . $name, $image);
                                    Storage::disk('gcs')->setVisibility('images/' . $name, 'public');
                                    $image_url = Storage::disk('gcs')->url('images/' . $name);
                                    $storage = 'gcp';
                                } elseif (config('settings.default_storage') == 'storj') {
                                    Storage::disk('storj')->put('images/' . $name, $image, 'public');
                                    Storage::disk('storj')->setVisibility('images/' . $name, 'public');
                                    $image_url = Storage::disk('storj')->temporaryUrl('images/' . $name, now()->addHours(167));
                                    $storage = 'storj';                        
                                } elseif (config('settings.default_storage') == 'dropbox') {
                                    Storage::disk('dropbox')->put('images/' . $name, $image);
                                    $image_url = Storage::disk('dropbox')->url('images/' . $name);
                                    $storage = 'dropbox';
                                }   
        
                            } else {
                                $data['status'] = 'error';
                                $data['message'] = __('There was an issue with image generation.');
                                return $data; 
                            }                    
                        }
                    } else {
                        $data['status'] = 'error';
                        $data['message'] = __('There was an issue with image generation.');
                        return $data;
                    }
                }
                

            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an issue with image generation. ') . $e->getMessage();
                return $data; 
            }

            # Update image credit balance
            $this->updateBalance(1, config('settings.wizard_image_vendor'));

            $flag = Language::where('language_code', $request->language)->first();

            $wizard = ArticleWizard::where('id', $request->wizard)->first();
            $wizard->image_description = $request->image_description;
            $wizard->selected_title = $request->title;
            $wizard->selected_keywords = $request->keywords;
            $wizard->selected_outline = $request->final_outlines;
            $wizard->selected_talking_points = $request->final_talking_points;
            $wizard->language = $flag->language;          
            $wizard->tone = $request->tone;          
            $wizard->creativity = (float)$request->creativity;          
            $wizard->view_point = $request->view_point;  
            $wizard->max_words = $request->words;  
            $wizard->current_step = 4;
            $wizard->save();

            $url = ($storage == 'local') ? URL::asset($image_url) : $image_url;
            return response()->json(['result' => $url]);
           
        }
	}


    /**
	*
	* Prepare article generation
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function prepare(Request $request) 
    {
        if ($request->ajax()) {
            $prompt = '';
            $max_tokens = 50;

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }

            $flag = Language::where('language_code', $request->language)->first();

            $wizard = ArticleWizard::where('id', $request->wizard)->first();
            $wizard->selected_title = $request->title;
            $wizard->selected_keywords = $request->keywords;
            $wizard->selected_outline = $request->final_outlines;
            $wizard->selected_talking_points = $request->final_talking_points;
            $wizard->image = $request->image_url;
            $wizard->language = $flag->language;
            $wizard->tone = $request->tone;
            $wizard->creativity = (float)$request->creativity;
            $wizard->view_point = $request->view_point;
            $wizard->current_step = 5;
            $wizard->save();

            
            $plan_type = (auth()->user()->plan_id) ? 'paid' : 'free';

            $content = new Content();
            $content->user_id = auth()->user()->id;
            $content->input_text = $prompt;
            $content->language = $request->language;
            $content->language_name = $flag->language;
            $content->language_flag = $flag->language_flag;
            $content->template_code = $request->template;
            $content->template_name = 'Article Wizard';
            $content->icon = '<i class="fa-solid fa-sparkles wizard-icon"></i>';
            $content->group = 'wizard';
            $content->tokens = 0;
            $content->image = $request->image_url;
            $content->plan_type = $plan_type;
            $content->model = $request->model;
            $content->save();

            $data['status'] = 'success';       
            $data['content_id'] = $content->id;
            $data['wizard_id'] = $request->wizard;
            return $data;            

        }
	}


    /**
	*
	* Process Wizard
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {
        # Check Openai APIs
        $openai_key = $this->getOpenai();
        if ($openai_key == 'none') {
            $data['status'] = 'error';
            $data['message'] = __('You must include your personal Openai API key in your profile settings first');
            return $data; 
        }

        
        $model = '';
        $max_tokens = '';

        $wizard = $request->wizard;
        $content = $request->content;
        $max_words = $request->max_words;
        $current_content = Content::where('id', $content)->first();
        $model = $current_content->model;


        return response()->stream(function () use($model, $wizard, $content, $openai_key) {

            $text = "";
            $final_text = "";
            $input_tokens = 0;
            $output_tokens = 0;

            $input = ArticleWizard::where('id', $wizard)->first();

            $outlines = json_decode($input->selected_outline);
            $talking_points = json_decode($input->selected_talking_points);
            $outline_text = '';
            foreach ($outlines as $key => $value) {
                $outline_text .= '[ Outline: ' . $value . ' ( Talking points: ';
                foreach ($talking_points as $index => $point) {
                    if ($index == $key) {
                        $points = implode(',', $point);
                        $outline_text .= $points . ' )], ';
                    }
                }

            }

            try {
                
                if (!is_null($input->max_words)) {
                    $prompt = "Write full article about: $input->selected_title (Must not contain title). Total length of the article must be $input->max_words words. Tone of the article must be: $input->tone. This is the outlines list: $outline_text. Expand each outline section to generate article, use its list of talking points in the outline section. Generate article in the exact same language as the outline and talking points. Do not add other outlines or write more than the specified outlines. Provide the outline headings wrapped with ***. Write the article in the view point of $input->view_point person. Each outline talking point must be written with as much words as possible to reach the provided maximum word limit.";                                                    
                } else {
                    $prompt = "Write full article about: $input->selected_title (Must not contain title). Tone of the article must be: $input->tone. This is the outlines list: $outline_text. Expand each outline section to generate article, use its list of talking points in the outline section. Generate article in the exact same language as the outline and talking points. Do not add other outlines or write more than the specified outlines. Provide the outline headings wrapped with ***. Write the article in the view point of $input->view_point person. Each outline talking point must be written with as much words as possible to reach the provided maximum word limit.";                          
                }
                


                $message = [[
                    'role' => 'user',
                    'content' => $prompt,
                ]];

                $openai_client = \OpenAI::client($openai_key);
                
                if (in_array($model, ['o1', 'o1-mini', 'o3-mini'])) {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                } else {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => $message,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$input->creativity,                        
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                }
                

                foreach ($stream as $result) {

                    if (isset($result->choices[0]->delta->content)) {
                        $raw = $result->choices[0]->delta->content;
                        $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                        $text .= $raw;
                        $final_text .= $clean;
    
                        if (connection_aborted()) {
                            break;
                        }
    
                        echo 'data: ' . $clean;
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }
    
                    if($result->usage !== null){
                        $input_tokens = $result->usage->promptTokens;
                        $output_tokens = $result->usage->completionTokens; 
                    }
                }

                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (Exception $e) {
                Log::error('OpenAI API Error: ' . $e->getMessage());
                echo 'data: OpenAI Notification: <span class="font-weight-bold">' . $e->getMessage() . '</span>. Please contact support team.';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }


            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);              

                $content = Content::where('id', $content)->first();
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->input_text = $prompt;
                $content->result_text = $final_text;
                $content->title = $input->selected_title;
                $content->workbook = $input->workbook;
                $content->save();       
            }     
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);

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
    public function updateBalance($images, $model) {

        switch ($model) {
            case 'dall-e-2':
    
                HelperService::updateMediaBalance('openai_dalle_2', $images);
                break;
            case 'dall-e-3':
                HelperService::updateMediaBalance('openai_dalle_3', $images);
                break;
            case 'dall-e-3-hd':
                HelperService::updateMediaBalance('openai_dalle_3_hd', $images);
                break;
            case 'stable-diffusion-v1-6':
                HelperService::updateMediaBalance('sd_v16', $images);
                break;
            case 'stable-diffusion-xl-1024-v1-0':
                HelperService::updateMediaBalance('sd_xl_v10', $images);
                break;
            case 'sd3.5-medium':
                HelperService::updateMediaBalance('sd_3_medium', $images);
                break;
            case 'sd3.5-large':
                HelperService::updateMediaBalance('sd_3_large', $images);
                break;
            case 'sd3.5-large-turbo':
                HelperService::updateMediaBalance('sd_3_large_turbo', $images);
                break;
            case 'core':
                HelperService::updateMediaBalance('sd_core', $images);
                break;
            case 'ultra':
                HelperService::updateMediaBalance('sd_ultra', $images);
                break;
            case 'flux/dev':
                HelperService::updateMediaBalance('flux_dev', $images);
                break;
            case 'flux/schnell':
                HelperService::updateMediaBalance('flux_schnell', $images);
                break;
            case 'flux-pro/new':
                HelperService::updateMediaBalance('flux_pro', $images);
                break;
            case 'flux-realism':
                HelperService::updateMediaBalance('flux_realism', $images);
                break;
            case 'midjourney/fast':
                HelperService::updateMediaBalance('midjourney_fast', $images);
                break;
            case 'midjourney/relax':
                HelperService::updateMediaBalance('midjourney_relax', $images);
                break;
            case 'midjourney/turbo':
                HelperService::updateMediaBalance('midjourney_turbo', $images);
                break;
            case 'clipdrop':
                HelperService::updateMediaBalance('clipdrop', $images);
                break;
        }
        
    }


    /**
	*
	* Save changes
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function save(Request $request) 
    {
        if ($request->ajax()) {

            $uploading = new UserService();
            $upload = $uploading->upload();
            if (!$upload['status']) return;    

            $document = Content::where('id', request('id'))->first(); 

            if ($document->user_id == Auth::user()->id){

                $document->result_text = $request->text;
                $document->title = $request->title;
                $document->workbook = $request->workbook;
                $document->save();

                $data['status'] = 'success';
                return $data;  
    
            } else{

                $data['status'] = 'error';
                return $data;
            }  
        }
	}


    /**
	*
	* Get openai instance
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function getOpenai() 
    {
         # Check personal API keys
         if (config('settings.personal_openai_api') == 'allow') {
            if (is_null(auth()->user()->personal_openai_key)) {
                return 'none'; 
            } else {
                return auth()->user()->personal_openai_key; 
            } 

        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                if (is_null(auth()->user()->personal_openai_key)) {
                    return 'none'; 
                } else {
                    return auth()->user()->personal_openai_key; 
                }
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                   $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                   array_push($api_keys, config('services.openai.key'));
                   $key = array_rand($api_keys, 1);
                   return $api_keys[$key];
               } else {
                    return config('services.openai.key');
               }
           }

        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                return $api_keys[$key];
            } else {
                return config('services.openai.key');
            }
        }
    }


    public function clear(Request $request)
    {
        ArticleWizard::where('user_id', auth()->user()->id)->delete();
        return response()->json("success");
    }



}
