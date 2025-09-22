<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_settings', function (Blueprint $table) {
            $table->id();
            $table->string('plagiarism_api')->nullable();
            $table->boolean('plagiarism_feature')->default(false);
            $table->boolean('plagiarism_free_tier')->default(false);
            $table->boolean('detector_feature')->default(false);
            $table->boolean('detector_free_tier')->default(false);
            $table->string('flux_api')->nullable();
            $table->string('pebblely_api')->nullable();
            $table->string('pebblely_fashion_api')->nullable();
            $table->boolean('pebblely_feature')->default(false);
            $table->boolean('pebblely_fashion_feature')->default(false);
            $table->boolean('pebblely_free_tier')->default(false);
            $table->boolean('pebblely_fashion_free_tier')->default(false);
            $table->string('voice_clone_elevenlabs_api')->nullable();
            $table->boolean('voice_clone_feature')->default(false);
            $table->boolean('voice_clone_free_tier')->default(false);
            $table->integer('voice_clone_limit')->nullable()->default(0);
            $table->boolean('sound_studio_feature')->default(false);
            $table->boolean('sound_studio_free_tier')->default(false);
            $table->integer('sound_studio_max_merge_files')->nullable()->default(1);
            $table->integer('sound_studio_max_audio_size')->nullable()->default(1);
            $table->string('photo_studio_stability_api')->nullable();
            $table->boolean('photo_studio_feature')->default(false);
            $table->boolean('photo_studio_free_tier')->default(false);            
            $table->boolean('integration_wordpress_feature')->default(false);
            $table->boolean('integration_wordpress_free_tier')->default(false);
            $table->boolean('integration_wordpress_auto_post')->default(false);
            $table->integer('integration_wordpress_website_numbers')->nullable()->default(1);
            $table->integer('integration_wordpress_post_numbers')->nullable()->default(1);
            $table->string('heygen_api')->nullable();
            $table->boolean('heygen_avatar_feature')->nullable()->default(0);
            $table->boolean('heygen_avatar_free_tier')->nullable()->default(0);
            $table->boolean('heygen_avatar_video')->nullable()->default(0);
            $table->boolean('heygen_avatar_image')->nullable()->default(0);
            $table->integer('heygen_avatar_video_numbers')->nullable()->default(0);
            $table->integer('heygen_avatar_image_numbers')->nullable()->default(0);
            $table->string('video_text_falai_api')->nullable();
            $table->boolean('video_text_feature')->default(false);
            $table->boolean('video_text_free_tier')->default(false);
            $table->string('voice_isolator_elevenlabs_api')->nullable();
            $table->boolean('voice_isolator_feature')->default(false);
            $table->boolean('voice_isolator_free_tier')->default(false);
            $table->boolean('saas_feature')->default(false);
            $table->boolean('social_media_feature')->default(false);
            $table->boolean('social_media_free_tier')->default(false);
            $table->string('video_video_falai_api')->nullable();
            $table->boolean('video_video_feature')->default(false);
            $table->boolean('video_video_free_tier')->default(false);
            $table->boolean('maintenance_feature')->default(false);
            $table->string('maintenance_banner')->nullable();
            $table->text('maintenance_header')->nullable();
            $table->text('maintenance_message')->nullable();
            $table->text('maintenance_footer')->nullable();
            $table->string('video_image_stability_api')->nullable();
            $table->string('video_image_falai_api')->nullable();
            $table->boolean('video_image_feature')->default(false);
            $table->boolean('video_image_free_tier')->default(false);
            $table->string('midjourney_api')->nullable();
            $table->string('faceswap_piapi_api')->nullable();
            $table->boolean('faceswap_feature')->default(false);
            $table->boolean('faceswap_free_tier')->default(false);
            $table->string('music_aiml_api')->nullable();
            $table->boolean('music_feature')->default(false);
            $table->boolean('music_free_tier')->default(false);
            $table->boolean('ibm_watson_feature')->default(false);
            $table->string('ibm_watson_api')->nullable();
            $table->string('ibm_watson_endpoint_url')->nullable();
            $table->string('clipdrop_api')->nullable();
            $table->string('hubspot_access_token')->nullable();
            $table->string('mailchimp_api')->nullable();
            $table->string('mailchimp_list_id')->nullable();
            $table->string('perplexity_api')->nullable();
            $table->string('perplexity_realtime_model')->nullable()->default('sonar');
            $table->boolean('chat_share_feature')->default(false);
            $table->boolean('chat_share_free_tier')->default(false);
            $table->string('textract_aws_access_key')->nullable();
            $table->string('textract_aws_secret_access_key')->nullable();
            $table->string('textract_aws_region')->nullable()->default('us-east-1');
            $table->string('textract_aws_bucket')->nullable();
            $table->boolean('textract_feature')->default(false);
            $table->boolean('textract_free_tier')->default(false);
            $table->integer('textract_max_pdf_pages')->nullable()->default(1);
            $table->integer('textract_max_pdf_size')->nullable()->default(1);
            $table->integer('textract_max_image_size')->nullable()->default(1);
            $table->boolean('seo_feature')->default(false);
            $table->boolean('seo_free_tier')->default(false);
            $table->boolean('chat_realtime_feature')->default(false);
            $table->boolean('chat_realtime_free_tier')->default(false);
            $table->string('chat_realtime_voice')->nullable()->default('alloy');
            $table->string('chat_realtime_model')->nullable()->default('gpt-4o-mini-realtime-preview');
            $table->boolean('chatbot_external_feature')->default(false);
            $table->boolean('chatbot_external_free_tier')->default(false);
            $table->integer('chatbot_external_quantity')->nullable()->default(1);
            $table->integer('chatbot_external_domains')->nullable()->default(1);
            $table->text('chatbot_external_ai_vendors')->nullable();
            $table->string('azure_openai_endpoint')->nullable();
            $table->string('azure_openai_key')->nullable();
            $table->boolean('azure_openai_activate')->default(false);
            $table->string('amazon_bedrock_access_key')->nullable();
            $table->string('amazon_bedrock_secret_key')->nullable();
            $table->string('amazon_bedrock_region')->nullable()->default('us-west-2');
            $table->string('elevenlabs_speech_pro_api')->nullable();
            $table->string('openai_speech_pro_api')->nullable();
            $table->boolean('speech_text_pro_feature')->default(false);
            $table->boolean('speech_text_pro_free_tier')->default(false);
            $table->integer('speech_text_pro_max_file_size')->default(1)->nullable();
            $table->string('xero_client_id')->nullable();
            $table->string('xero_client_secret')->nullable();
            $table->string('open_router_key')->nullable();
            $table->boolean('open_router_activate')->default(false);
            $table->boolean('wallet_feature')->default(false);
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
        Schema::dropIfExists('extension_settings');
    }
};
