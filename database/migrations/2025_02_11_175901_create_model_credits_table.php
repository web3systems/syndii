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
        Schema::create('model_credits', function (Blueprint $table) {
            $table->id();
            $table->integer('openai_fine_tune')->nullable()->default(0);
            $table->integer('gpt_35_turbo')->nullable()->default(0);
            $table->integer('gpt_4_turbo')->nullable()->default(0);
            $table->integer('gpt_4')->nullable()->default(0);
            $table->integer('gpt_4o')->nullable()->default(0);
            $table->integer('gpt_4o_mini')->nullable()->default(0);          
            $table->integer('o1_mini')->nullable()->default(0);
            $table->integer('o1')->nullable()->default(0);
            $table->integer('o3_mini')->nullable()->default(0);
            $table->integer('gemini_15_pro')->nullable()->default(0);
            $table->integer('gemini_15_flash')->nullable()->default(0);
            $table->integer('gemini_20_flash')->nullable()->default(0);
            $table->integer('claude_35_haiku')->nullable()->default(0);
            $table->integer('claude_35_sonnet')->nullable()->default(0);
            $table->integer('claude_3_opus')->nullable()->default(0);
            $table->integer('deepseek_r1')->nullable()->default(0);
            $table->integer('deepseek_v3')->nullable()->default(0);
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
        Schema::dropIfExists('model_credits');
    }
};

