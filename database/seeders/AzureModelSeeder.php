<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AzureModel;

class AzureModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = [
            ['id' => 1, 'model' => 'gpt-3.5-turbo-0125', 'deployment_name' => 'gpt-35-turbo'],
            ['id' => 2, 'model' => 'gpt-4', 'deployment_name' => 'gpt-4'],
            ['id' => 3, 'model' => 'gpt-4o', 'deployment_name' => 'gpt-4o'],
            ['id' => 4, 'model' => 'gpt-4o-mini', 'deployment_name' => 'gpt-4o-mini'],
            ['id' => 5, 'model' => 'gpt-4-0125-preview', 'deployment_name' => 'gpt-4-0125-preview'],
            ['id' => 6, 'model' => 'gpt-4.5-preview', 'deployment_name' => 'gpt-4.5-preview'],
            ['id' => 7, 'model' => 'o1', 'deployment_name' => 'o1'],
            ['id' => 8, 'model' => 'o1-mini', 'deployment_name' => 'o1-mini'],
            ['id' => 9, 'model' => 'o3-mini', 'deployment_name' => 'o3-mini']
        ];

        foreach ($vendors as $vendor) {
            AzureModel::updateOrCreate(['id' => $vendor['id']], $vendor);
        }
    }
}
