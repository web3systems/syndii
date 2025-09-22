<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatbotAvatar;

class ChatbotAvatarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ads = [
            ['id' => 1, 'user_id' => 1, 'avatar' => 'chatbots/bots/1.webp', 'public' => true],
            ['id' => 2, 'user_id' => 1, 'avatar' => 'chatbots/bots/2.webp', 'public' => true],
            ['id' => 3, 'user_id' => 1, 'avatar' => 'chatbots/bots/3.webp', 'public' => true],
            ['id' => 4, 'user_id' => 1, 'avatar' => 'chatbots/bots/4.jpg', 'public' => true],
            ['id' => 5, 'user_id' => 1, 'avatar' => 'chatbots/bots/5.webp', 'public' => true],
            ['id' => 6, 'user_id' => 1, 'avatar' => 'chatbots/bots/6.png', 'public' => true],

        ];

        foreach ($ads as $ad) {
            ChatbotAvatar::updateOrCreate(['id' => $ad['id']], $ad);
        }
    }
}
