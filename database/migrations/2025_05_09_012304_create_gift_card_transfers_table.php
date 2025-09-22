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
        Schema::create('gift_card_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sender_user_id')->unsigned();
            $table->string('sender_username');
            $table->string('sender_email');
            $table->bigInteger('receiver_user_id')->unsigned();
            $table->string('receiver_username');
            $table->string('receiver_email');
            $table->string('transfer_id');
            $table->decimal('amount', 15, 2)->unsigned();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('gift_card_transfers');
    }
};
