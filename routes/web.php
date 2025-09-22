<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDavinciController;
use App\Http\Controllers\Admin\DavinciConfigController;
use App\Http\Controllers\Admin\CustomTemplateController;
use App\Http\Controllers\Admin\VoiceCustomizationController;
use App\Http\Controllers\Admin\ChatCustomizationController;
use App\Http\Controllers\Admin\ChatAssistantController;
use App\Http\Controllers\Admin\ChatTrainingController;
use App\Http\Controllers\Admin\ImagePromptController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\EmailNotificationController;
use App\Http\Controllers\Admin\InstallController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Admin\Frontend\AppearanceController;
use App\Http\Controllers\Admin\Frontend\SEOManagerController;
use App\Http\Controllers\Admin\Frontend\FrontendSettingController;
use App\Http\Controllers\Admin\Frontend\FrontendSectionSettingController;
use App\Http\Controllers\Admin\Frontend\FrontendSectionController;
use App\Http\Controllers\Admin\Frontend\BlogController;
use App\Http\Controllers\Admin\Frontend\PageController;
use App\Http\Controllers\Admin\Frontend\FAQController;
use App\Http\Controllers\Admin\Frontend\ReviewController;
use App\Http\Controllers\Admin\Frontend\AdsenseController;
use App\Http\Controllers\Admin\Settings\GlobalController;
use App\Http\Controllers\Admin\Settings\OAuthController;
use App\Http\Controllers\Admin\Settings\ActivationController;
use App\Http\Controllers\Admin\Settings\LanguageController;
use App\Http\Controllers\Admin\Settings\SMTPController;
use App\Http\Controllers\Admin\Settings\RegistrationController;
use App\Http\Controllers\Admin\Settings\UpgradeController;
use App\Http\Controllers\Admin\Settings\SystemController;
use App\Http\Controllers\Admin\Settings\GDPRController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\TeamController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserPasswordController;
use App\Http\Controllers\User\TemplateController;
use App\Http\Controllers\User\SmartEditorController;
use App\Http\Controllers\User\RewriterController;
use App\Http\Controllers\User\ImageController;
use App\Http\Controllers\User\CodeController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\EmbeddingController;
use App\Http\Controllers\User\EmbeddingFileController;
use App\Http\Controllers\User\ArticleWizardController;
use App\Http\Controllers\User\VisionController;
use App\Http\Controllers\User\ChatImageController;
use App\Http\Controllers\User\ChatFileController;
use App\Http\Controllers\User\ChatWebController;
use App\Http\Controllers\User\TranscribeController;
use App\Http\Controllers\User\VoiceoverController;
use App\Http\Controllers\User\UserCustomTemplateController;
use App\Http\Controllers\User\UserCustomChatController;
use App\Http\Controllers\User\BrandVoiceController;
use App\Http\Controllers\User\IntegrationController;
use App\Http\Controllers\User\YoutubeController;
use App\Http\Controllers\User\RSSController;
use App\Http\Controllers\User\WorkbookController;
use App\Http\Controllers\User\DocumentController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\UserSupportController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\SearchController;
use App\Services\StripeService;
use App\Services\StripeMarketplace;
use Illuminate\Support\Facades\Artisan;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Elseyyid\LaravelJsonLocationsManager\Controllers\HomeController as ElseyyidController;
use App\Models\MainSetting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now reate something great!
|
*/

// AUTH ROUTES
Route::middleware(['middleware' => 'PreventBackHistory'])->group(function () {
    require __DIR__.'/auth.php';
});

// FRONTEND ROUTES
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/blog/{slug}', 'blogShow')->name('blogs.show');
        Route::get('/contact', 'contactShow')->name('contact');
        Route::post('/contact', 'contactSend')->name('contact.send');
        Route::get('/page/{slug}', 'showPage');
        Route::get('/unsubscribe', 'showUnsubscribe')->name('email.unsubscribe.show');
        Route::post('/unsubscribe/process/{email}', 'unsubscribe')->name('email.unsubscribe.process');
    });
});

// INSTALL ROUTES
Route::group(['prefix' => 'install', 'middleware' => 'install'], function() {
    Route::controller(InstallController::class)->group(function () {
        Route::get('/', 'index')->name('install');
        Route::get('/requirements', 'requirements')->name('install.requirements');
        Route::get('/permissions', 'permissions')->name('install.permissions');
        Route::get('/database', 'database')->name('install.database');    
        Route::post('/database', 'storeDatabaseCredentials')->name('install.database.store');
        Route::get('/activation', 'activation')->name('install.activation');    
        Route::post('/activation', 'activateApplication')->name('install.activation.activate');
    });
});

Route::controller(ActivationController::class)->group(function() {
    Route::get('/update/v6/download', 'showDownload')->name('download.update');
    Route::post('/update/v6/download', 'storeKey')->name('download.update.store');
});

