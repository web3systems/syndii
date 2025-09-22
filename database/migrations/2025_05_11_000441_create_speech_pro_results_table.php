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
        Schema::create('speech_pro_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('model')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_extension')->nullable();
            $table->float('file_size')->default(0)->nullable();
            $table->float('file_length')->default(0)->nullable();
            $table->string('file_type')->nullable();
            $table->text('file_url')->nullable();
            $table->longText('text')->nullable();
            $table->longText('transcript')->nullable();
            $table->longText('raw')->nullable();
            $table->string('language')->nullable();
            $table->string('task_type')->nullable();            
            $table->integer('credits')->nullable();
            $table->boolean('status')->default(1);
            $table->longText('export_files')->nullable();
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
        Schema::dropIfExists('speech_pro_results');
    }
};
