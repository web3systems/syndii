<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_settings', function (Blueprint $table) {
            $table->id();
            $table->text('languages');
            $table->string('default_language');
            $table->boolean('youtube_feature')->nullable()->default(0);
            $table->string('youtube_api')->nullable();
            $table->boolean('youtube_feature_free_tier')->nullable()->default(0);
            $table->boolean('rss_feature')->nullable()->default(0);
            $table->boolean('rss_feature_free_tier')->nullable()->default(0);            
            $table->boolean('weekly_reports')->nullable()->default(0);
            $table->boolean('monthly_reports')->nullable()->default(0);
            $table->string('frontend_theme')->default('default');
            $table->string('dashboard_theme')->default('default');
            $table->string('logo_frontend')->default('uploads/logo/frontend-logo.png');
            $table->string('logo_frontend_collapsed')->default('uploads/logo/frontend-collapsed-logo.png');
            $table->string('logo_frontend_footer')->default('uploads/logo/frontend-footer-logo.png');
            $table->string('logo_dashboard')->default('uploads/logo/dashboard-logo.png');
            $table->string('logo_dashboard_dark')->default('uploads/logo/dashboard-dark-logo.png');
            $table->string('logo_dashboard_collapsed')->default('uploads/logo/dashboard-collapsed-logo.png');
            $table->string('logo_dashboard_collapsed_dark')->default('uploads/logo/dashboard-collapsed-dark-logo.png');
            $table->string('image_vendors')->nullable()->default('openai');
            $table->integer('image_credits')->nullable()->default(0);
            $table->boolean('integration_feature')->default(false);
            $table->integer('token_credits')->nullable()->default(0);
            $table->string('model_credit_name')->nullable()->default('words');
            $table->string('model_charge_type')->nullable()->default('both');
            $table->string('model_disabled_vendors')->nullable()->default('hide');
            $table->string('deepseek_api')->nullable();
            $table->string('deepseek_base_url')->nullable()->default('https://api.deepseek.com/v1');
            $table->string('xai_api')->nullable();
            $table->string('xai_base_url')->nullable()->default('https://api.x.ai/v1');
            $table->string('realtime_data_engine')->nullable();

            $table->boolean('writer_feature')->nullable()->default(1);
            $table->boolean('writer_feature_free_tier')->nullable()->default(1); 
            $table->boolean('wizard_feature')->nullable()->default(1);
            $table->boolean('wizard_feature_free_tier')->nullable()->default(1); 
            $table->boolean('smart_editor_feature')->nullable()->default(1);
            $table->boolean('smart_editor_feature_free_tier')->nullable()->default(1); 
            $table->boolean('images_feature')->nullable()->default(1);
            $table->boolean('images_feature_free_tier')->nullable()->default(1);
            $table->boolean('rewriter_feature')->nullable()->default(1);
            $table->boolean('rewriter_feature_free_tier')->nullable()->default(1);
            $table->boolean('voiceover_feature')->nullable()->default(1);
            $table->boolean('voiceover_feature_free_tier')->nullable()->default(1);
            $table->boolean('transcribe_feature')->nullable()->default(1);
            $table->boolean('transcribe_feature_free_tier')->nullable()->default(1);
            $table->boolean('chat_feature')->nullable()->default(1);
            $table->boolean('chat_feature_free_tier')->nullable()->default(1);
            $table->boolean('vision_feature')->nullable()->default(1);
            $table->boolean('vision_feature_free_tier')->nullable()->default(1);
            $table->boolean('file_chat_feature')->nullable()->default(1);
            $table->boolean('file_chat_feature_free_tier')->nullable()->default(1);
            $table->boolean('web_chat_feature')->nullable()->default(1);
            $table->boolean('web_chat_feature_free_tier')->nullable()->default(1);
            $table->boolean('image_chat_feature')->nullable()->default(1);
            $table->boolean('image_chat_feature_free_tier')->nullable()->default(1);
            $table->boolean('code_feature')->nullable()->default(1);
            $table->boolean('code_feature_free_tier')->nullable()->default(1);
            $table->boolean('brand_voice_feature')->nullable()->default(1);
            $table->boolean('brand_voice_feature_free_tier')->nullable()->default(1);
            $table->boolean('integration_feature_free_tier')->nullable()->default(0);
            $table->boolean('team_member_feature')->nullable()->default(1);
            $table->boolean('team_member_feature_free_tier')->nullable()->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_settings');
    }
};
