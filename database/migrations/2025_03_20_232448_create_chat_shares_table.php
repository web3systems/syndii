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
        Schema::create('chat_shares', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->string('chat_code')->nullable();
            $table->string('conversation_id')->nullable();
            $table->boolean('read_only')->default(false);
            $table->string('availability')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('chat_shares');
    }
};
