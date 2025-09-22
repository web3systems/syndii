<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialMediaSetting;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ads = [
            ['id' => 1],
        ];

        foreach ($ads as $ad) {
            SocialMediaSetting::updateOrCreate(['id' => $ad['id']], $ad);
        }
    }
}
