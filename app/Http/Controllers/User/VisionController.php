<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use App\Models\SubscriptionPlan;
use App\Models\FavoriteChat;
use App\Models\ChatConversation;
use App\Models\ChatCategory;
use App\Models\ChatHistory;
use App\Models\ChatPrompt;
use App\Models\ApiKey;
use App\Models\Chat;
use App\Models\User;
use App\Models\Setting;
use GuzzleHttp\Client;
use App\Models\BrandVoice;
use App\Models\FineTuneModel;
use App\Services\HelperService;
use App\Services\AIService;
use Exception;


class VisionController extends Controller
{
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
            $models = explode(',', $plan->model_chat);
        } else {            
            $models = explode(',', config('settings.free_tier_models'));
        }

        $fine_tunes = FineTuneModel::all();
        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();
        $brands_feature = \App\Services\HelperService::checkBrandVoiceAccess();
        $default_model = auth()->user()->default_model_chat;

        # Check user permission to use the feature
        if (auth()->user()->group == 'user') {
            if (config('settings.vision_access_user') != 'allow') {
               toastr()->warning(__('AI Vision feature is not available for free tier users, subscribe to get a proper access'));
               return redirect()->route('user.plans');
            } else {
                if (session()->has('conversation_id')) {
                    session()->forget('conversation_id');
                }
        
                $chat = Chat::where('chat_code', 'VISION')->first(); 
                $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'VISION')->orderBy('updated_at', 'desc')->get(); 
        
                $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
                $prompts = ChatPrompt::where('status', true)->get();
        
                return view('user.vision.index', compact('chat', 'messages', 'categories', 'prompts', 'brands', 'models', 'fine_tunes', 'brands_feature', 'default_model'));
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->vision_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for AI Vision feature'));
                return redirect()->back();                   
            } else {
                if (session()->has('conversation_id')) {
                    session()->forget('conversation_id');
                }
        
                $chat = Chat::where('chat_code', 'VISION')->first(); 
                $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'VISION')->orderBy('updated_at', 'desc')->get(); 
        
                $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
                $prompts = ChatPrompt::where('status', true)->get();
        
                return view('user.vision.index', compact('chat', 'messages', 'categories', 'prompts', 'brands', 'models', 'fine_tunes', 'brands_feature', 'default_model'));
            }
        } else {
            if (session()->has('conversation_id')) {
                session()->forget('conversation_id');
            }
    
            $chat = Chat::where('chat_code', 'VISION')->first(); 
            $messages = ChatConversation::where('user_id', auth()->user()->id)->where('chat_code', 'VISION')->orderBy('updated_at', 'desc')->get(); 
    
            $categories = ChatPrompt::where('status', true)->groupBy('group')->pluck('group'); 
            $prompts = ChatPrompt::where('status', true)->get();


            return view('user.vision.index', compact('chat', 'messages', 'categories', 'prompts', 'brands', 'models', 'fine_tunes', 'brands_feature', 'default_model'));
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
        $verify = HelperService::creditCheck($request->model, 20);
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
        session()->put('company', $request->company);
        session()->put('service', $request->service);

        if (auth()->user()->available_words != -1) {
           // return response()->json(['status' => 'success', 'old'=> $balance, 'current' => ($balance - $words), 'chat_id' => $chat->id]);
           return response()->json(['status' => 'success', 'chat_id' => $chat->id]);
        } else {
           // return response()->json(['status' => 'success', 'old'=> 0, 'current' => 0, 'chat_id' => $chat->id]);
           return response()->json(['status' => 'success', 'chat_id' => $chat->id]);
        }

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
        $conversation_id = $request->conversation_id;

        return response()->stream(function () use($conversation_id) {

            $openai_key = AIService::getOpenAIKey();
    
            if (session()->has('chat_id')) {
                $chat_id = session()->get('chat_id');
            }

            $chat_conversation = ChatConversation::where('conversation_id', $conversation_id)->first();  
            $chat_message = ChatHistory::where('id', $chat_id)->first();
            $text = "";
            $model = '';
            $input_tokens = 0;
            $output_tokens = 0;

            if (is_null($chat_message->images)) {

                $this->handleTextChat($openai_key, $chat_conversation, $chat_message, $text);

            } else {
                $guzzle_client = new Client();
                $url = 'https://api.openai.com/v1/chat/completions';
            
                try {
                    $model = 'gpt-4-turbo-2024-04-09';
                    $response = $guzzle_client->post($url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $openai_key,
                        ],
                        'json' => [
                            'model' => $model,
                            'messages' => [
                                [
                                'role' => 'user',
                                'content' => [
                                            [ 
                                                'type' => 'text',
                                                'text' => $chat_message->prompt,
                                            ],
                                            [
                                            'type' => 'image_url',
                                            'image_url' => [
                                                'url' => $chat_message->images,
                                                ],
                                            ],
                                    ],
                                ],
                            ],
                            'max_tokens' => 2500,
                            'stream' => true,
                            
                        ]
                    ]); 
                } catch (\Exception $exception) {
                    $error = 'Looks like your OpenAI account does not have access to GPT4-Vision model. Contant OpenAI support team to get an access.';
                    echo "data: $error";
                    echo "\n\n";
                    ob_flush();
                    flush();
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }

        
                foreach (explode("\n", $response->getBody()->getContents()) as $data) { 
                    if ($data != 'data: [DONE]') {
                        $array = explode('data: ', $data);
                    } else {
                        echo "data: [DONE]";
                    }
                    
                    foreach ($array as $response){
                        $response = json_decode($response, true);
                   
                        if ($data != "data: [DONE]\n\n" and isset($response["choices"][0]["delta"]["content"])) {
                            $text .= $response["choices"][0]["delta"]["content"];
                            $raw = $response['choices'][0]['delta']['content'];
                            $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                            echo "data: " . $clean;
                        }
                    }
                
                    echo PHP_EOL;
                    ob_flush();
                    flush();
                    
                }

                if (!empty($text)) {
                    Log::info('tut');
                    # Update credit balance
                    $words = count(explode(' ', ($text)));   
                    $tokens = $this->estimateTokenUsage($chat_message->prompt, $text);
                    Log::info($tokens);
                    HelperService::updateBalance($words, $chat_message->model, $tokens['prompt_tokens'], $tokens['completion_tokens']); 
        
                    $chat_message->response = $text;
                    $chat_message->words = $words;
                    $chat_message->input_tokens = $input_tokens;
                    $chat_message->output_tokens = $output_tokens;
                    $chat_message->save();
        
                    $chat_conversation->words = ++$words;
                    $chat_conversation->messages = $chat_conversation->messages + 1;
                    $chat_conversation->save();
                }
            }


        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
        
    }


    private function handleTextChat($openai_key, $chat_conversation, $chat_message, &$text)
    {

        $main_chat = Chat::where('chat_code', $chat_conversation->chat_code)->first();
        $chat_messages = ChatHistory::where('conversation_id', $chat_conversation->conversation_id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->reverse();

        $input_tokens = 0;
        $output_tokens = 0;

        # Prepare chat history
        if (strpos($chat_message->model, 'o1-') === 0) {
            $first_message = $main_chat->prompt . "\n\nUser: " . $chat_message->prompt;
            $messages[] = ['role' => 'user', 'content' => $first_message];

        } else {

            $messages[] = ['role' => 'system', 'content' => $main_chat->prompt];

            foreach ($chat_messages as $chat) {
                if (empty($chat['response']) || is_null($chat['response'])) {
                    continue;
                }

                $messages[] = ['role' => 'user', 'content' => $chat['prompt']];
                if (!empty($chat['response'])) {
                    $messages[] = ['role' => 'assistant', 'content' => $chat['response']];
                }
            }                

            $messages[] = ['role' => 'user', 'content' => $chat_message->prompt];   
        }                       


        try {

            $openai_client = \OpenAI::client($openai_key);
                
            $stream = $openai_client->chat()->createStreamed([
                'model' => $chat_message->model,
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
            HelperService::updateBalance($words, $chat_message->model, $input_tokens, $output_tokens);   

            $current_chat = ChatHistory::where('id', $chat_message->id)->first();
            $current_chat->response = $text;
            $current_chat->words = $words;
            $current_chat->input_tokens = $input_tokens;
            $current_chat->output_tokens = $output_tokens;
            $current_chat->save();

            $chat_conversation->words = ++$words;
            $chat_conversation->messages = $chat_conversation->messages + 1;
            $chat_conversation->save();
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

            $chat = new ChatConversation();
            $chat->user_id = auth()->user()->id;
            $chat->title = 'AI Vision Conversation';
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


    public function escapeJson($value) 
    { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }


    /**
     * Estimate token count for text using a simple approximation
     * 
     * @param string $text The text to count tokens for
     * @return int Estimated token count
     */
    public function estimateTokenCount($text) {
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
    public function estimateTokenUsage($message, $generatedText) {

        $promptTokens = $this->estimateTokenCount($message);
        $completionTokens = $this->estimateTokenCount($generatedText);
        
        return [
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
        ];
    }

}
