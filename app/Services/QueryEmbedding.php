<?php

namespace App\Services;

use Exception;
use OpenAI\Client;
use App\Models\SubscriptionPlan;
use App\Models\ApiKey;

class QueryEmbedding
{
    public function getQueryEmbedding($question): array
    {
        if (config('settings.personal_openai_api') == 'allow') {
            $openai_key = auth()->user()->personal_openai_key;         
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                $openai_key = auth()->user()->personal_openai_key;                
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $openai_key = $api_keys[$key];
                } else {
                    $openai_key = config('services.openai.key');
                }
            }
        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                $openai_key = $api_keys[$key];
            } else {
                $openai_key = config('services.openai.key');
            }
        }

        $client = \OpenAI::client($openai_key);

        $result = $client->embeddings()->create([
            'model' => config('settings.default_embedding_model'),
            'input' => $question
        ]);

        if (count($result['data']) == 0) {
            throw new Exception("Failed to generated query embedding!");
        }

        return $result->embeddings[0]->embedding;
    }

    public function askQuestionStreamed($context, $question, $model)
    {
        if (config('settings.personal_openai_api') == 'allow') {
            $openai_key = auth()->user()->personal_openai_key;         
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                $openai_key = auth()->user()->personal_openai_key;                
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                    $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                    array_push($api_keys, config('services.openai.key'));
                    $key = array_rand($api_keys, 1);
                    $openai_key = $api_keys[$key];
                } else {
                    $openai_key = config('services.openai.key');
                }
            }
        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                $openai_key = $api_keys[$key];
            } else {
                $openai_key = config('services.openai.key');
            }
        }

        $system_template = "
        Use the following pieces of context to answer the users question. 
        If you don't know the answer, just say that you don't know, don't try to make up an answer.
        ----------------
        {context}
        ";

        $system_prompt = str_replace("{context}", $context, $system_template);

        $openai_client = \OpenAI::client($openai_key);
                    
        return $stream = $openai_client->chat()->createStreamed([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'temperature' => 0.8,
            'stream_options'=>[
                'include_usage' => true,
            ]
        ]);

    }
}
