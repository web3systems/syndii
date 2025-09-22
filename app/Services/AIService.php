<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPlan;
use App\Models\MainSetting;
use App\Models\Extension;
use App\Models\ExtensionSetting;
use App\Models\ApiKey;
use Carbon\Carbon;
use Exception;

class AIService 
{

    public static function getOpenAIKey()
    {
        if (config('settings.personal_openai_api') == 'allow' && auth()->user()->personal_openai_key) {
            return auth()->user()->personal_openai_key;
        }

        if (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api && auth()->user()->personal_openai_key) {
                return auth()->user()->personal_openai_key;
            }
        }

        if (config('settings.openai_key_usage') !== 'main') {
            $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
            array_push($api_keys, config('services.openai.key'));
            return $api_keys[array_rand($api_keys, 1)];
        }

        return config('services.openai.key');
    }


    
}



