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
        Schema::create('chatbot_embeddings', function (Blueprint $table) {
            $table->id();
            $table->integer('chatbot_id');
            $table->string('engine');
            $table->string('type')->default('text');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->nullable();
            $table->longText('content')->nullable();
            $table->longText('embedding')->nullable();
            $table->timestamp('trained_at')->nullable();
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
        Schema::dropIfExists('chatbot_embeddings');
    }
};
