<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Integration;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = [
            ['id' => 1, 'app' => 'wordpress', 'slug' => 'wordpress', 'logo' => 'img/csp/wordpress.png', 'description' => 'Wordpress Integration', 'status' => true, 'fields' => '[{"title":"WP Website Name","name":"domain"},{"title":"WP Website URL","name":"url"}, {"title":"Username","name":"username"},{"title":"Password","name":"password"}]'],
        ];

        foreach ($vendors as $vendor) {
            Integration::updateOrCreate(['id' => $vendor['id']], $vendor);
        }
    }
}
