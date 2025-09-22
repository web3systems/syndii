<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpClient\Chunk\ServerSentEvent;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\FavoriteChat;
use App\Models\ChatConversation;
use App\Models\ChatCategory;
use App\Models\ChatHistory;
use App\Models\ChatPrompt;
use App\Models\ApiKey;
use App\Models\CustomChat;
use App\Models\Chat;
use App\Models\ChatShare;
use App\Models\User;
use App\Models\BrandVoice;
use App\Models\ExtensionSetting;
use App\Models\MainSetting;
use App\Models\FineTuneModel;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\HelperService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use OpenAI\Client;
use Exception;



class ChatController extends Controller
{

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        $favorite_chats = Chat::select('chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'chats.chat_code')->where('status', true)->orderBy('category', 'asc')->get();    
        $user_chats = FavoriteChat::where('user_id', auth()->user()->id)->pluck('chat_code');     
        $other_chats = Chat::whereNotIn('chat_code', $user_chats)->where('status', true)->orderBy('category', 'asc')->get();  
        $original_chat_categories = Chat::where('status', true)->groupBy('group')->pluck('group'); 
        $custom_chat_categories = CustomChat::where('status', true)->groupBy('group')->pluck('group'); 
        $all_categories = $original_chat_categories->merge($custom_chat_categories); 
        $categories = ChatCategory::whereIn('code', $all_categories)->orderBy('name', 'asc')->get();  
        
        $favorite_chats_custom = CustomChat::select('custom_chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'custom_chats.chat_code')->where('status', true)->orderBy('category', 'asc')->get();    
        $custom_chats = CustomChat::whereNotIn('chat_code', $user_chats)->where('user_id', auth()->user()->id)->where('type', 'private')->where('status', true)->orderBy('group', 'asc')->get();  
        $public_custom_chats = CustomChat::whereNotIn('chat_code', $user_chats)->where('type', 'custom')->where('status', true)->orderBy('group', 'asc')->get();  
        
        if (!is_null(auth()->user()->plan_id)) {
            $subscription = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $check = $subscription->personal_chats_feature;
        } else {
            $check = false;
        }
        
        return view('user.chat.index', compact('favorite_chats', 'other_chats', 'categories', 'custom_chats', 'check', 'public_custom_chats', 'favorite_chats_custom'));
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
        # Check if user has access to the chat bot
        $template = Chat::where('chat_code', $request->chat_code)->first();
        if (auth()->user()->group == 'user') {
            if (config('settings.chat_feature_user') == 'allow') {
                if (config('settings.chats_access_user') != 'all' && config('settings.chats_access_user') != 'premium') {
                    if (is_null(auth()->user()->member_of)) {
                        if ($template->category == 'professional' && config('settings.chats_access_user') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if($template->category == 'premium' && (config('settings.chats_access_user') != 'premium' && config('settings.chats_access_user') != 'all')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if(($template->category == 'standard' || $template->category == 'all') && (config('settings.chats_access_user') != 'professional' && config('settings.chats_access_user') != 'standard')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;
                        }

                    } else {
                        $user = User::where('id', auth()->user()->member_of)->first();
                        $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                        if ($plan->chats != 'all' && $plan->chats != 'premium') {          
                            if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            } else if($template->category == 'professional' && $plan->chats != 'professional') {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            }                  
                        }
                    }
                    
                }                
            } else {
                if (is_null(auth()->user()->member_of)) {
                    $status = 'error';
                    $message = __('Ai chat assistant feature is not available for free tier users, subscribe to get a proper access');
                    return response()->json(['status' => $status, 'message' => $message]);
                } else {
                    $user = User::where('id', auth()->user()->member_of)->first();
                    $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                    if ($plan->chats != 'all' && $plan->chats != 'premium') {          
                        if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        } else if($template->category == 'professional' && $plan->chats != 'professional') {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        }                  
                    }
                }                      
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->chats != 'all' && $plan->chats != 'premium') {     
                if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } else if($template->category == 'professional' && $plan->chats != 'professional') {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                }                   
            }
        }


        # Check personal API keys
        if (config('settings.personal_openai_api') == 'allow') {
            if (is_null(auth()->user()->personal_openai_key)) {
                $status = 'error';
                $message =  __('You must include your personal Openai API key in your profile settings first');
                return response()->json(['status' => $status, 'message' => $message]); 
            }     
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $status = 'error';
                    $message =  __('You must include your personal Openai API key in your profile settings first');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } 
            }    
        } 


        # Check if user has sufficient words available to proceed
        $verify = HelperService::creditCheck($request->model, 200);
        if (isset($verify['status'])) {
            if ($verify['status'] == 'error') {
                return response()->json(['status' => $verify['status'], 'message' => $verify['message']]);
            }
        }

        $chat = new ChatHistory();
        $chat->user_id = auth()->user()->id;
        $chat->conversation_id = $request->conversation_id;
        $chat->prompt = $request->input('message');
        $chat->images = $request->image;
        $chat->model = $request->model;
        $chat->save();

        session()->put('conversation_id', $request->conversation_id);
        session()->put('chat_id', $chat->id);
        session()->put('realtime', $request->realtime);
        session()->put('message', $request->input('message'));
        session()->put('company', $request->company);
        session()->put('service', $request->service);
        session()->put('model', $request->model);


        //return response()->json(['status' => 'success', 'old'=> $balance, 'current' => ($balance - $words), 'chat_id' => $chat->id]);
        //return response()->json(['status' => 'success', 'old'=> 0, 'current' => 0, 'chat_id' => $chat->id]);
        return response()->json(['status' => 'success', 'chat_id' => $chat->id]);
        
	}


     /**
	*
	* Process Chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function generateChat(Request $request) 
    {  
        # Get Settings
        $settings = MainSetting::first();
        $extension = ExtensionSetting::first();

        $conversation_id = $request->conversation_id;

        $prompt= session()->get('message'); 
        $realtime = session()->get('realtime'); 
        $company = session()->get('company');
        $service = session()->get('service');
        $chat_id = session()->get('chat_id');
        $model = session()->get('model');
        

        # Append real time data
        if($realtime == 'on') {
            if ($settings->realtime_data_engine == 'serper') {
                $prompt = $this->realtimeData($prompt, 'serper');
            } elseif ($settings->realtime_data_engine == 'perplexity') {
                $prompt = $this->realtimeData($prompt, 'perplexity');
            } 
        } 
        

        # Add Brand information
        if ($company != 'none') {
            $brand = BrandVoice::where('id', $company)->first();

            if ($brand) {
                $product = '';
                if ($service != 'none') {
                    foreach($brand->products as $key => $value) {
                        if ($key == $request->service)
                            $product = $value;
                    }
                } 
                
                if ($service != 'none') {
                    $prompt .= ".\n Focus on my company and {$product['type']}'s information: \n";
                } else {
                    $prompt .= ".\n Focus on my company's information: \n";
                }
                
                if ($brand->name) {
                    $prompt .= "The company's name is {$brand->name}. ";
                }

                if ($brand->description) {
                    $prompt .= "The company's description is {$brand->description}. ";
                }

                if ($brand->website) {
                    $prompt .= ". The company's website is {$brand->website}. ";
                }

                if ($brand->tagline) {
                    $prompt .= "The company's tagline is {$brand->tagline}. ";
                }

                if ($brand->audience) {
                    $prompt .= "The company's target audience is: {$brand->audience}. ";
                }

                if ($brand->industry) {
                    $prompt .= "The company focuses in: {$brand->industry}. ";
                }

                if ($product) {
                    if ($product['name']) {
                        $prompt .= "The {$product['type']}'s name is {$product['name']}. \n";
                    }

                    if ($product['description']) {
                        $prompt .= "The {$product['type']} is about {$product['description']}. ";
                    }                        
                }
            }
        }


        # Start OpenAI task
        if (in_array($model, ['gpt-3.5-turbo-0125', 'gpt-4', 'gpt-4o', 'gpt-4o-mini', 'gpt-4.5-preview', 'o1', 'o1-mini', 'o3-mini', 'gpt-4-0125-preview', 'gpt-4o-search-preview', 'gpt-4o-mini-search-preview', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o4-mini', 'o3'])) {
            if (\App\Services\HelperService::extensionAzureOpenai() && $extension->azure_openai_activate) {
    
                return $this->streamAzure($conversation_id, $chat_id, $prompt);                      

            } elseif (\App\Services\HelperService::extensionOpenRouter() && $extension->open_router_activate) {

                return $this->streamOpenRouter($conversation_id, $chat_id, $prompt);                      
            
            } else {

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
    
                if (is_null($openai_api) || $openai_api == '') {
                    return response()->stream(function () {
                        echo 'data: OpenAI Notification: <span class="font-weight-bold">Missing OpenAI API key</span>. Please contact support team.';
                        echo "\n\n";
                        echo 'data: [DONE]';
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }, 200, [
                        'Cache-Control' => 'no-cache',
                        'X-Accel-Buffering' => 'no',
                        'Content-Type' => 'text/event-stream',
                    ]);
                }
    
                return $this->streamOpenai($conversation_id, $chat_id, $prompt, $openai_api);
            }
            
        }


        # Start Anthropic task
        if (in_array($model, ['claude-3-7-sonnet-20250219', 'claude-3-opus-20240229', 'claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022', 'claude-opus-4-20250514', 'claude-sonnet-4-20250514'])) {
            if (config('settings.personal_claude_api') == 'allow') {
                $anthropic_api = auth()->user()->personal_claude_key;        
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_claude_api) {
                    $anthropic_api = auth()->user()->personal_claude_key;               
                } else {
                    $anthropic_api = config('anthropic.api_key');                           
               }                       
            } else {
                $anthropic_api = config('anthropic.api_key'); 
            }

            if (is_null($anthropic_api) || $anthropic_api == '') {
                return response()->stream(function () {
                    echo 'data: Anthropic Notification: <span class="font-weight-bold">Missing Anthropic API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamClaude($conversation_id, $chat_id, $prompt, $anthropic_api);
        }


        # Start Gemini task         
        if ($model == 'gemini-1.5-pro' || $model == 'gemini-1.5-flash' || $model == 'gemini-2.0-flash') {
            if (config('settings.personal_gemini_api') == 'allow') {
                $gemini_api = auth()->user()->personal_gemini_key;        
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_gemini_api) {
                    $gemini_api = auth()->user()->personal_gemini_key;               
                } else {
                    $gemini_api = config('gemini.api_key');                           
               }                       
            } else {
                $gemini_api = config('gemini.api_key'); 
            }

            if (is_null($gemini_api) || $gemini_api == '') {
                return response()->stream(function () {
                    echo 'data: Gemini Notification: <span class="font-weight-bold">Missing Gemini API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamGemini($conversation_id, $chat_id, $prompt, $gemini_api);
        }


        # Start xAI task
        if ($model == 'grok-2-1212' || $model == 'grok-2-vision-1212') {
            if (is_null($settings->xai_api) || $settings->xai_api == '') {
                return response()->stream(function () {
                    echo 'data: xAI Notification: <span class="font-weight-bold">Missing xAI API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamxAI($conversation_id, $chat_id, $prompt, $settings->xai_api, $settings->xai_base_url);
        }


        # Start DeepSeek task
        if ($model == 'deepseek-chat' || $model == 'deepseek-reasoner') {
            if (is_null($settings->deepseek_api) || $settings->deepseek_api == '') {
                return response()->stream(function () {
                    echo 'data: Deepseek Notification: <span class="font-weight-bold">Missing DeepSeek API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamDeepSeek($conversation_id, $chat_id, $prompt, $settings->deepseek_api, $settings->deepseek_base_url);
        }


        # Start Nova task         
        if ($model == 'us.amazon.nova-micro-v1:0' || $model == 'us.amazon.nova-lite-v1:0' || $model == 'us.amazon.nova-pro-v1:0') {    

            if (is_null($extension->amazon_bedrock_access_key) || $extension->amazon_bedrock_access_key == '') {
                return response()->stream(function () {
                    echo 'data: Amazon Nova Notification: <span class="font-weight-bold">Missing AWS Access keys</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamBedrock($conversation_id, $chat_id, $prompt);
        }


        # Start Perplexity task
        if ($model == 'sonar' || $model == 'sonar-pro' || $model == 'sonar-reasoning' || $model == 'sonar-reasoning-pro') {
            if (is_null($extension->perplexity_api) || $extension->perplexity_api == '') {
                return response()->stream(function () {
                    echo 'data: Perplexity Notification: <span class="font-weight-bold">Missing Perplexity API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamPerplexity($conversation_id, $chat_id, $prompt, $extension->perplexity_api);
        }


        // return response()->stream(function () use($conversation_id, $prompt) {

            
    

        //         $guzzle_client = new GuzzleClient();
        //         $url = 'https://api.openai.com/v1/chat/completions';
                
        //         $model = 'gpt-4o-mini';                
                
        //         $response = $guzzle_client->post($url,
        //         [
        //             'headers' => [
        //                 'Authorization' => 'Bearer ' . config('services.openai.key'),
        //             ],
        //             'json' => [
        //                 'model' => $model,
        //                 'messages' => [
        //                     [
        //                     'role' => 'user',
        //                     'content' => [
        //                                 [
        //                                     'type' => 'text',
        //                                     'text' => $chat_message->prompt,
        //                                 ],
        //                                 [
        //                                 'type' => 'image_url',
        //                                 'image_url' => [
        //                                     'url' => $chat_message->images,
        //                                     ],
        //                                 ],
        //                         ],
        //                     ],
        //                 ],
        //                 'max_tokens' => 2500,
        //                 'stream' => true,
                        
        //             ]
        //         ]);     

        //         foreach (explode("\n", $response->getBody()->getContents()) as $data) { 
        //             if ($data != 'data: [DONE]') {
        //                 $array = explode('data: ', $data);
        //             } else {
        //                 echo "data: [DONE]";
        //             }
                    
        //             foreach ($array as $response){
        //                 $response = json_decode($response, true);
        //                 if ($data != "data: [DONE]\n\n" and isset($response["choices"][0]["delta"]["content"])) {
        //                     $text .= $response["choices"][0]["delta"]["content"];
        //                     $raw = $response['choices'][0]['delta']['content'];
        //                     $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
        //                     echo "data: " . $clean;
        //                 }
        //             }
                
        //             echo PHP_EOL;
        //             ob_flush();
        //             flush();
                    
        //         }
            



        // }, 200, [
        //     'Cache-Control' => 'no-cache',
        //     'X-Accel-Buffering' => 'no',
        //     'Content-Type' => 'text/event-stream',
        // ]);
        
    }


    /**
	*
	* Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamOpenai($conversation_id, $chat_id, $prompt, $openai_api, $user_id = null)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $openai_api, $user_id) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;
            $input_tokens = 0;
            $output_tokens = 0;

            # Prepare chat history
            if (strpos($model, 'o1-') === 0) {
                $first_message = $main_prompt . "\n\nUser: " . $prompt;
                $messages[] = ['role' => 'user', 'content' => $first_message];

            } else {

                $messages[] = ['role' => 'system', 'content' => $main_prompt];

                foreach ($chat_messages as $chat) {
                    if (empty($chat['response']) || is_null($chat['response'])) {
                        continue;
                    }

                    $messages[] = ['role' => 'user', 'content' => $chat['prompt']];
                    if (!empty($chat['response'])) {
                        $messages[] = ['role' => 'assistant', 'content' => $chat['response']];
                    }
                }                

                $messages[] = ['role' => 'user', 'content' => $prompt];   
            }                       
 

            try {

                $openai_client = \OpenAI::client($openai_api);
                    
                $stream = $openai_client->chat()->createStreamed([
                    'model' => $model,
                    'messages' => $messages,
                    'stream_options'=>[
                        'include_usage' => true,
                    ]
                ]);

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
    
                    if(isset($result->usage)){
                        $input_tokens = $result->usage->promptTokens;
                        $output_tokens = $result->usage->completionTokens; 
                    }
                }
                echo "event: stop\n";
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
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens, $user_id);   

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $input_tokens;
                $current_chat->output_tokens = $output_tokens;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
     *
     * Anthropic Claude stream task
     * @param - conversation_id, chat_id, prompt, anthropic_api_key
     * @return - text stream
     *
     */
    private function streamClaude($conversation_id, $chat_id, $prompt, $anthropic_api)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $anthropic_api) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;
            $input_tokens = 0;
            $output_tokens = 0;

            // Format messages for Anthropic API
            $messages = [];
            
            // Add system prompt
            $system_prompt = $main_prompt;
            
            // Add conversation history
            foreach ($chat_messages as $chat) {
                if (empty($chat['response']) || is_null($chat['response'])) {
                    continue;
                }

                $messages[] = ['role' => 'user', 'content' => $chat['prompt']];
                if (!empty($chat['response'])) {
                    $messages[] = ['role' => 'assistant', 'content' => $chat['response']];
                }
            }

            // Add current prompt
            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {
                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $anthropic_api,
                    'anthropic-version' => '2023-06-01'
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'system' => $system_prompt,
                    'temperature' => 1,
                    'stream' => true,
                    'max_tokens' => 4096
                ];
                
                $response = $client->post('https://api.anthropic.com/v1/messages', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Anthropic API Error: HTTP Status $statusCode";
                    
                    $errorType = isset($errorData['error']['type']) 
                        ? $errorData['error']['type'] 
                        : "unknown_error";
                    
                    Log::error("Anthropic API Error ($errorType): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line) || $line === 'data: ') {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "event: done\n";
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
                            $data = json_decode($json, true);

                            if (isset($data['error'])) {
                                $errorMessage = $data['error']['message'] ?? 'Unknown error';
                                $errorType = $data['error']['type'] ?? 'unknown_error';
                                
                                Log::error("Anthropic API Stream Error ($errorType): $errorMessage", [
                                    'error_data' => $data['error']
                                ]);
                                
                                echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                echo "\n\n";
                                flush();
                                continue;
                            }

                            // Handle content delta for streaming
                            if (isset($data['type']) && $data['type'] === 'content_block_delta') {
                                if (isset($data['delta']['text'])) {
                                    $raw = $data['delta']['text'];
                                    $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                    $text .= $raw;
                                
                                    echo 'data: ' . $clean;
                                    echo "\n\n";
                                    ob_flush();
                                    flush();
                                }
                            }
                            
                            // Handle message stop event                          
                            if (isset($data['message']['usage'])) {                              
                                // Check if usage data exists in the expected structure
                                if (isset($data['message']['usage']['input_tokens'])) {
                                    $input_tokens = $data['message']['usage']['input_tokens'];
                                }
                                if (isset($data['message']['usage']['output_tokens'])) {
                                    $output_tokens = $data['message']['usage']['output_tokens'];
                                }
                            }

                            if (isset($data['usage'])) {                              
                                if (isset($data['usage']['output_tokens'])) {
                                    $output_tokens = $data['usage']['output_tokens'];
                                }
                            }
                            
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
              
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Anthropic API Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Anthropic service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
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

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $input_tokens;
                $current_chat->output_tokens = $output_tokens;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Deepseek stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamDeepSeek($conversation_id, $chat_id, $prompt, $deepseek_api, $deepseek_base_url)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $deepseek_api, $deepseek_base_url) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;
            $input_tokens = 0;
            $output_tokens = 0;

            $messages[] = ['content' => $main_prompt, 'role' => 'system'];
            
            if ($model !== 'deepseek-reasoner') {
                foreach ($chat_messages as $chat) {
                    if (empty($chat['response']) || is_null($chat['response'])) {
                        continue;
                    }
    
                    $messages[] = ['content' => $chat['prompt'], 'role' => 'user'];
                    if (!empty($chat['response']) || !is_null($chat['response'])) {
                        $messages[] = ['content' => $chat['response'], 'role' => 'system'];
                    }
                } 
            }                           

            $messages[] = ['content' => $prompt, 'role' => 'user']; 

            try {

                $deepseek_client = \OpenAI::factory()
                                    ->withApiKey($deepseek_api)
                                    ->withBaseUri($deepseek_base_url) 
                                    ->withHttpClient($httpClient = new \GuzzleHttp\Client([])) 
                                    ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $httpClient->send($request, [
                                        'stream' => true
                                    ]))
                                    ->make();
                    
                $stream = $deepseek_client->chat()->createStreamed([
                    'model' => $model,
                    'messages' => $messages,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                    'temperature' => 1,
                    'stream_options'=>[
                        'include_usage' => true,
                    ]
                ]);

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
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (Exception $e) {
                Log::error('Deepseek API Error: ' . $e->getMessage());
                echo 'data: Deepseek Notification: <span class="font-weight-bold">' . $e->getMessage() . '</span>. Please contact support team.';
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

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $input_tokens;
                $current_chat->output_tokens = $output_tokens;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* xAI stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamxAI($conversation_id, $chat_id, $prompt, $xai_api, $xai_base_url)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $xai_api, $xai_base_url) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;

            $messages[] = ['role' => 'system', 'content' => $main_prompt];

            foreach ($chat_messages as $chat) {
                if (empty($chat['response']) || is_null($chat['response'])) {
                    continue;
                }

                $messages[] = ['role' => 'user', 'content' => $chat['prompt']];
                if (!empty($chat['response'])) {
                    $messages[] = ['role' => 'system', 'content' => $chat['response']];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {

                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $xai_api
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 1,
                    'stream' => true
                ];
                
                $response = $client->post($xai_base_url . '/chat/completions', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "xAI API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("xAI API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: xAI API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line)) {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "event: done\n";
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
    
                            if (strpos($json, '{"error":') === 0) {
                                $errorData = json_decode($json, true);
                                if (isset($errorData['error'])) {
                                    $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                                    $errorCode = $errorData['error']['code'] ?? 'unknown_error';
                                    
                                    Log::error("xAI API Stream Notification ($errorCode): $errorMessage", [
                                        'error_data' => $errorData
                                    ]);
                                    
                                    echo 'data: xAI API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                    echo "\n\n";
                                    flush();
                                    continue;
                                }
                            }
    
                            $data = json_decode($json, true);
                       
                            if (isset($data['choices'][0]['delta']['content'])) {
                                $raw = $data['choices'][0]['delta']['content'];
                                $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                $text .= $raw;
                               
                                echo 'data: ' . $clean;
                                echo "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
                
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
    
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("xAI Notification ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The xAI service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: xAI Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }
            
            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                $tokens = $this->estimateTokenUsage($messages, $text);
                HelperService::updateBalance($words, $model, $tokens['prompt_tokens'], $tokens['completion_tokens']); 

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $tokens['prompt_tokens'] ?? 0;
                $current_chat->output_tokens = $tokens['completion_tokens'] ?? 0;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Gemini stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamGemini($conversation_id, $chat_id, $prompt, $gemini_api)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $gemini_api) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;
            $input_tokens = 0;
            $output_tokens = 0;

            $modelMap = [
                'gemini-1.5-pro' => 'models/gemini-1.5-pro',
                'gemini-1.5-flash' => 'models/gemini-1.5-flash',
                'gemini-2.0-flash' => 'models/gemini-2.0-flash',
            ];

            $apiModel = $modelMap[$model] ?? 'models/gemini-pro';
            
            $contents = [];
    
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => "System: $main_prompt\n\nUser: $prompt"]
                ]
            ];
            
            if (count($chat_messages) > 0) {
                $history = [];
                foreach ($chat_messages as $chat) {
                    if (empty($chat['response']) || is_null($chat['response'])) {
                        continue;
                    }
                    
                    $history[] = [
                        'role' => 'user',
                        'parts' => [['text' => $chat['prompt']]]
                    ];
                    
                    if (!empty($chat['response'])) {
                        $history[] = [
                            'role' => 'model',
                            'parts' => [['text' => $chat['response']]]
                        ];
                    }
                }
                
                if (count($history) > 0) {
                    $contents = [
                        [
                            'role' => 'user',
                            'parts' => [['text' => "System: $main_prompt"]]
                        ],
                        [
                            'role' => 'model',
                            'parts' => [['text' => "I'll follow these instructions."]]
                        ]
                    ];
                    
                    $contents = array_merge($contents, $history);
                    
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ];
                }
            }

            try {
                $payload = [
                    'contents' => $contents,
                    'generation_config' => [
                        'temperature' => 1.0,
                        'topP' => 0.95,
                        'topK' => 40,
                    ],
                ];

                // Create HTTP client
                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $apiEndpoint = "https://generativelanguage.googleapis.com/v1beta/{$apiModel}:streamGenerateContent?key={$gemini_api}";
          
                $response = $client->post($apiEndpoint, [
                    'json' => $payload,
                    'stream' => true,
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]);
           
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Gemini API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("Gemini API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Gemini API Error: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }

                $stream = $response->getBody()->getContents();
                $stream = json_decode($stream, true);
      
                foreach ($stream as $result) {

                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $raw = $result['candidates'][0]['content']['parts'][0]['text'];

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
    
                    if(isset($result['usageMetadata'])){
                        if (isset($result['usageMetadata']['promptTokenCount'])) {
                            $input_tokens = $result['usageMetadata']['promptTokenCount'];
                        }

                        if (isset($result['usageMetadata']['candidatesTokenCount'])) {
                            $output_tokens = $result['usageMetadata']['candidatesTokenCount']; 
                        } 
                    }
                }
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
                
            } catch (\Exception $e) {
                // Handle exceptions
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Gemini Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Gemini service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'API key not valid') !== false || strpos($errorMessage, 'invalid API key') !== false) {
                    $userMessage = 'Invalid API key. Please check your Gemini API key.';
                } elseif (strpos($errorMessage, 'quota') !== false) {
                    $userMessage = 'API quota exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'PERMISSION_DENIED') !== false) {
                    $userMessage = 'Permission denied. Please check your API key permissions.';
                } elseif (strpos($errorMessage, 'INVALID_ARGUMENT') !== false) {
                    $userMessage = 'Invalid request. The prompt may contain content that violates Gemini\'s content policy.';
                }
                
                echo 'data: Gemini API Error: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }

            // Only update database if we have a response
            if (!empty($text)) {
                
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);  

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $input_tokens;
                $current_chat->output_tokens = $output_tokens;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Perplexity stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamPerplexity($conversation_id, $chat_id, $prompt, $perplexity_api)
    {
        return response()->stream(function () use($conversation_id, $chat_id, $prompt, $perplexity_api) {
            
            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";

            $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
            $chat_messages = ChatHistory::where('conversation_id', $conversation_id)->orderBy('created_at', 'desc')->take(3)->get()->reverse();
            $main_prompt = $main_chat->prompt;
            $model = $chat_message->model;
            $input_tokens = 0;
            $output_tokens = 0;

            $messages[] = ['role' => 'system', 'content' => $main_prompt];

            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {

                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $perplexity_api
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 1,
                    'stream' => true
                ];
                
                $response = $client->post('https://api.perplexity.ai/chat/completions', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Perplexity API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("Perplexity API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line)) {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "event: done\n";
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
    
                            if (strpos($json, '{"error":') === 0) {
                                $errorData = json_decode($json, true);
                                if (isset($errorData['error'])) {
                                    $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                                    $errorCode = $errorData['error']['code'] ?? 'unknown_error';
                                    
                                    Log::error("Perplexity API Stream Notification ($errorCode): $errorMessage", [
                                        'error_data' => $errorData
                                    ]);
                                    
                                    echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                    echo "\n\n";
                                    flush();
                                    continue;
                                }
                            }
    
                            $data = json_decode($json, true);
                          
                            if (isset($data['choices'][0]['delta']['content'])) {
                                $raw = $data['choices'][0]['delta']['content'];
                                $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                $text .= $raw;
                               
                                echo 'data: ' . $clean;
                                echo "\n\n";
                                ob_flush();
                                flush();

                            }  
                            
                            if (isset($data['usage'])) {
                                if (isset($data['usage']['prompt_tokens'])) {
                                    $input_tokens = $data['usage']['prompt_tokens'];
                                }
                                if (isset($data['usage']['completion_tokens'])) {
                                    $output_tokens = $data['usage']['completion_tokens'];
                                }
                            }
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
                
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
    
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Perplexity API Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Perplexity service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
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

                $current_chat = ChatHistory::where('id', $chat_id)->first();
                $current_chat->response = $text;
                $current_chat->words = $words;
                $current_chat->input_tokens = $input_tokens;
                $current_chat->output_tokens = $output_tokens;
                $current_chat->save();

                $chat_conversation->words = ++$words;
                $chat_conversation->messages = $chat_conversation->messages + 1;
                $chat_conversation->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Bedrock stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamBedrock($conversation_id, $chat_id, $prompt)
    {
        return response()->stream(function () use ($conversation_id, $chat_id, $prompt) {
            $streamService = new \App\Services\AmazonBedrock($conversation_id, $chat_id, $prompt);
            
            $streamService->processStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Azure Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamAzure($conversation_id, $chat_id, $prompt)
    {
        return response()->stream(function () use ($conversation_id, $chat_id, $prompt) {
            $streamService = new \App\Services\AzureOpenai($conversation_id, $chat_id, $prompt);
            
            $streamService->processStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Azure Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamOpenRouter($conversation_id, $chat_id, $prompt)
    {
        return response()->stream(function () use ($conversation_id, $chat_id, $prompt) {
            $streamService = new \App\Services\OpenRouter($conversation_id, $chat_id, $prompt);
            
            $streamService->processStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Process Input Text
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function processCustom(Request $request) 
    {    
        # Check personal API keys
        if (config('settings.personal_openai_api') == 'allow') {
            if (is_null(auth()->user()->personal_openai_key)) {
                $status = 'error';
                $message =  __('You must include your personal Openai API key in your profile settings first');
                return response()->json(['status' => $status, 'message' => $message]); 
            }     
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $status = 'error';
                    $message =  __('You must include your personal Openai API key in your profile settings first');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } 
            }    
        } 


        # Check if user has sufficient words available to proceed
        $verify = HelperService::creditCheck($request->model, 200);
        if (isset($verify['status'])) {
            if ($verify['status'] == 'error') {
                return response()->json(['status' => $verify['status'], 'message' => $verify['message']]);
            }
        }

        $chat = new ChatHistory();
        $chat->user_id = auth()->user()->id;
        $chat->conversation_id = $request->conversation_id;
        $chat->prompt = $request->input('message');
        $chat->images = $request->image;
        $chat->model = $request->model;
        $chat->save();

        session()->put('conversation_id', $request->conversation_id);
        session()->put('chat_id', $chat->id);
        session()->put('realtime', $request->realtime);
        session()->put('message', $request->input('message'));
        session()->put('company', $request->company);
        session()->put('service', $request->service);

        return response()->json(['status' => 'success', 'chat_id' => $chat->id]);
        

	}


    /**
	*
	* Process Custom Chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function generateCustomChat(Request $request) 
    {  
        $main_settings = MainSetting::first();

        $conversation_id = $request->conversation_id;

        $prompt = session()->get('message'); 
        $realtime = session()->get('realtime'); 
        $company = session()->get('company');
        $service = session()->get('service');

        # Append real time data
        if($realtime == 'on') {
            if ($main_settings->realtime_data_engine == 'serper') {
                $prompt = $this->realtimeData($prompt, 'serper');
            } elseif ($main_settings->realtime_data_engine == 'perplexity') {
                $prompt = $this->realtimeData($prompt, 'perplexity');
            } 
        } 


        # Add Brand information
        if ($company != 'none') {
            $brand = BrandVoice::where('id', $company)->first();

            if ($brand) {
                $product = '';
                if ($service != 'none') {
                    foreach($brand->products as $key => $value) {
                        if ($key == $request->service)
                            $product = $value;
                    }
                } 
                
                if ($service != 'none') {
                    $prompt .= ".\n Focus on my company and {$product['type']}'s information: \n";
                } else {
                    $prompt .= ".\n Focus on my company's information: \n";
                }
                
                if ($brand->name) {
                    $prompt .= "The company's name is {$brand->name}. ";
                }

                if ($brand->description) {
                    $prompt .= "The company's description is {$brand->description}. ";
                }

                if ($brand->website) {
                    $prompt .= ". The company's website is {$brand->website}. ";
                }

                if ($brand->tagline) {
                    $prompt .= "The company's tagline is {$brand->tagline}. ";
                }

                if ($brand->audience) {
                    $prompt .= "The company's target audience is: {$brand->audience}. ";
                }

                if ($brand->industry) {
                    $prompt .= "The company focuses in: {$brand->industry}. ";
                }

                if ($product) {
                    if ($product['name']) {
                        $prompt .= "The {$product['type']}'s name is {$product['name']}. \n";
                    }

                    if ($product['description']) {
                        $prompt .= "The {$product['type']} is about {$product['description']}. ";
                    }                        
                }
            }
        }

        return response()->stream(function () use($conversation_id, $prompt) {

            if (config('settings.personal_openai_api') == 'allow') {
                $open_ai = auth()->user()->personal_openai_key;        
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    $open_ai = auth()->user()->personal_openai_key;               
                } else {
                    if (config('settings.openai_key_usage') !== 'main') {
                       $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                       array_push($api_keys, config('services.openai.key'));
                       $key = array_rand($api_keys, 1);
                       $open_ai = $api_keys[$key];
                   } else {
                       $open_ai = config('services.openai.key');
                   }
               }
               
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $open_ai = $api_keys[$key];
                } else {
                    $open_ai = config('services.openai.key');
                }
            }
    
            if (session()->has('chat_id')) {
                $chat_id = session()->get('chat_id');
            }

            if (session()->has('message')) {
                $message = session()->get('message');
            }

            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";
                
            $main_chat = CustomChat::where('chat_code', $chat_conversation->chat_code)->first();

            $messages[] = ['role' => 'user', 'content' => $prompt];
            

            if(request()->has('file')) {
                $file = request()->file('file');

                $imageTypes = ['c', 'cpp', 'doc', 'docx', 'html', 'java', 'md', 'php', 'pptx', 'py', 'rb', 'tex', 'js', 'ts', 'pdf', 'txt', 'json'];
                if (!in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
                    toastr()->error(__('Unsupported file format was selected, make sure to upload a file with a supported file format listed below'));
                    return redirect()->back();

                } else {

                    $name = Str::random(20);            
                    $folder = '/uploads/assistant/';            
                    $filePath = $folder . $name . '.' . $file->getClientOriginalExtension();
                    $this->uploadImage($file, $folder, 'public', $name);

                    $client = \OpenAI::factory()
                    ->withApiKey($open_ai)
                    ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
                    ->make();

                    $uploaded_file = $client->files()->upload([
                        'purpose' => 'assistants',
                        'file' => fopen( public_path() . $filePath, 'rb'),
                    ]);

                    $this->addFile($open_ai, $chat_conversation->vector_store,  $uploaded_file['id']);

                    $this->modifyThread($open_ai, $conversation_id, $chat_conversation->vector_store);

                }
            } 

            $latest_message = [
                'role' => 'user',
                'content' => $prompt,
            ];

            $this->createMessage($open_ai, $conversation_id, $latest_message);

            $client = HttpClient::create();
            $client = new EventSourceHttpClient($client, reconnectionTime: 2);
            $input_tokens = 0;
            $output_tokens = 0;

            $url = 'https://api.openai.com/v1/threads/' . $conversation_id . '/runs';

            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $open_ai,
                'Content-Type' => 'application/json',
            ];

            $body = json_encode([
                'assistant_id' => $chat_conversation->chat_code,
                    'model' => $chat_message->model,
                    'stream' => true 
            ]);

            try {
                $source = $client->request(
                    method: 'POST',
                    url: $url,
                    options: [                  
                        'buffer' => false,
                        'headers' => $headers,
                        'body' => $body,
                    ],
                );
    
                while ($source) {
                    foreach ($client->stream($source) as $chunk) {
                        if ($chunk instanceof ServerSentEvent) {
                            
                            $raw = $chunk->getArrayData();

                            if ($raw['object'] == 'thread.message.delta') {
                                $answer = $raw['delta']['content'][0]['text']['value'];
                                $text .= $answer;
                                $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $answer);
                                echo "data: " . $clean;
                                echo "\n\n";
                                ob_flush();
                                flush();                                
                            
                            } elseif ($raw['object'] == 'thread.run') {
                                if ($raw['status'] == 'completed') {
                                    $input_tokens = $raw['usage']['prompt_tokens'];
                                    $output_tokens = $raw['usage']['completion_tokens'];
                                    break; 
                                }
                            }
                           
                        } else  {
                            break;
                        }
                    }
                }

                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (Exception $e) {
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }
                

            # Update credit balance
            $words = count(explode(' ', ($text))); 
            HelperService::updateBalance($words, $main_chat->model, $input_tokens, $output_tokens); 

            $current_chat = ChatHistory::where('id', $chat_id)->first();
            $current_chat->response = $text;
            $current_chat->words = $words;
            $current_chat->input_tokens = $input_tokens;
            $current_chat->output_tokens = $output_tokens;
            $current_chat->save();

            $chat_conversation->words = ++$words;
            $chat_conversation->messages = $chat_conversation->messages + 1;
            $chat_conversation->save();

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
        
    }


    /**
	*
	* Realtime internet access
	* @return - prompt
	*
	*/
    public function realtimeData($prompt, $engine)
    {   
        $settings = ExtensionSetting::first();

        if ($engine == 'serper') {

            $client = new GuzzleClient;

            $headers = [
                'X-API-KEY'    => config('services.serper.key'),
                'Content-Type' => 'application/json',
            ];

            $body = [
                'q' => $prompt,
            ];

            try {

                $response = $client->post('https://google.serper.dev/search', [
                    'headers' => $headers,
                    'json'    => $body,
                ]);
    
                $result = $response->getBody()->getContents();

                $final_prompt = 'Prompt: ' . $prompt .
                                '\n\nWeb search json results: '
                                . json_encode($result) .
                                '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results (if the Web search results in the same context). If there are links included, show them in the following format: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not mention anything about the prompt text.';

                return $final_prompt; 

            } catch (Exception $e) {
                Log::info($response->getBody());
                return false;
            }              
    
        } elseif ($engine == 'perplexity') {

            $url = 'https://api.perplexity.ai/chat/completions';
            $api = $settings->perplexity_api;

            $payload = [
                'model'    => $settings->perplexity_realtime_model,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ];

            try {
                $response = Http::withToken($api)
                                ->withHeaders([
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);

                if ($response->successful()) {

                    $data = $response->json();
                    $response = $data['choices'][0]['message']['content'];

                    $final_prompt = 'Prompt: ' . $prompt .
                        '\n\nWeb search results: '
                        . $response .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). If there are links included, show them in the following format: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not mention anything about the prompt text.';

                    return $final_prompt;

                } else {
                    Log::info($response->body());
                    return false;
                }
            } catch (Exception $e) {
                Log::info($response->body());
                return false;
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



    /**
	*
	* Chat conversation
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function conversation(Request $request) {

        if ($request->ajax()) {

            if (isset($request->custom)) {
                
                if (config('settings.personal_openai_api') == 'allow') {
                    $open_ai = auth()->user()->personal_openai_key;        
                } elseif (!is_null(auth()->user()->plan_id)) {
                    $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                    if ($check_api->personal_openai_api) {
                        $open_ai = auth()->user()->personal_openai_key;               
                    } else {
                        if (config('settings.openai_key_usage') !== 'main') {
                        $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                        array_push($api_keys, config('services.openai.key'));
                        $key = array_rand($api_keys, 1);
                        $open_ai = $api_keys[$key];
                    } else {
                        $open_ai = config('services.openai.key');
                    }
                }
                
                } else {
                    if (config('settings.openai_key_usage') !== 'main') {
                        $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                        array_push($api_keys, config('services.openai.key'));
                        $key = array_rand($api_keys, 1);
                        $open_ai = $api_keys[$key];
                    } else {
                        $open_ai = config('services.openai.key');
                    }
                }

                $response = $this->createThread($open_ai);

                $vector = $this->createVectorStore($open_ai);

                $chat = new ChatConversation();
                $chat->user_id = auth()->user()->id;
                $chat->title = 'New Conversation';
                $chat->chat_code = $request->chat_code;
                $chat->conversation_id = $response['id'];
                $chat->messages = 0;
                $chat->words = 0;
                $chat->vector_store = $vector['id'];
                $chat->save();

                $data['status'] = 'success';
                $data['id'] = $response['id'];
                return $data;
            } else {
                $chat = new ChatConversation();
                $chat->user_id = auth()->user()->id;
                $chat->title = 'New Conversation';
                $chat->chat_code = $request->chat_code;
                $chat->conversation_id = $request->conversation_id;
                $chat->messages = 0;
                $chat->words = 0;
                $chat->save();

                $data = 'success';
                return $data;
            }

            
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
	* Process media file
	* @param - file id in DB
	* @return - confirmation
	* 
	*/
	public function view($code) 
    {
        if (session()->has('conversation_id')) {
            session()->forget('conversation_id');
        }

        $chat = Chat::where('chat_code', $code)->first(); 
        $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->orderBy('updated_at', 'desc')->get(); 

        $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
        $prompts = ChatPrompt::where('status', true)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $internet = $plan->internet_feature;
        } else {
            if (config('settings.internet_user_access') == 'allow') {
                $internet = true;
            } else {
                $internet = false;
            }
        }

        $extension = ExtensionSetting::first();
        $default_model = auth()->user()->default_model_chat;
        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();
        $brands_feature = \App\Services\HelperService::checkBrandVoiceAccess();

        return view('user.chat.view', compact('chat', 'messages', 'categories', 'prompts', 'internet', 'brands', 'brands_feature', 'default_model', 'extension'));
	}


     /**
	* 
	* Process media file
	* @param - file id in DB
	* @return - confirmation
	* 
	*/
	public function viewRealtime() 
    {
        if (session()->has('conversation_id')) {
            session()->forget('conversation_id');
        }

        $chat = Chat::where('chat_code', 'REALTIME')->first(); 
        $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->orderBy('updated_at', 'desc')->get(); 

        $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
        $prompts = ChatPrompt::where('status', true)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $internet = $plan->internet_feature;
        } else {
            if (config('settings.internet_user_access') == 'allow') {
                $internet = true;
            } else {
                $internet = false;
            }
        }

        $extension = ExtensionSetting::first();
        $default_model = auth()->user()->default_model_chat;
        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();
        $brands_feature = \App\Services\HelperService::checkBrandVoiceAccess();

        return view('user.chat.view', compact('chat', 'messages', 'categories', 'prompts', 'internet', 'brands', 'brands_feature', 'default_model', 'extension'));
	}


    public function viewCustom($code) 
    {
        if (session()->has('conversation_id')) {
            session()->forget('conversation_id');
        }

        $chat = CustomChat::where('chat_code', $code)->first(); 
        $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->orderBy('updated_at', 'desc')->get(); 

        $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
        $prompts = ChatPrompt::where('status', true)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $internet = $plan->internet_feature;
        } else {
            if (config('settings.internet_user_access') == 'allow') {
                $internet = true;
            } else {
                $internet = false;
            }
        } 

        # Apply proper model based on role and subsciption
        if (auth()->user()->group == 'user') {
            $models = explode(',', config('settings.free_tier_models'));
        } elseif (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $models = explode(',', $plan->model_chat);
        } else {            
            $models = explode(',', config('settings.free_tier_models'));
        }

        $default_model = auth()->user()->default_model_chat;
        $fine_tunes = FineTuneModel::all();
        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();
        $brands_feature = \App\Services\HelperService::checkBrandVoiceAccess();

        return view('user.chat.view-custom', compact('chat', 'messages', 'categories', 'prompts', 'internet', 'brands', 'models', 'fine_tunes', 'brands_feature', 'default_model'));
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
	* Rename conversation
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function listen(Request $request) 
    {
        if ($request->ajax()) {

            $voice = config('settings.chat_default_voice');

            # Count characters 
            $total_characters = mb_strlen($request->text, 'UTF-8');

            # Check if user has characters available to proceed
            if (auth()->user()->characters != -1) {
                if ((Auth::user()->characters + Auth::user()->characters_prepaid) < $total_characters) {
                    $data['status'] = 'error';
                    $data['message'] = __('Not sufficient characters to generate audio, please subscribe or top up');
                    return $data;
                } else {
                    $this->updateAvailableCharacters($total_characters);
                } 
            }

            if (config('settings.personal_openai_api') == 'allow') {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                    return $data; 
                } else {
                    $open_ai = auth()->user()->personal_openai_key; 
                } 
    
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    if (is_null(auth()->user()->personal_openai_key)) {
                        $data['status'] = 'error';
                        $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                        return $data; 
                    } else {
                        $open_ai = auth()->user()->personal_openai_key;
                    }
                } else {
                    if (config('settings.openai_key_usage') !== 'main') {
                       $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                       array_push($api_keys, config('services.openai.key'));
                       $key = array_rand($api_keys, 1);
                       $open_ai = $api_keys[$key];
                   } else {
                       $open_ai = config('services.openai.key');
                   }
               }
    
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $open_ai = $api_keys[$key];
                } else {
                    $open_ai = config('services.openai.key');
                }
            }


            try {

                $client = \OpenAI::client($open_ai);

                $audio_stream = $client->audio()->speech([
                    'model' => 'tts-1',
                    'input' => $request->text,
                    'voice' => $voice,
                ]);

                $file_name = 'chat-audio-' . Str::random(10) . '.mp3';

                if (config('settings.voiceover_default_storage') == 'aws') {
                    Storage::disk('s3')->put('chat/' . $file_name, $audio_stream, 'public');                
                    $result_url = Storage::disk('s3')->url('chat/' . $file_name);  
                } elseif (config('settings.voiceover_default_storage') == 'wasabi') {
                    Storage::disk('wasabi')->put('chat/' . $file_name, $audio_stream, 'public');                
                    $result_url = Storage::disk('wasabi')->url('chat/' . $file_name);        
                } elseif (config('settings.voiceover_default_storage') == 'r2') {
                    Storage::disk('r2')->put('chat/' . $file_name, $audio_stream, 'public');                
                    $result_url = Storage::disk('r2')->url('chat/' . $file_name);            
                } else {     
                    Storage::disk('audio')->put($file_name, $audio_stream);            
                    $result_url = Storage::url($file_name);                
                }


                $data['status'] = 'success';
                $data['url'] = $result_url; 
                return $data;

            } catch(Exception $e) {
                $data['status'] = 'error';
                $data['message'] = __('There was an error while generating audio, please contact support') . $e->getMessage();
                return $data;
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
Log::info(request('conversation_id'));
            $chat = ChatConversation::where('conversation_id', request('conversation_id'))->first(); 
Log::info($chat);
            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->delete();

                    ChatHistory::where('conversation_id', request('conversation_id'))->delete();

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


    public function model(Request $request) 
    {
        if ($request->ajax()) {

            $data['balance'] = auth()->user()->tokens + auth()->user()->tokens_prepaid;
            return $data; 
              
        }
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


            if (strlen(request('id')) < 6) {
                $chat = Chat::where('chat_code', request('id'))->first(); 
            } else {
                $chat = CustomChat::where('chat_code', request('id'))->first();
            }

            $favorite = FavoriteChat::where('chat_code', $chat->chat_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteChat();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->chat_code = $chat->chat_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


    public function escapeJson($value) 
    { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }


    /**
     * Update user characters number
     */
    private function updateAvailableCharacters($characters)
    {
        HelperService::updateCharacterBalance($characters);
    }


    
    public function createVectorStore($openai)
    {
        $url = 'https://api.openai.com/v1/vector_stores';

        $ch = curl_init();

        $data = array(
            "name" => "Chatbot Assistant",
        ); 
                    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer ' . $openai,
        )); 

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result , true);

        return $response;
    }


    public function addFile($openai, $vector_store_id, $file_id)
    {
        $url = 'https://api.openai.com/v1/vector_stores/' . $vector_store_id . '/files';

        $ch = curl_init();

        $data = array(
            "file_id" => $file_id,
        ); 
                    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer ' . $openai,
        )); 

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result , true);

        return $response;
    }


    public function createThread($openai)
    {
        $url = 'https://api.openai.com/v1/threads';

        $ch = curl_init();
                    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer ' . $openai,
        )); 

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result , true);

        return $response;

    }


    public function modifyThread($openai, $thread_id, $vector_id)
    {
        $url = 'https://api.openai.com/v1/threads/' . $thread_id;

        $ch = curl_init();

        $data = array(
            "tool_resources" => ["file_search" => ["vector_store_ids" => [$vector_id],]]
        ); 
                 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer ' . $openai,
        )); 

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result , true);

        return $response;

    }


    public function createMessage($openai, $thread_id, $messages)
    {
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        $ch = curl_init();
                 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messages));   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer ' . $openai,
        )); 

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result , true);

        return $response;

    }


    /**
     * Estimate token count for text using a simple approximation
     * 
     * @param string $text The text to count tokens for
     * @return int Estimated token count
     */
    private function estimateTokenCount($text) {
        // Simple approximation: 1 token ≈ 4 characters or 0.75 words
        // This is a rough estimate and will vary by model and content
        $charCount = mb_strlen($text);
        $wordCount = count(preg_split('/\s+/', trim($text)));
        
        // Average of character-based and word-based estimates
        $charBasedEstimate = $charCount / 4;
        $wordBasedEstimate = $wordCount / 0.75;
        
        return (int)round(($charBasedEstimate + $wordBasedEstimate) / 2);
    }


    /**
     * Estimate token usage for a set of messages
     * 
     * @param array $messages Array of message objects
     * @param string $generatedText The text generated by the model
     * @return array Token usage estimates
     */
    private function estimateTokenUsage($messages, $generatedText) {
        $promptText = '';
        foreach ($messages as $message) {
            $promptText .= $message['content'] . "\n";
        }
        
        $promptTokens = $this->estimateTokenCount($promptText);
        $completionTokens = $this->estimateTokenCount($generatedText);
        
        return [
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
        ];
    }


    /**
     * Check which Gemini models are available to your API key
     * 
     * @param string $gemini_api API key
     * @return array List of available models
     */
    private function getAvailableGeminiModels($gemini_api)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://generativelanguage.googleapis.com/v1beta/models?key={$gemini_api}");
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                Log::info("Available Gemini models: " . json_encode($data));
                //return $data['models'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("Error checking available Gemini models: " . $e->getMessage());
        }
        
        //return [];
    }


    public function getEphemeralKey()
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


        // API endpoint
        $endpoint = 'https://api.openai.com/v1/realtime/sessions';

        // Request payload
        $payload = [
            'model' => 'gpt-4o-realtime-preview-2024-12-17', 
            'voice' => 'verse',
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $openai_api",
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);


        if ($response->successful()) {
            $data = $response->json();
            $ephemeralKey = $data['client_secret']['value'] ?? null;
            $expiresAt = $data['client_secret']['expires_at'] ?? null;

            if ($ephemeralKey) {
                return response()->json([
                    'ephemeral_key' => $ephemeralKey,
                    'expires_at' => date('Y-m-d H:i:s', $expiresAt),
                ]);
            } else {
                return response()->json([
                    'error' => 'Failed to retrieve ephemeral key',
                    'response' => $data,
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'API request failed',
                'status' => $response->status(),
                'response' => $response->json(),
            ], $response->status());
        }
    }


    public function storeRealtimeMessage(Request $request)
    {

        $chat_conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();

        if ($request->type == 'user') {
            $chat = new ChatHistory();
            $chat->user_id = auth()->user()->id;
            $chat->conversation_id = $request->conversation_id;
            $chat->prompt = $request->message;
            $chat->model = $request->model;
            $chat->save();

            return response()->json([
                'chat_id' => $chat->id,
            ]);
        } elseif ($request->type == 'assistant') {

            $chat = ChatHistory::where('id', $request->chat_id)->first();

            $words = count(explode(' ', ($request->messasge)));
  
            $chat->response = $request->message;
            $chat->words = $words;
            $chat->save();

            $chat_conversation->words = ++$words;
            $chat_conversation->messages = $chat_conversation->messages + 1;
            $chat_conversation->save();
        } else {

            $chat = ChatHistory::where('id', $request->chat_id)->first();
            $chat->input_tokens = $request->input_tokens;
            $chat->output_tokens = $request->output_tokens;
            $chat->save();

            HelperService::updateBalance($chat->words, $chat->model, $request->input_tokens, $request->output_tokens); 
        }

    }


    public function storeChatShare(Request $request)
    {
        if ($request->ajax()) {

            $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();


            if ($conversation) {
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $uuid = '';
                
                $charactersLength = strlen($characters);
                
                for ($i = 0; $i < 20; $i++) {
                    $uuid .= $characters[rand(0, $charactersLength - 1)];
                }

                $permission = ($request->permission == 'read') ? true : false;
                $expiresAt = ($request->availability == 'limited' && $request->expiry_date) ? $expiresAt = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->expiry_date)->format('Y-m-d H:i:s') : null;

                ChatShare::create([
                    'uuid' => $uuid,
                    'user_id' => auth()->user()->id,
                    'chat_code' => $conversation->chat_code,
                    'conversation_id' => $conversation->conversation_id,
                    'read_only' => $permission,
                    'availability' => $request->availability,
                    'expires_at' => $expiresAt
                ]);

                $url = config('app.url') . '/app/chat/share/' . $uuid;

                return response()->json([
                    'status' => 200,
                    'url' => $url
                ]);
            } else {
                return response()->json([
                    'status' => 400
                ]);
            }            
        }
    }


    public function showChatShare($uuid)
    {

        $shared = ChatShare::where('uuid', $uuid)->first();

        $conversation = ChatConversation::where('conversation_id', $shared->conversation_id)->first();

        if (!$conversation) {
            abort(404);
        }
        
        if ($shared) {
            if ($shared->availability == 'limited') {
                if ($shared->expires_at->isPast()) {
                    abort(404);
                } else {
                    $chat = Chat::where('chat_code', $shared->chat_code)->first();  
                    $conversation_id = $shared->conversation_id;
                    return view('user.chat.share', compact('chat', 'conversation_id', 'shared'));
                }
            } else {
                $chat = Chat::where('chat_code', $shared->chat_code)->first();  
                $conversation_id = $shared->conversation_id;
                return view('user.chat.share', compact('chat', 'conversation_id', 'shared'));
            }            
        } else {
            abort(404);
        }
    }


    public function sharedHistory(Request $request) {

        if ($request->ajax()) {

            $messages = ChatHistory::where('conversation_id', $request->conversation_id)->get();
            return $messages;
        }   
    }


    /**
	*
	* Process Input Text
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function processChatShare(Request $request) 
    {       
        $user = User::where('id', $request->user)->first();

        # Check if user has sufficient words available to proceed
        $verify = HelperService::creditCheck($request->model, 200, $user->id);
        if (isset($verify['status'])) {
            if ($verify['status'] == 'error') {
                return response()->json(['status' => $verify['status'], 'message' => $verify['message']]);
            }
        }

        $chat = new ChatHistory();
        $chat->user_id = $user->id;
        $chat->conversation_id = $request->conversation_id;
        $chat->prompt = $request->input('message');
        $chat->images = $request->image;
        $chat->model = $request->model;
        $chat->save();

        session()->put('conversation_id', $request->conversation_id);
        session()->put('chat_id', $chat->id);
        session()->put('message', $request->input('message'));
        session()->put('model', $request->model);
        session()->put('user_id', $request->user);

        return response()->json(['status' => 'success', 'chat_id' => $chat->id]);
        
	}


     /**
	*
	* Process Chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function generateChatShare(Request $request) 
    {  
        # Get Settings
        $settings = MainSetting::first();
        $extension = ExtensionSetting::first();

        $conversation_id = $request->conversation_id;

        $prompt= session()->get('message'); 
        $chat_id = session()->get('chat_id');
        $model = session()->get('model');
        $user_id = session()->get('user_id');
        

        # Start OpenAI task
        if (in_array($model, ['gpt-3.5-turbo-0125', 'gpt-4', 'gpt-4o', 'gpt-4o-mini', 'gpt-4.5-preview', 'o1', 'o1-mini', 'o3-mini', 'gpt-4-0125-preview'])) {
            if (\App\Services\HelperService::extensionAzureOpenai() && $extension->azure_openai_activate) {
    
                return $this->streamAzure($conversation_id, $chat_id, $prompt);                      

            } elseif (\App\Services\HelperService::extensionOpenRouter() && $extension->open_router_activate) {

                return $this->streamOpenRouter($conversation_id, $chat_id, $prompt);                      
            
            } else {
                $user = User::where('id', $user_id)->first();

                if (config('settings.personal_openai_api') == 'allow') {
                    $openai_api = $user->personal_openai_key;        
                } elseif (!is_null($user->plan_id)) {
                    $check_api = SubscriptionPlan::where('id', $user->plan_id)->first();
                    if ($check_api->personal_openai_api) {
                        $openai_api = $user->personal_openai_key;               
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
    
                if (is_null($openai_api) || $openai_api == '') {
                    return response()->stream(function () {
                        echo 'data: OpenAI Notification: <span class="font-weight-bold">Missing OpenAI API key</span>. Please contact support team.';
                        echo "\n\n";
                        echo 'data: [DONE]';
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }, 200, [
                        'Cache-Control' => 'no-cache',
                        'X-Accel-Buffering' => 'no',
                        'Content-Type' => 'text/event-stream',
                    ]);
                }
    
                return $this->streamOpenai($conversation_id, $chat_id, $prompt, $openai_api, $user_id);
            }
            
        }

    }


}
