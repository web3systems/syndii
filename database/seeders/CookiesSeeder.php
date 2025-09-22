<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CookieSetting;

class CookiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ads = [
            ['id' => 1, 'enable_cookies' => false, 'enable_dark_mode' => false, 'disable_page_interaction' => false, 'hide_from_bots' => true, 'consent_modal_layouts' => 'box wide', 'consent_modal_position' => 'bottom center', 'preferences_modal_layout' => 'box', 'preferences_modal_position' => 'right', 'days' => 7],
        ];

        foreach ($ads as $ad) {
            CookieSetting::updateOrCreate(['id' => $ad['id']], $ad);
        }
    }
}
