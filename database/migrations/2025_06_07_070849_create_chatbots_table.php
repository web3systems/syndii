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
        Schema::create('chatbots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('uuid');
            $table->string('chatbot_name')->nullable();
            $table->boolean('hide_chatbot_name')->default(false); 
            $table->string('chatbot_title')->nullable();
            $table->longText('instructions')->nullable();
            $table->boolean('instruction_restriction')->default(false); 
            $table->boolean('fallback_message')->default(false); 
            $table->text('custom_message')->nullable();
            $table->string('greeting_message')->nullable();
            $table->string('bubble_message')->nullable();
            $table->boolean('hide_bubble_message')->default(false); 
            $table->string('message_placeholder')->nullable(); 
            $table->string('model')->nullable('gpt-4o-mini'); 
            $table->string('embedding_model')->nullable('text-embedding-3-small'); 
            $table->string('interaction_type')->nullable('ai'); 
            $table->string('language')->nullable('auto'); 
            $table->string('main_header_logo')->nullable();
            $table->boolean('hide_main_header_logo')->default(false);
            $table->string('ai_avatar_logo')->nullable(); 
            $table->boolean('hide_ai_avatar')->default(false); 
            $table->boolean('hide_footer_brand')->default(false); 
            $table->boolean('show_voting')->default(false); 
            $table->boolean('hide_message_time')->default(false); 
            $table->string('footer_link')->nullable(); 
            $table->string('widget_position')->default('right');
            $table->string('header_bg_color')->default('#1e1e2d');
            $table->string('header_text_color')->default('#ffffff');
            $table->string('ai_text_color')->default('#1e1e2d');
            $table->string('ai_bg_color')->default('#f5faff');
            $table->string('user_bg_color')->default('#1e1e2d');
            $table->string('user_text_color')->default('#ffffff');
            $table->boolean('show_pre_defined_questions')->default(false); 
            $table->longText('pre_defined_questions')->nullable();
            $table->integer('limit_per_minute')->default(1000);
            $table->boolean('active')->default(true); 
            $table->boolean('favorite')->default(false); 
            $table->string('trigger_size')->default('60px');
            $table->string('panel_width')->default('440px');
            $table->string('panel_height')->default('700px');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatbots');
    }
};
