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
        Schema::create('chatbot_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('chatbot_id');
            $table->integer('conversation_id');
            $table->string('model')->nullable();
            $table->string('role')->nullable();
            $table->text('prompt')->nullable();
            $table->text('response')->nullable();
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('words')->nullable(); 
            $table->string('message_type')->default('text');
            $table->string('content_type')->default('text');
            $table->timestamp('read_at')->nullable();
            $table->text('media_url')->nullable();
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
        Schema::dropIfExists('chatbot_histories');
    }
};
