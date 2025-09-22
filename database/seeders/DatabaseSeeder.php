<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            AdsenseSeeder::class,
            CategorySeeder::class,
            PaymentPlatformsSeeder::class,
            LanguagesSeeder::class,
            TemplateSeeder::class,
            VoiceoverLanguagesSeeder::class,
            VoicesSeeder::class,
            VendorsSeeder::class,
            ChatsSeeder::class,
            FrontendStepSeeder::class,
            FrontendToolSeeder::class,
            FrontendFeatureSeeder::class,
            ChatCategorySeeder::class,
            ChatPromptSeeder::class,
            EmailsSeeder::class,
            IntegrationSeeder::class,
            APISeeder::class,
            SettingsSeeder::class,
            SDSeeder::class,
            PricesSeeder::class,
            SectionsSeeder::class,
            SEOSeeder::class,
            ExtensionSeeder::class,
            ImageCreditsSeeder::class,
            SocialMediaSeeder::class,
            ApiManagementSeeder::class,
            MenuSeeder::class,
            AzureModelSeeder::class,
            CookiesSeeder::class,
            ChatbotAvatarsSeeder::class,
        ]);
    }
}
