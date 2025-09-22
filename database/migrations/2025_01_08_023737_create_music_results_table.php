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
        Schema::create('music_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('model')->nullable();
            $table->string('title')->nullable();
            $table->string('file_name')->nullable();
            $table->text('prompt')->nullable();
            $table->integer('steps')->nullable()->default(1);
            $table->integer('seconds_total')->nullable()->default(1);
            $table->text('reference_audio_url')->nullable();
            $table->text('result_url')->nullable();
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
        Schema::dropIfExists('music_results');
    }
};
