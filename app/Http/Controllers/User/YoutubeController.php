<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\VoiceToneTrait;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Content;
use App\Models\Workbook;
use App\Models\Language;
use App\Models\ApiKey;
use App\Models\User;
use App\Models\MainSetting;
use App\Models\BrandVoice;
use App\Services\HelperService;
use OpenAI\Client;
use Exception;


class YoutubeController extends Controller
{
    use VoiceToneTrait;
    private Response $response;

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $languages = Language::orderBy('languages.language', 'asc')->get();
        $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();
        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $brand_feature = $plan->brand_voice_feature;
        } else {
            if (config('settings.brand_voice_user_access') == 'allow') {
                $brand_feature = true;
            } else {
                $brand_feature = false;
            }
        }

        $settings = MainSetting::first();
        if (auth()->user()->group == 'user') {
            if ($settings->youtube_feature_free_tier) {
                return view('user.youtube.index', compact('languages', 'workbooks', 'brands', 'brand_feature'));
            } else {
                toastr()->warning(__('AI Youtube feature is not available for free tier users, subscribe to get a proper access'));
                return redirect()->route('user.plans');
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->youtube_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for AI Youtube feature'));
                return redirect()->back();                   
            } else {
                return view('user.youtube.index', compact('languages', 'workbooks', 'brands', 'brand_feature'));
            }
        } else {
            return view('user.youtube.index', compact('languages', 'workbooks', 'brands', 'brand_feature'));
        }
    }


     /**
	*
	* Process Davinci
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function generate(Request $request) 
    {

        if ($request->ajax()) {
            $prompt = '';
            $max_tokens = '';
            $counter = 1;
            $input_title = '';
            $input_keywords = '';
            $input_description = '';

            # Check personal API keys
            if (config('settings.personal_openai_api') == 'allow') {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                    return $data;
                }     
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    if (is_null(auth()->user()->personal_openai_key)) {
                        $data['status'] = 'error';
                        $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                        return $data;
                    } 
                }    
            } 

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, 100);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }

            try {
                $video_id = $this->parseURL($request->url);
                $youtube = $this->youtube($video_id);
            } catch (Exception $e) {
                $data['status'] = 'error';
                $data['message'] = $e->getMessage();
                return $data;
            }
            
            $prompt = '';

            switch ($request->action) {
                case 'post': $prompt = 'Create full blog post about this youtube video details: ' . $youtube. '.'; break;
                case 'outline': $prompt = 'Create detailed outline for this youtube video details: ' . $youtube. '.'; break;
                case 'explain': $prompt = 'Write a detailed explanation about this youtube video details: ' . $youtube. '.'; break;
                case 'description': $prompt = 'Create a detailed meaningful descriptoin for this youtube video details: ' . $youtube. '.'; break;
                case 'summarize': $prompt = 'Create a summarize for this youtube video details: ' . $youtube. '.'; break;
                case 'compare': $prompt = 'Write a detailed pros and cons this youtube video details: ' . $youtube. '.'; break;
            }
            
            $flag = Language::where('language_code', $request->language)->first();

            $prompt .= "Provide response in " . $flag->language . '.';

            if (isset($request->tone)) {
                $prompt = $prompt . ' \n\n Voice of tone of the text must be ' . $request->tone . '.';
            }     
            
            if (isset($request->view_point)) {
                if ($request->view_point != 'none')
                    $prompt = $prompt . ' \n\n The point of view must be in ' . $request->view_point . ' person. \n\n';
            }

            $plan_type = (auth()->user()->plan_id) ? 'paid' : 'free';
            
            $content = new Content();
            $content->user_id = auth()->user()->id;
            $content->input_text = $prompt;
            $content->language = $request->language;
            $content->language_name = $flag->language;
            $content->language_flag = $flag->language_flag;
            $content->template_code = $request->code;
            $content->template_name = 'AI Youtube';
            $content->icon = '<i class="fa-brands fa-youtube ad-icon"></i>';
            $content->group = 'youtube';
            $content->tokens = 0;
            $content->plan_type = $plan_type;
            $content->model = $request->model;
            $content->save();

            $data['status'] = 'success';     
            $data['temperature'] = $request->creativity;     
            $data['id'] = $content->id;
            return $data;            

        }
	}


     /**
	*
	* Process Davinci
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {
        if (config('settings.personal_openai_api') == 'allow') {
            $openai_api = auth()->user()->personal_openai_key;        
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                $openai_api = auth()->user()->personal_openai_key;               
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                   $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                   array_push($api_keys, config('services.openai.key'));
                   $key = array_rand($api_keys, 1);
                   $openai_api = $api_keys[$key];
               } else {
                   $openai_api = config('services.openai.key');
               }
           }               
        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                $openai_api = $api_keys[$key];
            } else {
                $openai_api = config('services.openai.key');
            }
        }
        

        $content_id = $request->content_id;
        $temperature = $request->temperature;
        

        return response()->stream(function () use($content_id, $temperature, $openai_api) {

            $content = Content::where('id', $content_id)->first();
            $prompt = $content->input_text; 
            $model = $content->model; 
            $text = "";

            try {

                $openai_client = \OpenAI::client($openai_api);
                
                if (in_array($model, ['o1', 'o1-mini', 'o3-mini'])) {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                } else {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$temperature,                        
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

                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();

            }            
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',            
        ]);

	}


    public function custom(Request $request)
    {
        # Check API keys
        if (config('settings.personal_openai_api') == 'allow') {
            if (is_null(auth()->user()->personal_openai_key)) {
                return response()->json(["status" => "error", 'message' => __('You must include your personal Openai API key in your profile settings first')]);
            } else {
                config(['openai.api_key' => auth()->user()->personal_openai_key]); 
            } 
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                if (is_null(auth()->user()->personal_openai_key)) {
                    return response()->json(["status" => "error", 'message' => __('You must include your personal Openai API key in your profile settings first')]);
                } else {
                    config(['openai.api_key' => auth()->user()->personal_openai_key]); 
                }
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                   $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                   array_push($api_keys, config('services.openai.key'));
                   $key = array_rand($api_keys, 1);
                   config(['openai.api_key' => $api_keys[$key]]);
               } else {
                    config(['openai.api_key' => config('services.openai.key')]);
               }
           }
        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                config(['openai.api_key' => $api_keys[$key]]);
            } else {
                config(['openai.api_key' => config('services.openai.key')]);
            }
        }


        # Verify if user has enough credits
        $model = 'gpt-3.5-turbo-0125';

        # Verify if user has enough credits
        $verify = HelperService::creditCheck($model, 100);
        if (isset($verify['status'])) {
            if ($verify['status'] == 'error') {
                return response()->json(["status" => "error", 'message' => __('Not enough word balance to proceed, subscribe or top up your word balance and try again')]);
            }
        }

        if ($request->content == null || $request->content == "") {
            return response()->json(["status" => "success", "message" => ""]);
        }

        $completion = OpenAI::chat()->create([
            'model' => "gpt-3.5-turbo",
            'temperature' => 0.9,
            'messages' => [[
                'role' => 'user',
                'content' => "$request->prompt:\n\n$request->content"
            ]]
        ]);


        $words = count(explode(' ', ($completion->choices[0]->message->content)));
        $this->updateBalance($words); 

        return response()->json(["status" => "success", "message" => $completion->choices[0]->message->content]);
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


    private function getVideoDetails($videoId) 
    {
        try {
            
            $settings = MainSetting::first();
            $apiKey = $settings->youtube_api;
            Log::info($apiKey);
            // Get both snippet and contentDetails in a single request
            $response = Http::asJson()
                ->get('https://youtube.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet,contentDetails,statistics',
                    'id' => $videoId,
                    'key' => $apiKey,
                ]);

            if ($response->failed()) {
                throw new Exception('Failed to fetch video details: ' . $response->status());
            }

            $data = $response->json();
            
            if (empty($data['items'])) {
                throw new Exception('Video not found');
            }

            $videoData = $data['items'][0];
            
            // Structure the video information
            return [
                'title' => $videoData['snippet']['title'] ?? '',
                'description' => $videoData['snippet']['description'] ?? '',
                'publishedAt' => $videoData['snippet']['publishedAt'] ?? '',
                'channelTitle' => $videoData['snippet']['channelTitle'] ?? '',
                'duration' => $videoData['contentDetails']['duration'] ?? '',
                'viewCount' => $videoData['statistics']['viewCount'] ?? 0,
                'likeCount' => $videoData['statistics']['likeCount'] ?? 0,
                'tags' => $videoData['snippet']['tags'] ?? [],
            ];

        } catch (Exception $e) {
            throw new Exception('YouTube API Error: ' . $e->getMessage());
        }
    }


    private function parseURL($url)
    {
        try {
            // Handle different YouTube URL formats
            preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            
            if (!isset($matches[1])) {
                throw new Exception('Invalid YouTube URL format');
            }
            
            return $matches[1];
        } catch (Exception $e) {
            throw new Exception('URL parsing error: ' . $e->getMessage());
        }
    }

    private function youtube($id)
    {
        try {
            $videoDetails = $this->getVideoDetails($id);
            
            // Format the response for AI processing
            $result = sprintf(
                "Title: %s\nChannel: %s\nPublished: %s\nViews: %s\nDescription: %s\nTags: %s",
                $videoDetails['title'],
                $videoDetails['channelTitle'],
                $videoDetails['publishedAt'],
                number_format($videoDetails['viewCount']),
                $videoDetails['description'],
                implode(', ', $videoDetails['tags'])
            );
    
            return $result;
    
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('YouTube API Error: ' . $e->getMessage());
            
            throw new Exception('Failed to fetch video details: ' . $e->getMessage());
        }
        
    }

}
