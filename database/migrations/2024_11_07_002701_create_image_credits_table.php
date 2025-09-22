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
        Schema::create('image_credits', function (Blueprint $table) {
            $table->id();
            $table->integer('sd_ultra')->nullable()->default(8);
            $table->integer('sd_core')->nullable()->default(3);
            $table->integer('sd_3_medium')->nullable()->default(3);
            $table->integer('sd_3_large')->nullable()->default(6);
            $table->integer('sd_3_large_turbo')->nullable()->default(4);
            $table->integer('sd_v16')->nullable()->default(1);
            $table->integer('sd_xl_v10')->nullable()->default(1);
            $table->integer('openai_dalle_3_hd')->nullable()->default(1);
            $table->integer('openai_dalle_3')->nullable()->default(1);
            $table->integer('openai_dalle_2')->nullable()->default(1);
            $table->integer('flux_pro')->nullable()->default(1);
            $table->integer('flux_dev')->nullable()->default(1);
            $table->integer('flux_schnell')->nullable()->default(1);
            $table->integer('flux_realism')->nullable()->default(1);
            $table->integer('pebblely_create_background')->nullable()->default(1);
            $table->integer('pebblely_remove_background')->nullable()->default(1);
            $table->integer('pebblely_upscale')->nullable()->default(1);
            $table->integer('pebblely_inpaint')->nullable()->default(1);
            $table->integer('pebblely_outpaint')->nullable()->default(1);
            $table->integer('pebblely_fashion')->nullable()->default(1);
            $table->integer('kling_15_video')->nullable()->default(1);
            $table->integer('haiper_2_video')->nullable()->default(1);
            $table->integer('minimax_video')->nullable()->default(1);
            $table->integer('mochi_1_video')->nullable()->default(1);
            $table->integer('luma_dream_machine_video')->nullable()->default(1);
            $table->integer('hunyuan_video')->nullable()->default(1);
            $table->integer('video_upscaler_video_video')->nullable()->default(1);
            $table->integer('cogvideox_5b_video_video')->nullable()->default(1);
            $table->integer('animatediff_video_video')->nullable()->default(1);
            $table->integer('fast_animatediff_video_video')->nullable()->default(1);
            $table->integer('kling_15_video_image')->nullable()->default(1);
            $table->integer('haiper_2_video_image')->nullable()->default(1);
            $table->integer('luma_dream_machine_video_image')->nullable()->default(1);
            $table->integer('stable_diffusion_video_image')->nullable()->default(1);
            $table->integer('midjourney_fast')->nullable()->default(1);
            $table->integer('midjourney_relax')->nullable()->default(1);
            $table->integer('midjourney_turbo')->nullable()->default(1);
            $table->integer('faceswap')->nullable()->default(1);
            $table->integer('music_stable')->nullable()->default(1);
            $table->integer('music_minimax')->nullable()->default(1);
            $table->integer('clipdrop')->nullable()->default(1);
            $table->integer('sd_photo_studio_reimagine')->default(3);
            $table->integer('sd_photo_studio_inpaint')->default(3);
            $table->integer('sd_photo_studio_search_replace')->default(4);
            $table->integer('sd_photo_studio_outpaint')->default(4);
            $table->integer('sd_photo_studio_erase_object')->default(3);
            $table->integer('sd_photo_studio_remove_background')->default(2);
            $table->integer('sd_photo_studio_structure')->default(3);
            $table->integer('sd_photo_studio_sketch')->default(3);
            $table->integer('sd_photo_studio_creative_upscaler')->default(25);
            $table->integer('sd_photo_studio_conservative_upscaler')->default(25);
            $table->integer('sd_photo_studio_text')->default(1);
            $table->integer('sd_photo_studio_style')->default(4);
            $table->integer('sd_photo_studio_3d')->default(1);  
            $table->integer('textract_text')->default(1);  
            $table->integer('textract_form')->default(1);  
            $table->integer('textract_table')->default(1);  
            $table->integer('textract_receipt')->default(1); 
            $table->integer('google_veo2_video_image')->nullable()->default(1);
            $table->decimal('elevenlabs_file_transcribe', 10, 3)->nullable()->default(1.0);
            $table->decimal('openai_live_transcribe', 10, 3)->nullable()->default(1.0);
            $table->integer('kling_21_standard_video_image')->nullable()->default(1);
            $table->integer('kling_21_pro_video_image')->nullable()->default(1);
            $table->integer('kling_21_master_video_image')->nullable()->default(1);
            $table->integer('google_veo3_video_image')->nullable()->default(1);
            $table->integer('google_veo3_video')->nullable()->default(1);
            $table->integer('google_veo2_video')->nullable()->default(1);
            $table->integer('kling_21_master_video')->nullable()->default(1);
             
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
        Schema::dropIfExists('image_credits');
    }
};
