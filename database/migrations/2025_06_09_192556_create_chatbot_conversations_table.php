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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('chatbot_id');
            $table->string('session_id')->nullable();
            $table->string('conversation_name')->default('anonymous');
            $table->string('ip_address')->nullable();
            $table->string('domain_name')->nullable();
            $table->text('latest_message')->nullable();
            $table->integer('messages')->nullable();
            $table->string('chatbot_channel')->nullable();
            $table->string('chatbot_channel_id')->nullable();
            $table->string('customer_channel_id')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('agent_connected_at')->nullable();
            $table->longText('payload')->nullable();
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
        Schema::dropIfExists('chatbot_conversations');
    }
};
