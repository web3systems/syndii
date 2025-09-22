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
        Schema::create('textract_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('language')->nullable();
            $table->longText('text')->nullable();
            $table->longText('raw')->nullable();
            $table->string('project')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->string('format')->nullable();
            $table->string('task_id')->nullable();
            $table->string('plan_type')->comment('free|paid');
            $table->string('status')->nullable();            
            $table->integer('text_credits')->default(0);
            $table->integer('form_credits')->default(0);
            $table->integer('table_credits')->default(0);
            $table->integer('receipt_credits')->default(0);
            $table->integer('pages')->default(0);
            $table->string('type')->nullable()->comment('text|form|table|receipt');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('textract_results');
    }
};