Route::controller(ChatController::class)->group(function() {
    Route::get('/app/chat/share/{uuid}', 'showChatShare')->name('app.chat.share');
    Route::post('/app/chat/share/process', 'processChatShare');
    Route::get('/app/chat/share', 'generateChatShare');
    Route::post('/app/chat/history', 'sharedHistory'); 
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    // ADMIN ROUTES
    Route::group(['prefix' => 'app'], function() {
        Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {

            // UPDATE ROUTE
            Route::get('/update/now', [UpdateController::class, 'updateDatabase']);
    
            // ADMIN DASHBOARD ROUTES
            Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
            Route::get('/dashboard/analytics', [AdminController::class, 'analytics']);
            Route::get('/dashboard/check-update', [AdminController::class, 'checkUpdate']);
    
            // ADMIN DAVINCI MANAGEMENT ROUTES
            Route::controller(AdminDavinciController::class)->group(function() {
                Route::get('/davinci/dashboard', 'index')->name('admin.davinci.dashboard'); 
                Route::get('/davinci/templates', 'templates')->name('admin.davinci.templates');
                Route::post('/davinci/templates/template/update', 'descriptionUpdate');  
                Route::post('/davinci/templates/template/activate', 'templateActivate');   
                Route::post('/davinci/templates/template/deactivate', 'templateDeactivate');  
                Route::post('/davinci/templates/template/changepackage', 'assignPackage');  
                Route::post('/davinci/templates/template/setnew', 'setNew');  
                Route::post('/davinci/templates/template/delete', 'deleteTemplate');  
                Route::get('/davinci/templates/activate/all', 'templateActivateAll'); 
                Route::get('/davinci/templates/deactivate/all', 'templateDeactivateAll'); 
            }); 
            
            // ADMIN DAVINCI CONFIGURATION ROUTES
            Route::controller(DavinciConfigController::class)->group(function() {
                Route::get('/davinci/configs', 'index')->name('admin.davinci.configs');
                Route::post('/davinci/configs', 'store')->name('admin.davinci.configs.store');
                Route::post('/davinci/configs/trial', 'storeTrial')->name('admin.davinci.configs.store.trial');
                Route::get('/davinci/configs/keys', 'showKeys')->name('admin.davinci.configs.keys');
                Route::get('/davinci/configs/keys/create', 'createKeys')->name('admin.davinci.configs.keys.create');
                Route::post('/davinci/configs/keys/store', 'storeKeys')->name('admin.davinci.configs.keys.store');              
                Route::get('/davinci/configs/fine-tune', 'showFineTune')->name('admin.davinci.configs.fine-tune');
                Route::post('/davinci/configs/fine-tune', 'createFineTune')->name('admin.davinci.configs.fine-tune.create');
                Route::get('/davinci/configs/images', 'showImageCredits')->name('admin.davinci.configs.image.credits');
                Route::post('/davinci/configs/images/store', 'storeImageCredits')->name('admin.davinci.configs.image.credits.store');
                Route::post('/davinci/configs/fine-tune/delete', 'deleteFineTune');
                Route::post('/davinci/configs/keys/update', 'update');
                Route::post('/davinci/configs/keys/activate', 'activate');
                Route::post('/davinci/configs/keys/delete', 'delete');
                Route::get('/davinci/configs/api/openai', 'showAPIOpenai');
                Route::post('/davinci/configs/api/openai', 'storeAPIOpenai')->name('admin.davinci.configs.api.openai.store');
                Route::get('/davinci/configs/api/anthropic', 'showAPIAnthropic');
                Route::post('/davinci/configs/api/anthropic', 'storeAPIAnthropic')->name('admin.davinci.configs.api.anthropic.store');
                Route::get('/davinci/configs/api/google', 'showAPIGoogle');
                Route::post('/davinci/configs/api/google', 'storeAPIGoogle')->name('admin.davinci.configs.api.google.store');
                Route::get('/davinci/configs/api/stablediffusion', 'showAPIStability');
                Route::post('/davinci/configs/api/stablediffusion', 'storeAPIStability')->name('admin.davinci.configs.api.stability.store');
                Route::get('/davinci/configs/api/azure', 'showAPIAzure');
                Route::post('/davinci/configs/api/azure', 'storeAPIAzure')->name('admin.davinci.configs.api.azure.store');
                Route::get('/davinci/configs/api/elevenlabs', 'showAPIElevenlabs');
                Route::post('/davinci/configs/api/elevenlabs', 'storeAPIElevenlabs')->name('admin.davinci.configs.api.elevenlabs.store');
                Route::get('/davinci/configs/api/aws', 'showAPIAWS');
                Route::post('/davinci/configs/api/aws', 'storeAPIAWS')->name('admin.davinci.configs.api.aws.store');
                Route::get('/davinci/configs/api/storj', 'showAPIStorj');
                Route::post('/davinci/configs/api/storj', 'storeAPIStorj')->name('admin.davinci.configs.api.storj.store');
                Route::get('/davinci/configs/api/dropbox', 'showAPIDropbox');
                Route::post('/davinci/configs/api/dropbox', 'storeAPIDropbox')->name('admin.davinci.configs.api.dropbox.store');
                Route::get('/davinci/configs/api/wasabi', 'showAPIWasabi');
                Route::post('/davinci/configs/api/wasabi', 'storeAPIWasabi')->name('admin.davinci.configs.api.wasabi.store');
                Route::get('/davinci/configs/api/cloudflare', 'showAPICloudflare');
                Route::post('/davinci/configs/api/cloudflare', 'storeAPICloudflare')->name('admin.davinci.configs.api.cloudflare.store');
                Route::get('/davinci/configs/api/serper', 'showAPISerper');
                Route::post('/davinci/configs/api/serper', 'storeAPISerper')->name('admin.davinci.configs.api.serper.store');
                Route::get('/davinci/configs/api/youtube', 'showAPIYoutube');
                Route::post('/davinci/configs/api/youtube', 'storeAPIYoutube')->name('admin.davinci.configs.api.youtube.store');
                Route::get('/davinci/configs/api/credit', 'showAPICredit');
                Route::post('/davinci/configs/api/credit', 'storeAPICredit')->name('admin.davinci.configs.api.credit.store');
                Route::get('/davinci/configs/api/deepseek', 'showAPIDeepseek');
                Route::post('/davinci/configs/api/deepseek', 'storeAPIDeepseek')->name('admin.davinci.configs.api.deepseek.store');
                Route::get('/davinci/configs/api/xai', 'showAPIxAI');
                Route::post('/davinci/configs/api/xai', 'storeAPIxAI')->name('admin.davinci.configs.api.xai.store');
            }); 
    
            // ADMIN DAVINCI CUSTOM TEMPLATES ROUTES
            Route::controller(CustomTemplateController::class)->group(function() {
                Route::get('/davinci/custom', 'index')->name('admin.davinci.custom');
                Route::get('/davinci/create', 'createTemplate')->name('admin.davinci.custom.create');
                Route::post('/davinci/custom', 'store')->name('admin.davinci.custom.store');
                Route::get('/davinci/custom/{id}/show', 'show')->name('admin.davinci.custom.show');
                Route::put('/davinci/custom/{id}/update', 'update')->name('admin.davinci.custom.update');
                Route::get('/davinci/custom/category', 'category')->name('admin.davinci.custom.category');
                Route::post('/davinci/custom/category/change', 'change');
                Route::post('/davinci/custom/category/description', 'description');
                Route::post('/davinci/custom/category/create', 'create');
                Route::post('/davinci/custom/category/delete', 'delete');
            }); 
    
            // ADMIN VOICEOVER VOICE CUSTOMIZATION ROUTES
            Route::controller(VoiceCustomizationController::class)->group(function() {
                Route::get('/text-to-speech/voices', 'voices')->name('admin.davinci.voices');  
                Route::post('/text-to-speech/voices/avatar/upload', 'changeAvatar'); 
                Route::post('/text-to-speech/voice/update', 'voiceUpdate');  
                Route::post('/text-to-speech/voices/voice/activate', 'voiceActivate');  
                Route::post('/text-to-speech/voices/voice/deactivate', 'voiceDeactivate');    
                Route::get('/text-to-speech/voices/activate/all', 'voicesActivateAll');  
                Route::get('/text-to-speech/voices/deactivate/all', 'voicesDeactivateAll'); 
            });
    
            // ADMIN AI CHAT CUSTOMIZATION ROUTES
            Route::controller(ChatCustomizationController::class)->group(function() {
                Route::get('/chats', 'chats')->name('admin.davinci.chats');  
                Route::post('/chats/avatar/upload', 'changeAvatar'); 
                Route::post('/chats/update', 'chatUpdate');  
                Route::post('/chats/chat/activate', 'chatActivate');  
                Route::post('/chats/chat/deactivate', 'chatDeactivate');  
                Route::get('/chats/chat/create', 'create')->name('admin.davinci.chat.create');  
                Route::post('/chats/chat/store', 'store')->name('admin.davinci.chat.store');  
                Route::get('/chats/chat/{id}/edit', 'edit')->name('admin.davinci.chat.edit');  
                Route::put('/chats/chat/{id}/update', 'update')->name('admin.davinci.chat.update');  
                Route::get('/chats/chat/category', 'category')->name('admin.davinci.chat.category');  
                Route::post('/chats/chat/category/change', 'change');
                Route::post('/chats/chat/category/create', 'createCategory');
                Route::post('/chats/chat/category/delete', 'delete');
                Route::get('/chats/chat/prompt', 'prompt')->name('admin.davinci.chat.prompt'); 
                Route::get('/chats/chat/prompt/create', 'promptCreate')->name('admin.davinci.chat.prompt.create');  
                Route::post('/chats/chat/prompt/store', 'promptStore')->name('admin.davinci.chat.prompt.store'); 
                Route::get('/chats/chat/prompt/{id}/edit', 'promptEdit')->name('admin.davinci.chat.prompt.edit');  
                Route::put('/chats/chat/prompt/{id}/update', 'promptUpdate')->name('admin.davinci.chat.prompt.update'); 
                Route::post('/chats/chat/prompt/activate', 'promptActivate');  
                Route::post('/chats/chat/prompt/deactivate', 'promptDeactivate'); 
                Route::post('/chats/chat/prompt/delete', 'promptDelete');
            });
    
            // ADMIN CHAT ASSISTANT ROUTES
            Route::controller(ChatAssistantController::class)->group(function() {
                Route::get('/chat/assistant', 'index')->name('admin.chat.assistant');
                Route::post('/chat/assistant', 'store')->name('admin.chat.assistant.store');
                Route::get('/chat/assistant/create', 'create')->name('admin.chat.assistant.create');
                Route::get('/chat/assistant/{id}/show', 'show')->name('admin.chat.assistant.show');
                Route::get('/chat/assistant/files', 'files')->name('admin.chat.assistant.files');
                Route::put('/chat/assistant/{id}/update', 'update')->name('admin.chat.assistant.update'); 
                Route::post('/chat/assistant/activate', 'chatActivate');   
                Route::post('/chat/assistant/deactivate', 'chatDeactivate');  
                Route::post('/chat/assistant/delete', 'chatDelete'); 
                Route::post('/chat/assistant/file/delete', 'fileDelete'); 
            });
    
            // ADMIN CHAT TRAINING ROUTES
            Route::controller(ChatTrainingController::class)->group(function() {
                Route::get('/chat/training', 'index')->name('admin.chat.training');
                Route::post('/chat/training', 'store')->name('admin.chat.training.store');
                Route::get('/chat/training/{id}/show', 'show')->name('admin.chat.training.show');
                Route::put('/chat/training/{id}/update', 'update')->name('admin.chat.training.update'); 
                Route::post('/chat/training/activate', 'chatActivate');   
                Route::post('/chat/training/deactivate', 'chatDeactivate');  
                Route::post('/chat/training/delete', 'chatDelete'); 
            });
    
            // ADMIN IMAGE PROMPT ROUTES
            Route::controller(ImagePromptController::class)->group(function() {
                Route::get('/image/prompt', 'prompt')->name('admin.davinci.image.prompt'); 
                Route::get('/image/prompt/create', 'promptCreate')->name('admin.davinci.image.prompt.create');  
                Route::post('/image/prompt/store', 'promptStore')->name('admin.davinci.image.prompt.store'); 
                Route::get('/image/prompt/{id}/edit', 'promptEdit')->name('admin.davinci.image.prompt.edit');  
                Route::put('/image/prompt/{id}/update', 'promptUpdate')->name('admin.davinci.image.prompt.update'); 
                Route::post('/image/prompt/activate', 'promptActivate');  
                Route::post('/image/prompt/deactivate', 'promptDeactivate'); 
                Route::post('/image/prompt/delete', 'promptDelete');
            });
    
            // ADMIN USER MANAGEMENT ROUTES
            Route::controller(AdminUserController::class)->group(function() {
                Route::get('/users/dashboard', 'index')->name('admin.user.dashboard');
                Route::get('/users/activity', 'activity')->name('admin.user.activity');
                Route::get('/users/list', 'listUsers')->name('admin.user.list');        
                Route::post('/users', 'store')->name('admin.user.store');
                Route::get('/users/create', 'create')->name('admin.user.create');        
                Route::get('/users/{user}/show', 'show')->name('admin.user.show');
                Route::get('/users/{user}/edit', 'edit')->name('admin.user.edit');
                Route::get('/users/{user}/credit', 'credit')->name('admin.user.credit');
                Route::get('/users/{user}/subscription', 'subscription')->name('admin.user.subscription');
                Route::post('/users/{user}/assign', 'assignSubscription')->name('admin.user.assign');
                Route::post('/users/{user}/increase', 'increase')->name('admin.user.increase');
                Route::put('/users/{user}/update', 'update')->name('admin.user.update');
                Route::put('/users/{user}', 'change')->name('admin.user.change');       
                Route::post('/users/delete', 'delete');
                Route::post('/users/plan', 'hiddenPlans');
            });            
    
            // ADMIN SUPPORT ROUTES
            Route::controller(SupportController::class)->group(function() {
                Route::get('/support', 'index')->name('admin.support');
                Route::get('/support/{ticket_id}/show', 'show')->name('admin.support.show');        
                Route::post('/support/response', 'response')->name('admin.support.response');
                Route::post('/support/delete', 'delete');
            });
    
            // ADMIN NOTIFICATION ROUTES
            Route::controller(NotificationController::class)->group(function() {
                Route::get('/notifications', 'index')->name('admin.notifications');
                Route::get('/notifications/sytem', 'system')->name('admin.notifications.system');
                Route::get('/notifications/create', 'create')->name('admin.notifications.create');
                Route::post('/notifications', 'store')->name('admin.notifications.store');
                Route::get('/notifications/{id}/show', 'show')->name('admin.notifications.show');
                Route::get('/notifications/system/{id}/show', 'systemShow')->name('admin.notifications.systemShow');
                Route::get('/notifications/mark-all', 'markAllRead')->name('admin.notifications.markAllRead');
                Route::get('/notifications/delete-all', 'deleteAll')->name('admin.notifications.deleteAll');
                Route::post('/notifications/delete', 'delete'); 
            });
    
            // ADMIN EMAIL TEMPLATES ROUTES
            Route::controller(EmailNotificationController::class)->group(function() {
                Route::get('/email/templates', 'templates')->name('admin.email.templates');
                Route::get('/email/templates/{id}/edit', 'editTemplate')->name('admin.email.templates.edit');
                Route::put('/email/templates/{id}', 'updateTemplate')->name('admin.email.templates.update'); 
                Route::get('/email/newsletter', 'newsletter')->name('admin.email.newsletter'); 
                Route::post('/email/newsletter', 'store')->name('admin.email.newsletter.store');
                Route::get('/email/newsletter/create', 'create')->name('admin.email.newsletter.create');
                Route::get('/email/newsletter/{id}/view', 'view')->name('admin.email.newsletter.view');        
                Route::get('/email/newsletter/{id}/edit', 'editEmail')->name('admin.email.newsletter.edit');
                Route::put('/email/newsletter/{id}', 'updateEmail')->name('admin.email.newsletter.update');      
                Route::post('/email/newsletter/delete', 'delete'); 
                Route::post('/email/newsletter/send', 'send');
            });
            
            // ADMIN GENERAL SETTINGS - GLOBAL SETTINGS
            Route::controller(GlobalController::class)->group(function() {
                Route::get('/settings/global', 'index')->name('admin.settings.global');
                Route::post('/settings/global', 'store')->name('admin.settings.global.store');
            });
    
            // ADMIN GENERAL SETTINGS - SMTP SETTINGS
            Route::controller(SMTPController::class)->group(function() {
                Route::post('/settings/smtp/test', 'test')->name('admin.settings.smtp.test');
                Route::get('/settings/smtp', 'index')->name('admin.settings.smtp');
                Route::post('/settings/smtp', 'store')->name('admin.settings.smtp.store');  
            });      
    
            // ADMIN GENERAL SETTINGS - REGISTRATION SETTINGS
            Route::controller(RegistrationController::class)->group(function() {
                Route::get('/settings/registration', 'index')->name('admin.settings.registration');
                Route::post('/settings/registration', 'store')->name('admin.settings.registration.store');
            });
    
            // ADMIN GENERAL SETTINGS - OAUTH SETTINGS
            Route::controller(OAuthController::class)->group(function() {
                Route::get('/settings/oauth', 'index')->name('admin.settings.oauth');
                Route::post('/settings/oauth', 'store')->name('admin.settings.oauth.store');
            });
    
            // ADMIN GENERAL SETTINGS - LANGUAGE TRANSLATION SETTINGS
            Route::controller(ElseyyidController::class)->group(function() {
                Route::get('/settings/languages/home', 'index')->name('elseyyid.translations.home2');
                Route::get('/settings/languages/lang/{lang}', 'lang')->name('elseyyid.translations.lang2');
                Route::get('/settings/languages/lang/generateJson/{lang}', 'generateJson')->name('elseyyid.translations.lang.generateJson2');
                Route::get('/settings/languages/newLang', 'newLang')->name('elseyyid.translations.lang.newLang2');
                Route::get('/settings/languages/newString', 'newString')->name('elseyyid.translations.lang.newString2');
                Route::get('/settings/languages/search', 'search')->name('elseyyid.translations.lang.search2');
                Route::get('/settings/languages/publish-all', 'publishAll')->name('elseyyid.translations.lang.publishAll2');
               // Route::post('/settings/languages/lang/update/{id}', 'update')->name('elseyyid.translations.lang.update');
            });
    
            Route::post('/settings/languages/lang/update-all', [LanguageController::class, 'languagesUpdateAll'])->name('elseyyid.translations.lang.update-all2');
            Route::post('/settings/languages/lang/save', [LanguageController::class, 'languageSave'])->name('elseyyid.translations.lang.lang-save2');
    
            Route::get('/settings/languages/setLocale', function (\Illuminate\Http\Request $request) {
                $settings = MainSetting::first();
                $settings->default_language = $request->setLocale;
                $settings->save();
                LaravelLocalization::setLocale($request->setLocale);
                toastr()->success(__('Default language successfully set'));
                return redirect()->route('elseyyid.translations.home2', [$request->setLocale]);
            })->name('elseyyid.translations.lang.setLocale2');
        
            Route::get('/settings/languages/regenerate', function () {
                Artisan::call('elseyyid:location:install');
                toastr()->success(__('Language files successfully regenerated'));
                return redirect()->route('elseyyid.translations.home2');
            })->name('elseyyid.translations.lang.reinstall');
        
            
            // ADMIN GENERAL SETTINGS - ACTIVATION SETTINGS
            Route::controller(ActivationController::class)->group(function() {
                Route::get('/settings/activation', 'index')->name('admin.settings.activation');
                Route::post('/settings/activation', 'store')->name('admin.settings.activation.store');
                Route::post('/settings/activation/destroy', 'destroy');
                Route::get('/settings/activation/manual', 'showManualActivation')->name('admin.settings.activation.manual');
                Route::post('/settings/activation/manual', 'storeManualActivation')->name('admin.settings.activation.manual.store');
            });
    
            // ADMIN FRONTEND SETTINGS - APPEARANCE SETTINGS
            Route::controller(AppearanceController::class)->group(function() {
                Route::get('/settings/appearance', 'index')->name('admin.settings.appearance');
                Route::post('/settings/appearance', 'store')->name('admin.settings.appearance.store');
            });
    
            // ADMIN FRONTEND SETTINGS - SEO Manager
            Route::controller(SEOManagerController::class)->group(function() {
                Route::get('/settings/seo', 'index')->name('admin.settings.seo');
                Route::post('/settings/seo', 'store')->name('admin.settings.seo.store');
            });
    
            // ADMIN FRONTEND SETTINGS - FRONTEND SETTINGS
            Route::controller(FrontendSettingController::class)->group(function() {
                Route::get('/settings/frontend', 'index')->name('admin.settings.frontend');
                Route::post('/settings/frontend', 'store')->name('admin.settings.frontend.store');
            });
    
            // ADMIN FRONTEND SECTION SETTINGS - FRONTEND SECTION SETTINGS
            Route::controller(FrontendSectionSettingController::class)->group(function() {
                Route::get('/settings/sections', 'index')->name('admin.settings.section');
                Route::post('/settings/sections', 'store')->name('admin.settings.section.store');
            });
    
            // ADMIN FRONTEND SETTINGS - FRONTEND SECTIONS
            Route::controller(FrontendSectionController::class)->group(function() {
                Route::get('/settings/steps', 'showSteps')->name('admin.settings.step');
                Route::get('/settings/steps/create', 'createSteps')->name('admin.settings.step.create');
                Route::post('/settings/steps/create', 'storeSteps')->name('admin.settings.step.store');
                Route::get('/settings/steps/{id}/edit', 'editSteps')->name('admin.settings.step.edit');
                Route::put('/settings/steps/{id}', 'updateSteps')->name('admin.settings.step.update');
                Route::post('/settings/steps/delete', 'deleteSteps');
                Route::get('/settings/tools', 'showTools')->name('admin.settings.tool');
                Route::get('/settings/tools/create', 'createTools')->name('admin.settings.tool.create');
                Route::post('/settings/tools/store', 'storeTools')->name('admin.settings.tool.store');
                Route::get('/settings/tools/{id}/edit', 'editTools')->name('admin.settings.tool.edit');
                Route::put('/settings/tools/{id}', 'updateTools')->name('admin.settings.tool.update');
                Route::post('/settings/tools/delete', 'deleteTools');
                Route::get('/settings/features', 'showFeatures')->name('admin.settings.feature');
                Route::get('/settings/features/create', 'createFeatures')->name('admin.settings.feature.create');
                Route::post('/settings/features/store', 'storeFeatures')->name('admin.settings.feature.store');
                Route::get('/settings/features/{id}/edit', 'editFeatures')->name('admin.settings.feature.edit');
                Route::put('/settings/features/{id}', 'updateFeatures')->name('admin.settings.feature.update');
                Route::post('/settings/features/delete', 'deleteFeatures');
                Route::get('/settings/cases', 'showCases')->name('admin.settings.case');
                Route::get('/settings/cases/create', 'createCases')->name('admin.settings.case.create');
                Route::post('/settings/cases/store', 'storeCases')->name('admin.settings.case.store');
                Route::get('/settings/cases/{id}/edit', 'editCases')->name('admin.settings.case.edit');
                Route::put('/settings/cases/{id}', 'updateCases')->name('admin.settings.case.update');
                Route::post('/settings/cases/delete', 'deleteCases');
                Route::get('/settings/clients', 'showClients')->name('admin.settings.client');
                Route::get('/settings/clients/create', 'createClients')->name('admin.settings.client.create');
                Route::post('/settings/clients/store', 'storeClients')->name('admin.settings.client.store');
                Route::post('/settings/clients/delete', 'deleteClients');
            });
    
            // ADMIN FRONTEND SETTINGS - BLOG MANAGER
            Route::controller(BlogController::class)->group(function() {
                Route::get('/settings/blog', 'index')->name('admin.settings.blog');
                Route::get('/settings/blog/create', 'create')->name('admin.settings.blog.create');
                Route::post('/settings/blog', 'store')->name('admin.settings.blog.store');   
                Route::put('/settings/blogs/{id}', 'update')->name('admin.settings.blog.update');		
                Route::get('/settings/blogs/{id}/edit', 'edit')->name('admin.settings.blog.edit');        
                Route::post('/settings/blog/delete', 'delete');
            });
    
            // ADMIN FRONTEND SETTINGS - FAQ MANAGER
            Route::controller(FAQController::class)->group(function() {
                Route::get('/settings/faq', 'index')->name('admin.settings.faq');
                Route::get('/settings/faq/create', 'create')->name('admin.settings.faq.create');        
                Route::post('/settings/faq', 'store')->name('admin.settings.faq.store');   
                Route::put('/settings/faqs/{id}', 'update')->name('admin.settings.faq.update');		
                Route::get('/settings/faqs/{id}/edit', 'edit')->name('admin.settings.faq.edit');        
                Route::post('/settings/faq/delete', 'delete');
            });
    
            // ADMIN FRONTEND SETTINGS - REVIEW MANAGER
            Route::controller(ReviewController::class)->group(function() {
                Route::get('/settings/review', 'index')->name('admin.settings.review');
                Route::get('/settings/review/create', 'create')->name('admin.settings.review.create');
                Route::post('/settings/review', 'store')->name('admin.settings.review.store');   
                Route::put('/settings/reviews/{id}', 'update')->name('admin.settings.review.update');		
                Route::get('/settings/reviews/{id}/edit', 'edit')->name('admin.settings.review.edit');        
                Route::post('/settings/review/delete', 'delete');
            });
    
            // ADMIN FRONTEND SETTINGS - GOOGLE ADSENSE
            Route::controller(AdsenseController::class)->group(function() {
                Route::get('/settings/adsense', 'index')->name('admin.settings.adsense');  
                Route::put('/settings/adsense/{id}', 'update')->name('admin.settings.adsense.update');		
                Route::get('/settings/adsense/{id}/edit', 'edit')->name('admin.settings.adsense.edit');        
            });
            
            // ADMIN FRONTEND SETTINGS - PAGES 
            Route::controller(PageController::class)->group(function() {
                Route::get('/settings/pages', 'index')->name('admin.settings.page');
                Route::get('/settings/pages/create', 'create')->name('admin.settings.page.create');
                Route::post('/settings/pages/store', 'store')->name('admin.settings.page.store');
                Route::put('/settings/pages/{id}', 'update')->name('admin.settings.page.update');		
                Route::get('/settings/pages/{id}/edit', 'edit')->name('admin.settings.page.edit');        
                Route::post('/settings/page/delete', 'delete');
            });
    
            // ADMIN GENERAL SETTINGS - UPGRADE SOFTWARE
            Route::controller(UpgradeController::class)->group(function() {
                Route::get('/settings/upgrade', 'index')->name('admin.settings.upgrade');
                Route::post('/settings/upgrade', 'upgrade')->name('admin.settings.upgrade.start');
            });
    
            // ADMIN GENERAL SETTINGS - SYSTEM SETTINGS
            Route::controller(SystemController::class)->group(function() {
                Route::get('/settings/system', 'index')->name('admin.settings.system');
                Route::post('/settings/system/cache', 'cache')->name('admin.settings.system.cache');
                Route::post('/settings/system/sitemap', 'sitemap')->name('admin.settings.system.sitemap');
            });

            // ADMIN GENERAL SETTINGS - GDPR SETTINGS
            Route::controller(GDPRController::class)->group(function() {
                Route::get('/settings/gdpr', 'index')->name('admin.settings.gdpr');
                Route::post('/settings/gdpr', 'store')->name('admin.settings.gdpr.store');
            });

            // ADMIN - MARKETPLACE
            Route::controller(MarketplaceController::class)->group(function() {
                Route::get('/marketplace', 'index')->name('admin.extensions');
                Route::post('/marketplace/purchase/install/{slug}', 'installExtension')->name('admin.extension.install');
                Route::get('/marketplace/purchase/{slug}', 'showExtension')->name('admin.extension.show');
                Route::post('/marketplace/purchase/{slug}', 'purchaseExtension')->name('admin.extension.purchase');
                Route::get('/marketplace/purchase/package/{slug}', 'purchasePackage')->name('admin.extension.purchase.package');
                Route::get('/marketplace/activate/{slug}', 'activateExtension')->name('admin.extension.activate');
            });
    
            // ADMIN - THEMES
            Route::controller(ThemeController::class)->group(function() {
                Route::get('/themes', 'index')->name('admin.themes');
                Route::post('/themes/purchase/install/{slug}', 'installTheme')->name('admin.theme.install');
                Route::get('/themes/purchase/{slug}', 'showTheme')->name('admin.theme.show');
                Route::post('/themes/purchase/{slug}', 'purchaseTheme')->name('admin.theme.purchase');
                Route::get('/themes/activate/{slug}', 'activateTheme')->name('admin.theme.activate');
            });
    
            // ADMIN STRIPE ROUTES
            Route::controller(StripeMarketplace::class)->group(function() {
                Route::post('/payments/stripe/process', 'processStripe')->name('admin.payments.process');
                Route::get('/payments/stripe/theme/approved', 'handleThemeApproval')->name('admin.payments.theme.approved');
                Route::get('/payments/stripe/market/approved', 'handleMarketApproval')->name('admin.payments.market.approved');
                Route::get('/payments/stripe/theme/cancel', 'processThemeCancel')->name('admin.payments.stripe.theme.cancel');
                Route::get('/payments/stripe/market/cancel', 'processMarketCancel')->name('admin.payments.stripe.market.cancel');
            });
    
        });
   
   
        // REGISTERED USER ROUTES
        Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {

            // USER DASHBOARD ROUTES
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');  
            Route::post('/dashboard/favorite', [UserDashboardController::class, 'favorite']);    
            Route::post('/dashboard/favoritecustom', [UserDashboardController::class, 'favoriteCustom']);    
            
            // USER TEMPLATE ROUTES
            Route::controller(TemplateController::class)->group(function () {
                Route::get('/templates', 'index')->name('user.templates');       
                Route::post('/templates/original-template/generate', 'generate');     
                Route::get('/templates/original-template/process', 'process');   
                Route::post('/templates/custom-template/customGenerate', 'customGenerate');   
                Route::get('/templates/custom-template/process', 'process');                 
                Route::post('/templates/save', 'save');     
                Route::post('/templates/brand', 'brand');     
                Route::post('/templates/original-template/favorite', 'favorite');     
                Route::post('/templates/original-template/favoritecustom', 'favoriteCustom');     
                Route::post('/templates/custom-template/favorite', 'favoriteCustom');     
                Route::get('/templates/custom-template/{code}', 'viewCustomTemplate');
                Route::get('/templates/original-template/{slug}', 'viewOriginalTemplate');
            });

            // USER CUSTOM TEMPLATES ROUTES
            Route::controller(UserCustomTemplateController::class)->group(function() {
                Route::get('/templates/custom', 'index')->name('user.templates.custom');
                Route::post('/templates/custom', 'store')->name('user.templates.custom.store');
                Route::get('/templates/custom/{id}/show', 'show')->name('user.templates.custom.show');
                Route::put('/templates/custom/{id}/update', 'update')->name('user.templates.custom.update');
                Route::post('/templates/custom/template/update', 'descriptionUpdate');  
                Route::post('/templates/custom/template/activate', 'templateActivate');   
                Route::post('/templates/custom/template/deactivate', 'templateDeactivate');  
                Route::post('/templates/custom/template/delete', 'deleteTemplate'); 
            }); 

            // USER CUSTOM CHAT ROUTES
            Route::controller(UserCustomChatController::class)->group(function() {
                Route::get('/chat/custom', 'index')->name('user.chat.custom');
                Route::post('/chat/custom', 'store')->name('user.chat.custom.store');
                Route::get('/chat/custom/create', 'create')->name('user.chat.custom.create');
                Route::get('/chat/custom/{id}/show', 'show')->name('user.chat.custom.show');
                Route::get('/chat/custom/files', 'files')->name('user.chat.custom.files');
                Route::put('/chat/custom/{id}/update', 'update')->name('user.chat.custom.update'); 
                Route::post('/chat/custom/activate', 'chatActivate');   
                Route::post('/chat/custom/deactivate', 'chatDeactivate');  
                Route::post('/chat/custom/delete', 'chatDelete'); 
                Route::post('/chat/custom/file/delete', 'fileDelete');         
            }); 

            // USER SMART EDITOR ROUTES
            Route::controller(SmartEditorController::class)->group(function () {
                Route::get('/smart-editor', 'index')->name('user.smart.editor');       
                Route::post('/smart-editor/show', 'show');     
                Route::post('/smart-editor/generate', 'generate');     
                Route::get('/smart-editor/process', 'process');                  
                Route::post('/smart-editor/custom', 'custom');     
                Route::post('/smart-editor/save', 'save');     
                Route::post('/smart-editor/favorite', 'favorite');            
            });

            // USER AI REWRITER ROUTES
            Route::controller(RewriterController::class)->group(function () {
                Route::get('/rewriter', 'index')->name('user.rewriter');       
                Route::post('/rewriter/show', 'show');     
                Route::post('/rewriter/generate', 'generate');     
                Route::get('/rewriter/process', 'process');                  
                Route::post('/rewriter/custom', 'custom');     
                Route::post('/rewriter/save', 'save');   
                Route::post('/rewriter/brand', 'brand');          
            });

            // USER AI IMAGE ROUTES
            Route::controller(ImageController::class)->group(function () {
                Route::get('/images', 'index')->name('user.images');      
                Route::post('/images/process', 'process');         
                Route::post('/images/view', 'view');         
                Route::post('/images/delete', 'delete');
                Route::get('/images/load', 'loadMore')->name('user.images.load');
            });

            // USER AI CODE ROUTES
            Route::controller(CodeController::class)->group(function () {
                Route::get('/code', 'index')->name('user.codex');      
                Route::post('/code/process', 'process');         
                Route::post('/code/save', 'save');         
                Route::post('/code/view', 'view');         
                Route::post('/code/delete', 'delete');
            });

            // USER AI CHAT ROUTES
            Route::controller(ChatController::class)->group(function () {        
                Route::get('/chat', 'index')->name('user.chat');      
                Route::post('/chat/process', 'process');   
                Route::post('/chat/process/custom', 'processCustom');   
                Route::post('/chat/clear', 'clear');   
                Route::post('/chat/favorite', 'favorite');
                Route::get('/chat/generate', 'generateChat');   
                Route::get('/chat/generate/custom', 'generateCustomChat');   
                Route::get('/chat/ephemeral', 'getEphemeralKey')->name('user.chat.ephemeral');             
                Route::post('/chat/conversation', 'conversation');                
                Route::post('/chat/history', 'history');                                  
                Route::post('/chat/model', 'model');                
                Route::post('/chat/rename', 'rename');
                Route::post('/chat/listen', 'listen');
                Route::post('/chat/delete', 'delete');
                Route::get('/chats/{code}', 'view');
                Route::get('/chats/realtime', 'viewRealtime')->name('user.chat.realtime');
                Route::get('/chats/custom/{code}', 'viewCustom');
                Route::post('/chat/storeRealtime', 'storeRealtimeMessage');
                Route::post('/chat/share/generate', 'storeChatShare');
            });

            // USER SPEECH TO TEXT ROUTES
            Route::controller(TranscribeController::class)->group(function () {
                Route::get('/speech-to-text', 'index')->name('user.transcribe');      
                Route::post('/speech-to-text/process', 'process');         
                Route::post('/speech-to-text/save', 'save');         
                Route::post('/speech-to-text/view', 'view');         
                Route::post('/speech-to-text/delete', 'delete');
            });

            // USER AI VOICEOVER ROUTES
            Route::controller(VoiceoverController::class)->group(function() {
                Route::get('/text-to-speech','index')->name('user.voiceover');    
                Route::post('/text-to-speech/synthesize','synthesize')->name('user.voiceover.synthesize');    
                Route::post('/text-to-speech/listen','listen')->name('user.voiceover.listen');    
                Route::post('/text-to-speech/listen-row','listenRow');    
                Route::get('/text-to-speech/{id}/show','show')->name('user.voiceover.show');       
                Route::post('/text-to-speech/audio','audio');           
                Route::post('/text-to-speech/delete','delete');           
                Route::post('/text-to-speech/configuration','configuration'); 
            });
            
            // USER AI ARTICLE WIZARD ROUTES
            Route::controller(ArticleWizardController::class)->group(function () {
                Route::get('/wizard', 'index')->name('user.wizard');       
                Route::post('/wizard/generate/keywords', 'keywords');     
                Route::post('/wizard/generate/ideas', 'ideas');     
                Route::post('/wizard/generate/outlines', 'outlines');     
                Route::post('/wizard/generate/talking-points', 'talkingPoints');     
                Route::post('/wizard/generate/images', 'images');     
                Route::post('/wizard/generate/prepare', 'prepare');     
                Route::get('/wizard/generate/process', 'process');     
                Route::post('/wizard/generate/clear', 'clear');            
            });

            // USER AI VISION ROUTES
            Route::controller(VisionController::class)->group(function () {        
                Route::get('/vision', 'index')->name('user.vision');      
                Route::post('/vision/process', 'process');   
                Route::post('/vision/clear', 'clear');   
                Route::get('/vision/generate', 'generateChat');   
                Route::post('/vision/conversation', 'conversation');                
                Route::post('/vision/history', 'history');                
                Route::post('/vision/rename', 'rename');
                Route::post('/vision/delete', 'delete');
            });

            // USER AI CHAT IMAGE ROUTES
            Route::controller(ChatImageController::class)->group(function () {        
                Route::get('/chat/image', 'index')->name('user.chat.image');      
                Route::post('/chat/image/process', 'process');   
                Route::post('/chat/image/clear', 'clear');      
                Route::post('/chat/image/conversation', 'conversation');                
                Route::post('/chat/image/history', 'history');                
                Route::post('/chat/image/rename', 'rename');
                Route::post('/chat/image/delete', 'delete');
            });

            // USER AI CHAT PDF ROUTES
            Route::controller(ChatFileController::class)->group(function () {        
                Route::get('/chat/file', 'index')->name('user.chat.file');      
                Route::post('/chat/file/process', 'process');   
                Route::post('/chat/file/clear', 'clear');     
                Route::post('/chat/file/conversation', 'conversation');                
                Route::post('/chat/file/rename', 'rename');
                Route::post('/chat/file/delete', 'delete');
                Route::post('/chat/file/metainfo', 'metainfo');
                Route::get('/chats/file/{code}', 'view');
                Route::post('/chat/file/credits', 'credits');
                Route::post('/chat/file/check-balance', 'checkBalance');
            });

            Route::controller(EmbeddingFileController::class)->group(function () {        
                Route::post('/chat/file/embedding', 'store');      
            });

            // USER AI WEB CHAT ROUTES
            Route::controller(ChatWebController::class)->group(function () {        
                Route::get('/chat/web', 'index')->name('user.chat.web');      
                Route::post('/chat/web/process', 'process');   
                Route::post('/chat/web/clear', 'clear');   
                Route::post('/chat/web/conversation', 'conversation');                              
                Route::post('/chat/web/rename', 'rename');
                Route::post('/chat/web/delete', 'delete');
                Route::post('/chat/web/metainfo', 'metainfo');
                Route::get('/chats/web/{code}', 'view');
                Route::post('/chat/web/credits', 'credits');
                Route::post('/chat/web/check-balance', 'checkBalance');
            });

            Route::controller(EmbeddingController::class)->group(function () {        
                Route::post('/chat/web/embedding', 'store');      
            });


            // USER BRAND VOICE ROUTES
            Route::controller(BrandVoiceController::class)->group(function () {
                Route::get('/brand', 'index')->name('user.brand');    
                Route::post('/brand', 'store')->name('user.brand.store');
                Route::get('/brand/create', 'create')->name('user.brand.create');          
                Route::get('/brand/{id}/edit', 'edit')->name('user.brand.edit');  
                Route::put('/brand/{id}/update', 'update')->name('user.brand.update');                  
                Route::post('/brand/delete', 'delete');
            });


            // USER INTEGRATION ROUTES
            Route::controller(IntegrationController::class)->group(function () {
                Route::get('/integration', 'index')->name('user.integration');                              
            });

            // USER AI YOUTUBE ROUTES
            Route::controller(YoutubeController::class)->group(function () {
                Route::get('/youtube', 'index')->name('user.youtube');          
                Route::post('/youtube/generate', 'generate');     
                Route::get('/youtube/process', 'process');                  
                Route::post('/youtube/custom', 'custom');     
                Route::post('/youtube/save', 'save');            
            });

            // USER AI RSS ROUTES
            Route::controller(RSSController::class)->group(function () {
                Route::get('/rss', 'index')->name('user.rss');       
                Route::post('/rss/fetch', 'fetch');     
                Route::post('/rss/generate', 'generate');     
                Route::get('/rss/process', 'process');                  
                Route::post('/rss/custom', 'custom');     
                Route::post('/rss/save', 'save');            
            });

            // USER DOCUMENT ROUTES
            Route::controller(DocumentController::class)->group(function() { 
                Route::get('/document', 'index')->name('user.documents');
                Route::post('/document', 'store');
                Route::get('/document/images', 'images')->name('user.documents.images');
                Route::post('/document/images/view', 'showImage'); 
                Route::get('/document/codes', 'codes')->name('user.documents.codes');
                Route::get('/document/voiceovers', 'voiceovers')->name('user.documents.voiceovers');
                Route::get('/document/transcripts', 'transcripts')->name('user.documents.transcripts');
                Route::post('/document/result/delete', 'delete');   
                Route::post('/document/result/code/delete', 'deleteCode');   
                Route::post('/document/result/voiceover/delete', 'deleteVoiceover');   
                Route::post('/document/result/transcript/delete', 'deleteTranscript');   
                Route::get('/document/result/{id}/show', 'show')->name('user.documents.show');
                Route::get('/document/result/code/{id}/show', 'showCode')->name('user.documents.code.show');
                Route::get('/document/result/voiceover/{id}/show', 'showVoiceover')->name('user.documents.voiceover.show');
                Route::get('/document/result/transcript/{id}/show', 'showTranscript')->name('user.documents.transcript.show');
            });

            // USER WORKBOOK ROUTES
            Route::controller(WorkbookController::class)->group(function() { 
                Route::get('/workbook', 'index')->name('user.workbooks');
                Route::post('/workbook', 'store');
                Route::post('/workbook/result/delete', 'delete');
                Route::get('/workbook/change', 'change')->name('user.workbooks.change');        
                Route::get('/workbook/result/{id}/show', 'show')->name('user.workbooks.show');
                Route::put('/workbook', 'update')->name('user.workbooks.update');
                Route::delete('/workbook', 'destroy')->name('user.workbooks.delete');
            });

            // USER CHANGE PASSWORD ROUTES
            Route::controller(UserPasswordController::class)->group(function() {
                Route::get('/profile/security', 'index')->name('user.security');
                Route::post('/profile/security/password', 'update')->name('user.security.password');
                Route::get('/profile/security/2fa', 'google')->name('user.security.2fa');
                Route::post('/profile/security/2fa/activate', 'activate2FA')->name('user.security.2fa.activate');
                Route::post('/profile/security/2fa/deactivate', 'deactivate2FA')->name('user.security.2fa.deactivate');
            });

            // USER PROFILE ROUTES
            Route::controller(UserController::class)->group(function () {
                Route::get('/profile', 'index')->name('user.profile');
                Route::put('/profile', 'update')->name('user.profile.update');
                Route::post('/profile/project', 'updateProject')->name('user.profile.project');
                Route::get('/profile/edit', 'edit')->name('user.profile.edit');     
                Route::get('/profile/edit/defaults', 'editDefaults')->name('user.profile.defaults');     
                Route::get('/profile/edit/delete', 'showDelete')->name('user.profile.delete');     
                Route::get('/profile/api/edit', 'showAPI')->name('user.profile.api');     
                Route::put('/profile/api/store', 'storeAPI')->name('user.profile.api.store');     
                Route::post('/profile/edit/delete', 'accountDelete')->name('user.profile.delete.account');     
                Route::put('/profile/update/defaults', 'updateDefaults')->name('user.profile.update.defaults'); 
                Route::post('/profile/change/referral', 'updateReferral');     
                Route::post('/profile/settings', 'themeSetting');     
                Route::post('/profile/email', 'emailNewsletter');  
                Route::get('/profile/wallet', 'showWallet')->name('user.wallet');     
                Route::put('/profile/wallet/store', 'storeWallet')->name('user.wallet.store');    
                Route::post('/profile/wallet/transfer', 'transferWallet')->name('user.wallet.transfer');    
                Route::get('/profile/wallet/transfer/list', 'transferList')->name('user.wallet.transfer.list');    
            });      

            // USER TEAM MANAGEMENT ROUTES
            Route::controller(TeamController::class)->group(function() {
                Route::get('/team', 'index')->name('user.team');
                Route::get('/team/list', 'listUsers')->name('user.team.list');        
                Route::post('/team', 'store')->name('user.team.store');
                Route::get('/team/create', 'create')->name('user.team.create');        
                Route::get('/team/{user}/show', 'show')->name('user.team.show');
                Route::get('/team/{user}/edit', 'edit')->name('user.team.edit');
                Route::put('/team/{user}/update', 'update')->name('user.team.update');     
                Route::post('/team/leave', 'leave');
                Route::post('/team/delete', 'delete');
            });                                 

            // USER SUPPORT REQUEST ROUTES  
            Route::controller(UserSupportController::class)->group(function() { 
                Route::get('/support', 'index')->name('user.support');
                Route::post('/support', 'store')->name('user.support.store');
                Route::post('/support/delete', 'delete');
                Route::post('/support/response', 'response')->name('user.support.response');
                Route::get('/support/create', 'create')->name('user.support.create'); 
                Route::get('/support/{ticket_id}/show', 'show')->name('user.support.show');
                
            });      

            // USER NOTIFICATION ROUTES
            Route::controller(UserNotificationController::class)->group(function() {
                Route::get('/notification', 'index')->name('user.notifications');
                Route::get('/notification/{id}/show', 'show')->name('user.notifications.show');        
                Route::post('/notification/delete', 'delete');
                Route::get('/notifications/mark-all', 'markAllRead')->name('user.notifications.markAllRead');
                Route::get('/notifications/delete-all', 'deleteAll')->name('user.notifications.deleteAll');
                Route::post('/notifications/mark-as-read', 'markNotification')->name('user.notifications.mark');
            });    

            // USER SEARCH ROUTES
            Route::post('/search', [SearchController::class, 'search'])->name('search'); 
        });


        // INCLUDE CUSTOM ROUTES
        $files = glob(base_path('routes/extensions/*.php'));
        for ($i = 0; $i < count($files); $i++) {
            include $files[$i];
        }

    });

    
    Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber',  'PreventBackHistory']], function() {
        
        // USER PAYMENT ROUTES
        Route::controller(PaymentController::class)->group(function() {
            Route::get('/payments/pay/{id}', 'pay');
            Route::post('/payments/pay/{id}', 'pay')->name('user.payments.pay');
            Route::get('/payments/pay/one-time/{type}/{id}', 'payPrePaid');
            Route::post('/payments/pay/one-time/{type}/{id}', 'payPrePaid')->name('user.payments.pay.prepaid');
            Route::post('/payments/approved/razorpay', 'approvedRazorpayPrepaid')->name('user.payments.approved.razorpay');
            Route::post('/payments/approved/midtrans', 'midtransSuccess')->name('user.payments.approved.midtrans'); 
            Route::post('/payments/approved/iyzico', 'iyzicoSuccess')->name('user.payments.approved.iyzico'); 
            Route::get('/payments/approved/braintree', 'braintreeSuccess')->name('user.payments.approved.braintree'); 
            Route::get('/payments/approved/paddle', 'paddleSuccess'); 
            Route::get('/payments/approved', 'approved')->name('user.payments.approved');               
            Route::get('/payments/cancelled', 'cancelled')->name('user.payments.cancelled');
            Route::post('/payments/subscription/razorpay', 'approvedRazorpaySubscription')->name('user.payments.subscription.razorpay');
            Route::get('/payments/subscription/flutterwave', 'approvedFlutterwaveSubscription')->name('user.payments.subscription.flutterwave');
            Route::get('/payments/subscription/stripe', 'approvedStripeSubscription')->name('user.payments.subscription.stripe');
            Route::get('/payments/subscription/approved', 'approvedSubscription')->name('user.payments.subscription.approved');        
            Route::get('/payments/subscription/cancelled', 'cancelledSubscription')->name('user.payments.subscription.cancelled');
        });

        // USER STRIPE ROUTES
        Route::controller(StripeService::class)->group(function() {
            Route::post('/payments/stripe/process', 'processStripe')->name('user.payments.stripe.process');
            Route::get('/payments/stripe/cancel', 'processCancel')->name('user.payments.stripe.cancel');
        });
    });

});