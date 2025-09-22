<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwitterService;
use App\Services\LinkedinService;
use App\Services\InstagramService;
use App\Services\FacebookService;
use App\Models\SocialMediaPost;
use App\Models\SocialMediaAccount;
use App\Models\User;
use Carbon\Carbon;

class SocialMediaPostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto post schedule social media posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check subscription status, block the ones that missed payments.
     *
     * @return int
     */
    public function handle()
    {
        # Get all active posts
        $posts = SocialMediaPost::where('status', '<>', 'completed')->get();
        
        foreach($posts as $post) {

            # Check if yearly or lifetime plans
            if ($row->frequency == 'yearly' || $row->frequency == 'lifetime') {

                $date = Carbon::createFromFormat('Y-m-d H:i:s', $row->active_until);

                $result = Carbon::createFromFormat('Y-m-d H:i:s', $date)->isPast();

                if (!$result) {            

                    $today = Carbon::now();
                    $subscription_day = $date->day;
                    $current_day = $today->day;
                    $days_in_month = $today->daysInMonth;

                    if ($subscription_day == $current_day) {
                        $user = User::where('id', $row->user_id)->firstOrFail();
                        $plan = SubscriptionPlan::where('id', $row->plan_id)->firstOrFail();
                        if ($user) {
                            $user->gpt_3_turbo_credits = $plan->gpt_3_turbo_credits;
                            $user->gpt_4_turbo_credits = $plan->gpt_4_turbo_credits;
                            $user->gpt_4_credits = $plan->gpt_4_credits;
                            $user->gpt_4o_credits = $plan->gpt_4o_credits;
                            $user->claude_3_opus_credits = $plan->claude_3_opus_credits;
                            $user->claude_3_sonnet_credits = $plan->claude_3_sonnet_credits;
                            $user->claude_3_haiku_credits = $plan->claude_3_haiku_credits;
                            $user->gemini_pro_credits = $plan->gemini_pro_credits;
                            $user->fine_tune_credits = $plan->fine_tune_credits;
                            $user->available_chars = $plan->characters;
                            $user->available_minutes = $plan->minutes;
                            $user->available_dalle_images = $plan->dalle_images;
                            $user->available_sd_images = $plan->sd_images;
                            $user->save();
                        }
                    } elseif ($subscription_day > $days_in_month) {
                        $user = User::where('id', $row->user_id)->firstOrFail();
                        $plan = SubscriptionPlan::where('id', $row->plan_id)->firstOrFail();
                        if ($user) {
                            $user->gpt_3_turbo_credits = $plan->gpt_3_turbo_credits;
                            $user->gpt_4_turbo_credits = $plan->gpt_4_turbo_credits;
                            $user->gpt_4_credits = $plan->gpt_4_credits;
                            $user->gpt_4o_credits = $plan->gpt_4o_credits;
                            $user->claude_3_opus_credits = $plan->claude_3_opus_credits;
                            $user->claude_3_sonnet_credits = $plan->claude_3_sonnet_credits;
                            $user->claude_3_haiku_credits = $plan->claude_3_haiku_credits;
                            $user->gemini_pro_credits = $plan->gemini_pro_credits;
                            $user->fine_tune_credits = $plan->fine_tune_credits;
                            $user->available_chars = $plan->characters;
                            $user->available_minutes = $plan->minutes;
                            $user->available_dalle_images = $plan->dalle_images;
                            $user->available_sd_images = $plan->sd_images;
                            $user->save();
                        }
                    }
                    
                }
            }

            if ($row->gateway == 'Manual') {

                $date = Carbon::createFromFormat('Y-m-d H:i:s', $row->active_until);

                $result = Carbon::createFromFormat('Y-m-d H:i:s', $date)->isPast();

                if (!$result) {            

                    $today = Carbon::now();
                    $subscription_day = $date->day;
                    $current_day = $today->day;
                    $days_in_month = $today->daysInMonth;

                    if ($subscription_day == $current_day) {
                        $user = User::where('id', $row->user_id)->firstOrFail();
                        $plan = SubscriptionPlan::where('id', $row->plan_id)->firstOrFail();

                        if ($user) {
                            $user->gpt_3_turbo_credits = $plan->gpt_3_turbo_credits;
                            $user->gpt_4_turbo_credits = $plan->gpt_4_turbo_credits;
                            $user->gpt_4_credits = $plan->gpt_4_credits;
                            $user->gpt_4o_credits = $plan->gpt_4o_credits;
                            $user->claude_3_opus_credits = $plan->claude_3_opus_credits;
                            $user->claude_3_sonnet_credits = $plan->claude_3_sonnet_credits;
                            $user->claude_3_haiku_credits = $plan->claude_3_haiku_credits;
                            $user->gemini_pro_credits = $plan->gemini_pro_credits;
                            $user->fine_tune_credits = $plan->fine_tune_credits;
                            $user->available_chars = $plan->characters;
                            $user->available_minutes = $plan->minutes;
                            $user->available_dalle_images = $plan->dalle_images;
                            $user->available_sd_images = $plan->sd_images;
                            $user->save();
                        }
                    } elseif ($subscription_day > $days_in_month) {
                        $user = User::where('id', $row->user_id)->firstOrFail();
                        $plan = SubscriptionPlan::where('id', $row->plan_id)->firstOrFail();
                        if ($user) {
                            $user->gpt_3_turbo_credits = $plan->gpt_3_turbo_credits;
                            $user->gpt_4_turbo_credits = $plan->gpt_4_turbo_credits;
                            $user->gpt_4_credits = $plan->gpt_4_credits;
                            $user->gpt_4o_credits = $plan->gpt_4o_credits;
                            $user->claude_3_opus_credits = $plan->claude_3_opus_credits;
                            $user->claude_3_sonnet_credits = $plan->claude_3_sonnet_credits;
                            $user->claude_3_haiku_credits = $plan->claude_3_haiku_credits;
                            $user->gemini_pro_credits = $plan->gemini_pro_credits;
                            $user->fine_tune_credits = $plan->fine_tune_credits;
                            $user->available_chars = $plan->characters;
                            $user->available_minutes = $plan->minutes;
                            $user->available_dalle_images = $plan->dalle_images;
                            $user->available_sd_images = $plan->sd_images;
                            $user->save();
                        }
                    }
                    
                }

             }
        }
    }
}
