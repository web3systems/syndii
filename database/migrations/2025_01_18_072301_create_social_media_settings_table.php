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
        Schema::create('social_media_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('twitter')->nullable()->default(false);
            $table->string('twitter_api_key')->nullable();
            $table->string('twitter_api_secret')->nullable();
            $table->string('twitter_access_token')->nullable();
            $table->string('twitter_access_token_secret')->nullable();
            $table->string('twitter_client_id')->nullable();
            $table->string('twitter_client_secret')->nullable();
            $table->string('twitter_callback_url')->nullable();
            $table->string('twitter_app_version')->nullable()->default(2);

            $table->boolean('linkedin')->default(false);
            $table->string('linkedin_client_id')->nullable();
            $table->string('linkedin_client_secret')->nullable();
            $table->string('linkedin_callback_url')->nullable();

            $table->boolean('facebook')->default(false);
            $table->string('facebook_client_id')->nullable();
            $table->string('facebook_client_secret')->nullable();
            $table->string('facebook_callback_url')->nullable();

            $table->boolean('instagram')->default(false);
            $table->string('instagram_client_id')->nullable();
            $table->string('instagram_client_secret')->nullable();
            $table->string('instagram_callback_url')->nullable();

            $table->boolean('tiktok')->default(false);
            $table->string('tiktok_app_id')->nullable();
            $table->string('tiktok_app_key')->nullable();
            $table->string('tiktok_app_secret')->nullable();
            $table->string('tiktok_callback_url')->nullable();
            $table->string('tiktok_verification_file')->nullable();

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
        Schema::dropIfExists('social_media_settings');
    }
};
