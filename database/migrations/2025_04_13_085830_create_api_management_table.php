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
        Schema::create('api_management', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('vendor_model');
            $table->string('model');
            $table->boolean('new')->nullable()->default(false);            
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->decimal('input_token', 10, 3)->nullable()->default(1.0);
            $table->decimal('output_token', 10, 3)->nullable()->default(1.0);
            $table->text('logo')->nullable();
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
        Schema::dropIfExists('api_management');
    }
};
