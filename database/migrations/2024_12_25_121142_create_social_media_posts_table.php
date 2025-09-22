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
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->string('post_type')->nullable()->default('text');
            $table->longText('post_text')->nullable();
            $table->string('target_platform_id')->nullable();
            $table->string('target_platform')->nullable();
            $table->string('media_name')->nullable();
            $table->string('media_original_name')->nullable();
            $table->string('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->string('schedule_type')->nullable()->default('immediately');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('repost_interval')->nullable()->default(30);
            $table->string('repost_days')->nullable()->default('all');
            $table->string('status')->nullable();
            $table->boolean('published')->nullable()->default(false);
            $table->boolean('draft')->nullable()->default(false);
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
        Schema::dropIfExists('social_media_posts');
    }
};
