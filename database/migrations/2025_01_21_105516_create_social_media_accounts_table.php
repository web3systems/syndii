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
        Schema::create('social_media_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('platform_id')->nullable();
            $table->string('platform');
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->text('picture')->nullable();
            $table->text('access_token');
            $table->dateTime('access_token_expire_at')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('refresh_token_expire_at')->nullable();
            $table->dateTime('failed_mail_send_at')->nullable(); 
            $table->string('type')->nullable();
            $table->text('metadata')->nullable();
            $table->boolean('status')->nullable()->default(true);
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
        Schema::dropIfExists('social_media_accounts');
    }
};
