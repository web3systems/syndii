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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->decimal('price', 15, 2)->unsigned();
            $table->string('currency')->default('USD');
            $table->string('status')->default('active')->comment('active|closed');
            $table->string('templates')->nullable();
            $table->text('model')->nullable();
            $table->text('model_chat')->nullable();
            $table->string('chats')->nullable();
            $table->integer('max_tokens')->default(0);
            $table->string('payment_frequency')->nullable()->comment('monthly|yearly');
            $table->string('primary_heading')->nullable();
            $table->boolean('featured')->nullable()->default(0);
            $table->boolean('free')->nullable()->default(0);
            $table->boolean('image_feature')->nullable()->default(1);
            $table->longText('plan_features')->nullable();
            $table->integer('characters')->default(0);
            $table->integer('minutes')->default(0);
            $table->integer('image_storage_days')->default(0);
            $table->integer('voiceover_storage_days')->default(0);
            $table->integer('whisper_storage_days')->default(0);
            $table->boolean('voiceover_feature')->nullable()->default(1);
            $table->boolean('transcribe_feature')->nullable()->default(1);
            $table->boolean('code_feature')->nullable()->default(1);
            $table->boolean('chat_feature')->nullable()->default(1);
            $table->string('paypal_gateway_plan_id')->nullable();
            $table->string('stripe_gateway_plan_id')->nullable();
            $table->string('paystack_gateway_plan_id')->nullable();
            $table->string('razorpay_gateway_plan_id')->nullable();
            $table->string('flutterwave_gateway_plan_id')->nullable();
            $table->string('paddle_gateway_plan_id')->nullable();
            $table->integer('team_members')->nullable()->default(0);
            $table->boolean('personal_openai_api')->default(false)->nullable();
            $table->boolean('personal_claude_api')->default(false)->nullable();
            $table->boolean('personal_gemini_api')->default(false)->nullable();
            $table->boolean('personal_sd_api')->default(false)->nullable();
            $table->integer('days')->nullable();
            $table->boolean('wizard_feature')->nullable()->default(1);
            $table->boolean('vision_feature')->nullable()->default(1);
            $table->boolean('internet_feature')->nullable()->default(1);
            $table->boolean('chat_image_feature')->nullable()->default(1);
            $table->boolean('chat_web_feature')->nullable()->default(1);
            $table->float('chat_csv_file_size')->nullable()->default(1);
            $table->float('chat_pdf_file_size')->nullable()->default(1);
            $table->boolean('rewriter_feature')->nullable()->default(1);
            $table->boolean('smart_editor_feature')->nullable()->default(1);
            $table->boolean('file_chat_feature')->nullable()->default(1);
            $table->boolean('video_image_feature')->nullable()->default(0);
            $table->boolean('voice_clone_feature')->nullable()->default(0);
            $table->boolean('sound_studio_feature')->nullable()->default(0);
            $table->float('chat_word_file_size')->nullable()->default(1);
            $table->integer('voice_clone_number')->nullable()->default(0);
            $table->boolean('ai_detector_feature')->nullable()->default(0);
            $table->boolean('plagiarism_feature')->nullable()->default(0);
            $table->integer('plagiarism_pages')->nullable()->default(0);
            $table->integer('ai_detector_pages')->nullable()->default(0);
            $table->boolean('personal_chats_feature')->nullable()->default(0);
            $table->boolean('personal_templates_feature')->nullable()->default(0);
            $table->string('voiceover_vendors')->nullable();
            $table->boolean('brand_voice_feature')->nullable()->default(0);
            $table->integer('file_result_duration')->nullable()->default(-1);
            $table->integer('document_result_duration')->nullable()->default(-1);           
            $table->boolean('writer_feature')->nullable()->default(0);            
            $table->boolean('integration_feature')->nullable()->default(0);
            $table->boolean('photo_studio_feature')->nullable()->default(0);
            $table->boolean('youtube_feature')->nullable()->default(0);
            $table->boolean('rss_feature')->nullable()->default(0);           
            $table->integer('image_credits')->nullable()->default(0);
            $table->text('image_vendors')->nullable();
            $table->boolean('wordpress_feature')->nullable()->default(0);
            $table->integer('wordpress_website_number')->nullable()->default(0);
            $table->integer('wordpress_post_number')->nullable()->default(0);
            $table->boolean('product_photo_feature')->nullable()->default(0);
            $table->boolean('avatar_feature')->nullable()->default(0);
            $table->boolean('avatar_video_feature')->nullable()->default(0);
            $table->boolean('avatar_image_feature')->nullable()->default(0);
            $table->integer('avatar_video_numbers')->nullable()->default(0);
            $table->integer('avatar_image_numbers')->nullable()->default(0);
            $table->boolean('video_text_feature')->nullable()->default(0);
            $table->boolean('voice_isolator_feature')->nullable()->default(0);
            $table->boolean('social_media_feature')->nullable()->default(0);
            $table->boolean('video_video_feature')->nullable()->default(0);
            $table->boolean('faceswap_feature')->nullable()->default(0);
            $table->boolean('music_feature')->nullable()->default(0);
            $table->boolean('seo_feature')->nullable()->default(0);            
            $table->integer('token_credits')->nullable()->default(0);
            $table->boolean('chat_share_feature')->nullable()->default(0);
            $table->boolean('textract_feature')->nullable()->default(0);
            $table->boolean('chat_realtime_feature')->nullable()->default(0);
            $table->boolean('chatbot_external_feature')->nullable()->default(0);
            $table->integer('chatbot_external_quantity')->nullable()->default(0);
            $table->integer('chatbot_external_domains')->nullable()->default(0);
            $table->boolean('team_member_feature')->nullable()->default(0); 
            $table->boolean('speech_text_pro_feature')->nullable()->default(0); 
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
        Schema::dropIfExists('plans');
    }
};
