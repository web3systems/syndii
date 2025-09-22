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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_key')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('key')->nullable();
            $table->string('route')->nullable();
            $table->string('route_slug')->nullable();
            $table->string('label')->nullable();
            $table->string('icon')->nullable();
            $table->string('type')->default('item'); // item, divider, label, group
            $table->longText('svg')->nullable();   
            $table->boolean('is_active')->default(true); 
            $table->boolean('is_admin')->default(false);  
            $table->boolean('extension')->default(false);      
            $table->boolean('original')->default(false);      
            $table->string('url')->nullable();
            $table->string('permission')->nullable();          
            $table->json('conditions')->nullable(); // Store show/hide conditions
            $table->json('children')->nullable(); // Store show/hide conditions
            $table->string('badge_text')->nullable();
            $table->string('badge_type')->nullable(); // warning, danger, etc
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
        Schema::dropIfExists('menu_items');
    }
};
