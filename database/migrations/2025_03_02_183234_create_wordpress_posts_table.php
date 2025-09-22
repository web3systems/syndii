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
        Schema::create('wordpress_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('website_id')->nullable()->constrained('user_integrations')->onDelete('set null');
            $table->string('website_name')->nullable();
            $table->string('platform')->default('wordpress');
            $table->string('title');
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('slug')->nullable();
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('post_status')->nullable();
            $table->enum('status', ['scheduled', 'published', 'failed', 'cancelled'])->default('scheduled');
            $table->string('scheduled_at')->nullable();
            $table->string('published_at')->nullable();
            $table->string('post_id')->nullable();
            $table->string('post_url')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('wordpress_posts');
    }
};
