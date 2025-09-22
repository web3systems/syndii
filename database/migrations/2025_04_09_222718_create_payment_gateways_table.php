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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('prepaid_plans')->default(false);
            $table->boolean('subscription_plans')->default(false);
            $table->string('live_api_key')->nullable();
            $table->string('live_api_secret')->nullable();
            $table->string('sandbox_api_key')->nullable();
            $table->string('sandbox_api_secret')->nullable();
            $table->string('base_url')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->boolean('sandbox')->default(false);
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
        Schema::dropIfExists('payment_gateways');
    }
};
