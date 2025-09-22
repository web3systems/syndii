<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Traits\VoiceToneTrait;
use Illuminate\Http\Request;
use App\Models\FavoriteTemplate;
use App\Models\CustomTemplate;
use App\Models\SubscriptionPlan;
use App\Models\Template;
use App\Models\Content;
use App\Models\Workbook;
use App\Models\Language;
use App\Models\Category;
use App\Models\ApiKey;
use App\Models\User;
use App\Models\Setting;
use App\Models\BrandVoice;
use App\Models\FineTuneModel;
use App\Models\MainSetting;
use App\Models\ExtensionSetting;
use App\Services\HelperService;
use OpenAI\Client;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;


class TemplateController extends Controller
{
    use VoiceToneTrait;

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $favorite_templates = Template::select('templates.*', 'favorite_templates.*')->where('favorite_templates.user_id', auth()->user()->id)->join('favorite_templates', 'favorite_templates.template_code', '=', 'templates.template_code')->where('status', true)->get();  
        $favorite_custom_templates = CustomTemplate::select('custom_templates.*', 'favorite_templates.*')->where('favorite_templates.user_id', auth()->user()->id)->join('favorite_templates', 'favorite_templates.template_code', '=', 'custom_templates.template_code')->where('status', true)->get();  
        $user_templates = FavoriteTemplate::where('user_id', auth()->user()->id)->pluck('template_code');     
        $other_templates = Template::whereNotIn('template_code', $user_templates)->where('status', true)->orderBy('group', 'desc')->get();   
        $custom_templates = CustomTemplate::whereNotIn('template_code', $user_templates)->where('type', '<>', 'private')->where('status', true)->orderBy('group', 'desc')->get();   
        $private_templates = CustomTemplate::where('user_id', auth()->user()->id)->where('type', 'private')->where('status', true)->orderBy('group', 'desc')->get();   
        
        $check_categories = Template::where('status', true)->groupBy('group')->pluck('group')->toArray();
        $check_custom_categories = CustomTemplate::where('status', true)->groupBy('group')->pluck('group')->toArray();
        $active_categories = array_unique(array_merge($check_categories, $check_custom_categories));
        $categories = Category::whereIn('code', $active_categories)->orderBy('name', 'asc')->get(); 

        if (!is_null(auth()->user()->plan_id)) {
            $subscription = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $check = $subscription->personal_templates_feature;
        } else {
            $check = false;
        }

        return view('user.templates.index', compact('favorite_templates', 'other_templates', 'custom_templates', 'favorite_custom_templates', 'categories', 'private_templates', 'check'));
    }


     /**
	*
	* Process Davinci
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function generate(Request $request) 
    {
        if ($request->ajax()) {
            $prompt = '';
            $max_tokens = '';
            $counter = 1;
            $input_title = '';
            $input_keywords = '';
            $input_description = '';

            # Check if user has access to the template
            $template = Template::where('template_code', $request->template)->first();
            if (auth()->user()->group == 'user') {
                if (config('settings.templates_access_user') != 'all' && config('settings.templates_access_user') != 'premium') {
                    if (is_null(auth()->user()->member_of)) {
                        if ($template->package == 'professional' && config('settings.templates_access_user') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if ($template->package == 'premium' && (config('settings.templates_access_user') != 'premium' && config('settings.templates_access_user') != 'all')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if (($template->package == 'standard' || $template->package == 'all') && (config('settings.templates_access_user') != 'professional' && config('settings.templates_access_user') != 'standard')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        }
                    } else {
                        $user = User::where('id', auth()->user()->member_of)->first();
                        $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                        if ($plan) {
                            if ($plan->templates != 'all' && $plan->templates != 'premium') {          
                                if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                } else if ($template->package == 'professional' && $plan->templates != 'professional') {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'standard' && $plan->templates != 'professional')) {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                }                     
                            }
                        } else {
                            $data['status'] = 'error';
                            $data['message'] = __('Your team subscription plan does not include support for this template category');
                            return $data;
                        }
                       
                    }
        
                }
            } elseif (auth()->user()->group == 'admin') {
                if (is_null(auth()->user()->plan_id)) {
                    if (config('settings.templates_access_admin') != 'all' && config('settings.templates_access_admin') != 'premium') {
                        if ($template->package == 'professional' && config('settings.templates_access_admin') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if(($template->package == 'standard' || $template->package == 'all') && (config('settings.templates_access_admin') != 'standard' || config('settings.templates_access_admin') != 'professional')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if ($template->package == 'premium' && (config('settings.templates_access_admin') != 'all' && config('settings.templates_access_admin') != 'premium')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } 
                    }
                } else {
                    $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                    if ($plan->templates != 'all' && $plan->templates != 'premium') {        
                        if ($template->package == 'professional' && $plan->templates != 'professional') {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'standard' && $plan->templates != 'professional')) {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        } else if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        }                 
                    }
                }
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($plan->templates != 'all' && $plan->templates != 'premium') {        
                    if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    } else if ($template->package == 'professional' && $plan->templates != 'professional') {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'professional' && $plan->templates != 'standard')) {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    }                     
                }
            }


            # Verify word limit
            if (auth()->user()->group == 'user') {
                $max_tokens = (config('settings.max_results_limit_user') < (int)$request->words) ? config('settings.max_results_limit_user') : (int)$request->words;
            } elseif (auth()->user()->group == 'admin') {
                $max_tokens = (config('settings.max_results_limit_admin') < (int)$request->words) ? config('settings.max_results_limit_user') : (int)$request->words;
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                $max_tokens = ($plan->max_tokens < (int)$request->words) ? $plan->max_tokens : (int)$request->words;
            }


            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }
            

            # Check personal API keys
            if (config('settings.personal_openai_api') == 'allow') {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                    return $data;
                }     
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    if (is_null(auth()->user()->personal_openai_key)) {
                        $data['status'] = 'error';
                        $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                        return $data;
                    } 
                }    
            } 
            

            # Filter for sensitive words
            $bad_words = Setting::where('name', 'words_filter')->first();
            $bad_words = explode(',', $bad_words->value);
            $bad_words = array_map('trim', $bad_words);
            $count_words = count($bad_words);

            if ($count_words == 1) {
                if ($request->title) {
                    $input_title = $request->title;
                }

                if ($request->keywords) {
                    $input_keywords = $request->keywords;
                }

                if ($request->description) {
                    $input_description = $request->description;
                }

            } else {
                foreach ($bad_words as $key => $word) {
                    if ($request->title) {                        
                        if ($key == 0) {
                            $input_title = $this->check_bad_words($word, $request->title, '');
                        } else {
                            $input_title = $this->check_bad_words($word, $input_title, '');
                        }                        
                    }
    
                    if ($request->keywords) {
                        if ($key == 0) {
                            $input_keywords = $this->check_bad_words($word, $request->keywords, '');
                        } else {
                            $input_keywords = $this->check_bad_words($word, $input_keywords, '');
                        }
                    }
    
                    if ($request->description) {
                        if ($key == 0) {
                            $input_description = $this->check_bad_words($word, $request->description, '');
                        } else {
                            $input_description = $this->check_bad_words($word, $input_description, '');
                        }
                    }

                }
            }
            

            # Generate proper prompt in respective language
            switch ($request->template) {
                case 'KPAQQ':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getArticleGeneratorPrompt(strip_tags($input_title), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'JXRZB':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getParagraphGeneratorPrompt(strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'OPYAB':                                        
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getProsAndConsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'VFWSQ':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getTalkingPointsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'OMMEI':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getSummarizeTextPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'HXLNA':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getProductDescriptionPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'DJSVM':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getStartupNameGeneratorPrompt(strip_tags($input_keywords), strip_tags($input_description), $request->language, $max_tokens);
                    break;
                case 'IXKBE':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getProductNameGeneratorPrompt(strip_tags($input_keywords), strip_tags($input_description), $request->language, $max_tokens);
                    break;
                case 'JCDIK':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getMetaDescriptionPrompt(strip_tags($input_title), strip_tags($input_keywords), strip_tags($input_description), $request->language, $max_tokens);
                    break;
                case 'SZAUF':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getFAQsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'BFENK':                    
                    request()->validate(['title' => 'required', 'description' => 'required', 'question' => 'required']);
                    $prompt = $this->getFAQAnswersPrompt(strip_tags($input_title), strip_tags($request->question), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'XLGPP':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getTestimonialsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WGKYP':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getBlogTitlesPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'EEKZF':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getBlogSectionPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'KDGOX':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getBlogIdeasPrompt(strip_tags($input_title), $request->language, $request->tone, $max_tokens);
                    break;
                case 'TZTYR':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getBlogIntrosPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'ZGUKM':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getBlogConclusionPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WCZGL':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getContentRewriterPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'CTMNI':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getFacebookAdsPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'ZLKSP':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getVideoDescriptionsPrompt(strip_tags($input_title), $request->language, $request->tone, $max_tokens);
                    break;
                case 'OJIOV':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getVideoTitlesPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'ECNVU':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getYoutubeTagsGeneratorPrompt(strip_tags($input_description), $request->language);
                    break;
                case 'EOASR':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getInstagramCaptionsPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'IEMBM':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getInstagramHashtagsPrompt(strip_tags($input_title), $request->language, $max_tokens);
                    break;
                case 'CKOHL':                  
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getSocialPostPersonalPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'ABWGU':                    
                    request()->validate(['description' => 'required', 'title' => 'required', 'post' => 'required']);
                    $prompt = $this->getSocialPostBusinessPrompt(strip_tags($input_description), strip_tags($input_title), strip_tags($request->post), $request->language, $request->tone, $max_tokens);
                    break;
                case 'HJYJZ':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getFacebookHeadlinesPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SGZTW':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getGoogleHeadlinesPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'YQAFG':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getGoogleAdsPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'BGXJE':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getPASPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SXQBT':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getAcademicEssayPrompt(strip_tags($input_title), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'RLXGB':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getWelcomeEmailPrompt(strip_tags($input_title), strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'RDJEZ':                    
                    request()->validate(['description' => 'required', 'title' => 'required']);
                    $prompt = $this->getColdEmailPrompt(strip_tags($input_title), strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'XVNNQ':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getFollowUpEmailPrompt(strip_tags($input_title), strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'PAKMF':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getCreativeStoriesPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'OORHD':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getGrammarCheckerPrompt(strip_tags($input_description), $request->language, $max_tokens);
                    break;
                case 'SGJLU':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getSummarize2ndGraderPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WISHV':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getVideoScriptsPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WISTT':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getAmazonProductPrompt(strip_tags($input_title), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'LMMPR':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getTextExtenderPrompt(strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'NJLCK':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getRewriteTextPrompt(strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'QJGQU':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getSongLyricsPrompt(strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'IQWZV':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getBusinessIdeasPrompt(strip_tags($input_description), $request->language);
                    break;
                case 'NEVUR':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getLinkedinPostPrompt(strip_tags($input_description), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'MQSHO':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getCompanyBioPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'TFYLZ':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getEmailSubjectPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'CPTXT':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getProductBenefitsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'KMKBQ':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getSellingTitlesPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'UNOEP':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getProductComparisonPrompt(strip_tags($input_title), $request->language, $request->tone, $max_tokens);
                    break;
                case 'RKYNX':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getProductCharacteristicsPrompt(strip_tags($input_title), strip_tags($input_keywords), $request->language, $request->tone, $max_tokens);
                    break;
                case 'YVEFP':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getTwitterTweetsPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'PEVVE':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getTiktokScriptsPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WMRJR':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getLinkedinHeadlinesPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SSWNL':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getLinkedinAdDescriptionPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'HRXVL':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getSMSNotificationPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SYVKG':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getToneChangerPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'ETEDT':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getAmazonProductFeaturesPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SNINY':                    
                    request()->validate(['title' => 'required']);
                    $prompt = $this->getDictionaryPrompt(strip_tags($input_title),$request->language);
                    break;
                case 'GUXCM':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getPrivacyPolicyPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'LWOKG':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getTermsAndConditionsPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'CHJGF':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getClickbaitTitlesPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'JKTUY':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getCompanyPressReleasePrompt(strip_tags($input_title), strip_tags($input_description), strip_tags($request->audience), $request->language, $request->tone, $max_tokens);
                    break;
                case 'XTABO':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getProductPressReleasePrompt(strip_tags($input_title), strip_tags($input_description), strip_tags($request->audience), $request->language, $request->tone, $max_tokens);
                    break;
                case 'WQJYP':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getAIDAPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'APUSA':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getBABPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'AEJJV':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getPPPPPrompt(strip_tags($input_title), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'FYKJD':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getBrandNamesPrompt(strip_tags($input_description), $request->language, $max_tokens);
                    break;
                case 'DYNJE':                    
                    request()->validate(['title' => 'required', 'description' => 'required']);
                    $prompt = $this->getAdHeadlinesPrompt(strip_tags($input_title), strip_tags($request->audience), strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                case 'SXFVD':                    
                    request()->validate(['description' => 'required']);
                    $prompt = $this->getNewsletterGeneratorPrompt(strip_tags($input_description), $request->language, $request->tone, $max_tokens);
                    break;
                default:
                    # code...
                    break;
            }


             # Add Brand information
            if ($request->brand == 'on') {
                $brand = BrandVoice::where('id', $request->company)->first();

                if ($brand) {
                    $product = '';
                    if ($request->service != 'none') {
                        foreach($brand->products as $key => $value) {
                            if ($key == $request->service)
                                $product = $value;
                        }
                    } 
                    
                    if ($request->service != 'none') {
                        $prompt .= ".\n Focus on my company and {$product['type']}'s information: \n";
                    } else {
                        $prompt .= ".\n Focus on my company's information: \n";
                    }
                    
                    if ($brand->name) {
                        $prompt .= "The company's name is {$brand->name}. ";
                    }

                    if ($brand->description) {
                        $prompt .= "The company's description is {$brand->description}. ";
                    }

                    if ($brand->website) {
                        $prompt .= ". The company's website is {$brand->website}. ";
                    }

                    if ($brand->tagline) {
                        $prompt .= "The company's tagline is {$brand->tagline}. ";
                    }

                    if ($brand->audience) {
                        $prompt .= "The company's target audience is: {$brand->audience}. ";
                    }

                    if ($brand->industry) {
                        $prompt .= "The company focuses in: {$brand->industry}. ";
                    }
    
                    if ($product) {
                        if ($product['name']) {
                            $prompt .= "The {$product['type']}'s name is {$product['name']}. \n";
                        }

                        if ($product['description']) {
                            $prompt .= "The {$product['type']} is about {$product['description']}. ";
                        }                        
                    }
                }
            }

     
            $plan_type = (auth()->user()->plan_id) ? 'paid' : 'free';

            session()->put('realtime', $request->internet);
            
            # Update credit balance
            $flag = Language::where('language_code', $request->language)->first();

            $content = new Content();
            $content->user_id = auth()->user()->id;
            $content->input_text = $prompt;
            $content->language = $request->language;
            $content->language_name = $flag->language;
            $content->language_flag = $flag->language_flag;
            $content->template_code = $request->template;
            $content->template_name = $template->name;
            $content->icon = $template->icon;
            $content->group = $template->group;
            $content->tokens = 0;
            $content->plan_type = $plan_type;
            $content->model = $request->model;
            $content->save();

            $data['status'] = 'success';    
            $data['max_results'] = $request->max_results;    
            $data['temperature'] = $request->creativity;    
            $data['max_words'] = $max_tokens;    
            $data['id'] = $content->id;
            $data['language'] = $request->language;
            return $data;            

        }
	}


     /**
	*
	* Process Davinci
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {

        # Get Settings
        $settings = MainSetting::first();
        $extension = ExtensionSetting::first();
        
        $max_results = $request->max_results;
        $max_words = $request->max_words;
        $temperature = $request->temperature;
        $content = Content::where('id', $request->content_id)->first();
        $prompt = $content->input_text;  
        $model = $content->model;

        $realtime = session()->get('realtime'); 

        # Append real time data
        if($realtime == 'on') {
            if ($settings->realtime_data_engine == 'serper') {
                $prompt = $this->realtimeData($prompt, 'serper');
            } elseif ($settings->realtime_data_engine == 'perplexity') {
                $prompt = $this->realtimeData($prompt, 'perplexity');
            } 
        } 


         # Start OpenAI task
         if (in_array($model, ['gpt-3.5-turbo-0125', 'gpt-4', 'gpt-4o', 'gpt-4o-mini', 'gpt-4.5-preview', 'o1', 'o1-mini', 'o3-mini', 'gpt-4-0125-preview', 'o3', 'o4-mini', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'gpt-4o-search-preview', 'gpt-4o-mini-search-preview'])) {
            if (\App\Services\HelperService::extensionAzureOpenai() && $extension->azure_openai_activate) {
    
                return $this->streamAzure($request->content_id, $max_results, $prompt, $max_words, $temperature);                      

            } elseif (\App\Services\HelperService::extensionOpenRouter() && $extension->open_router_activate) {
            
                return $this->streamOpenRouter($request->content_id, $max_results, $prompt, $max_words, $temperature); 

            } else {

                if (config('settings.personal_openai_api') == 'allow') {
                    $openai_api = auth()->user()->personal_openai_key;        
                } elseif (!is_null(auth()->user()->plan_id)) {
                    $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                    if ($check_api->personal_openai_api) {
                        $openai_api = auth()->user()->personal_openai_key;               
                    } else {
                        if (config('settings.openai_key_usage') !== 'main') {
                        $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                        array_push($api_keys, config('services.openai.key'));
                        $key = array_rand($api_keys, 1);
                        $openai_api = $api_keys[$key];
                    } else {
                        $openai_api = config('services.openai.key');
                    }
                }               
                } else {
                    if (config('settings.openai_key_usage') !== 'main') {
                        $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                        array_push($api_keys, config('services.openai.key'));
                        $key = array_rand($api_keys, 1);
                        $openai_api = $api_keys[$key];
                    } else {
                        $openai_api = config('services.openai.key');
                    }
                }

                if (is_null($openai_api) || $openai_api == '') {
                    return response()->stream(function () {
                        echo 'data: OpenAI Notification: <span class="font-weight-bold">Missing OpenAI API key</span>. Please contact support team.';
                        echo "\n\n";
                        echo 'data: [DONE]';
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }, 200, [
                        'Cache-Control' => 'no-cache',
                        'X-Accel-Buffering' => 'no',
                        'Content-Type' => 'text/event-stream',
                    ]);
                }

                return $this->streamOpenai($prompt, $request->content_id, $max_results, $max_words, $temperature, $openai_api);
            }
        }

        # Start Anthropic task
        if (in_array($model, ['claude-3-7-sonnet-20250219', 'claude-3-opus-20240229', 'claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022', 'claude-opus-4-20250514', 'claude-sonnet-4-20250514'])) {
            if (config('settings.personal_claude_api') == 'allow') {
                $anthropic_api = auth()->user()->personal_claude_key;        
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_claude_api) {
                    $anthropic_api = auth()->user()->personal_claude_key;               
                } else {
                    $anthropic_api = config('anthropic.api_key');                           
               }                       
            } else {
                $anthropic_api = config('anthropic.api_key'); 
            }

            if (is_null($anthropic_api) || $anthropic_api == '') {
                return response()->stream(function () {
                    echo 'data: Anthropic Notification: <span class="font-weight-bold">Missing Anthropic API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamClaude($prompt, $request->content_id, $max_results, $max_words, $temperature, $anthropic_api);
        }


         # Start Anthropic task         
         if ($model == 'gemini-1.5-pro' || $model == 'gemini-1.5-flash' || $model == 'gemini-2.0-flash') {
            if (config('settings.personal_gemini_api') == 'allow') {
                $gemini_api = auth()->user()->personal_gemini_key;        
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_gemini_api) {
                    $gemini_api = auth()->user()->personal_gemini_key;               
                } else {
                    $gemini_api = config('gemini.api_key');                           
               }                       
            } else {
                $gemini_api = config('gemini.api_key'); 
            }

            if (is_null($gemini_api) || $gemini_api == '') {
                return response()->stream(function () {
                    echo 'data: Gemini Notification: <span class="font-weight-bold">Missing Gemini API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamGemini($prompt, $request->content_id, $max_results, $max_words, $temperature, $gemini_api);
        }


        # Start xAI task
        if ($model == 'grok-2-1212' || $model == 'grok-2-vision-1212') {
            if (is_null($settings->xai_api) || $settings->xai_api == '') {
                return response()->stream(function () {
                    echo 'data: xAI Notification: <span class="font-weight-bold">Missing xAI API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamxAI($prompt, $request->content_id, $max_results, $max_words, $temperature, $settings->xai_api, $settings->xai_base_url);
        }


        # Start DeepSeek task
        if ($model == 'deepseek-chat' || $model == 'deepseek-reasoner') {
            if (is_null($settings->deepseek_api) || $settings->deepseek_api == '') {
                return response()->stream(function () {
                    echo 'data: Deepseek Notification: <span class="font-weight-bold">Missing DeepSeek API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamDeepSeek($prompt, $request->content_id, $max_results, $max_words, $temperature, $settings->deepseek_api, $settings->deepseek_base_url);
        }


        # Start Perplexity task
        if ($model == 'sonar' || $model == 'sonar-pro' || $model == 'sonar-reasoning' || $model == 'sonar-reasoning-pro') {
            if (is_null($extension->perplexity_api) || $extension->perplexity_api == '') {
                return response()->stream(function () {
                    echo 'data: Perplexity Notification: <span class="font-weight-bold">Missing Perplexity API key</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamPerplexity($prompt, $request->content_id, $max_results, $max_words, $temperature, $extension->perplexity_api);
        }


        # Start Nova task         
        if ($model == 'us.amazon.nova-micro-v1:0' || $model == 'us.amazon.nova-lite-v1:0' || $model == 'us.amazon.nova-pro-v1:0') {    

            if (is_null($extension->amazon_bedrock_access_key) || $extension->amazon_bedrock_access_key == '') {
                return response()->stream(function () {
                    echo 'data: Amazon Nova Notification: <span class="font-weight-bold">Missing AWS Access keys</span>. Please contact support team.';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                    'Content-Type' => 'text/event-stream',
                ]);
            }

            return $this->streamBedrock($request->content_id, $max_results, $prompt, $max_words, $temperature);
        }

	}


    /**
	*
	* Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamOpenai($prompt, $content_id, $max_results, $max_words, $temperature, $openai_api)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $openai_api) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";              

            $messages[] = ['role' => 'user', 'content' => $prompt];                          
 
            try {

                $openai_client = \OpenAI::client($openai_api);
                
                if (in_array($model, ['o1', 'o1-mini', 'o3-mini', 'o3', 'o4-mini'])) {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => $messages,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                } elseif (in_array($model, ['gpt-4o-search-preview', 'gpt-4o-mini-search-preview'])) {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => $messages,
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                } else {
                    $stream = $openai_client->chat()->createStreamed([
                        'model' => $model,
                        'messages' => $messages,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                        'temperature' => (float)$temperature,                        
                        'stream_options'=>[
                            'include_usage' => true,
                        ]
                    ]);
                }
                

                foreach ($stream as $result) {

                    if (isset($result->choices[0]->delta->content)) {
                        $raw = $result->choices[0]->delta->content;
                        $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                        $text .= $raw;
    
                        if (connection_aborted()) {
                            break;
                        }
    
                        echo 'data: ' . $clean;
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }
    
                    if($result->usage !== null){
                        $input_tokens = $result->usage->promptTokens;
                        $output_tokens = $result->usage->completionTokens; 
                    }
                }

                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (Exception $e) {
                Log::error('OpenAI API Error: ' . $e->getMessage());
                echo 'data: OpenAI Notification: <span class="font-weight-bold">' . $e->getMessage() . '</span>. Please contact support team.';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }
       
Log::info($text);
            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);   
Log::info('tut');
Log::info($input_tokens);
Log::info($output_tokens);
                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();

            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no'
        ]);
    }


    /**
     *
     * Anthropic Claude stream task
     * @param - conversation_id, chat_id, prompt, anthropic_api_key
     * @return - text stream
     *
     */
    private function streamClaude($prompt, $content_id, $max_results, $max_words, $temperature, $anthropic_api)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $anthropic_api) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";              

            // Add current prompt
            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {
                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $anthropic_api,
                    'anthropic-version' => '2023-06-01'
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => (float)$temperature,
                    'stream' => true,
                    'max_tokens' => (int)$max_words
                ];
                
                $response = $client->post('https://api.anthropic.com/v1/messages', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Anthropic API Error: HTTP Status $statusCode";
                    
                    $errorType = isset($errorData['error']['type']) 
                        ? $errorData['error']['type'] 
                        : "unknown_error";
                    
                    Log::error("Anthropic API Error ($errorType): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line) || $line === 'data: ') {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
                            $data = json_decode($json, true);

                            if (isset($data['error'])) {
                                $errorMessage = $data['error']['message'] ?? 'Unknown error';
                                $errorType = $data['error']['type'] ?? 'unknown_error';
                                
                                Log::error("Anthropic API Stream Error ($errorType): $errorMessage", [
                                    'error_data' => $data['error']
                                ]);
                                
                                echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                echo "\n\n";
                                flush();
                                continue;
                            }

                            // Handle content delta for streaming
                            if (isset($data['type']) && $data['type'] === 'content_block_delta') {
                                if (isset($data['delta']['text'])) {
                                    $raw = $data['delta']['text'];
                                    $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                    $text .= $raw;
                                
                                    echo 'data: ' . $clean;
                                    echo "\n\n";
                                    ob_flush();
                                    flush();
                                }
                            }

                            // Handle message stop event                          
                            if (isset($data['message']['usage'])) {                              
                                // Check if usage data exists in the expected structure
                                if (isset($data['message']['usage']['input_tokens'])) {
                                    $input_tokens = $data['message']['usage']['input_tokens'];
                                }
                                if (isset($data['message']['usage']['output_tokens'])) {
                                    $output_tokens = $data['message']['usage']['output_tokens'];
                                }
                            }

                            if (isset($data['usage'])) {                              
                                if (isset($data['usage']['output_tokens'])) {
                                    $output_tokens = $data['usage']['output_tokens'];
                                }
                            }
                            
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
                
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Anthropic API Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Anthropic service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: Anthropic API Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }

            
            if (!empty($text)) {

                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);  

                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Deepseek stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamDeepSeek($prompt, $content_id, $max_results, $max_words, $temperature, $deepseek_api, $deepseek_base_url)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $deepseek_api, $deepseek_base_url) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";                            

            $messages[] = ['content' => $prompt, 'role' => 'user']; 

            try {

                $deepseek_client = \OpenAI::factory()
                                    ->withApiKey($deepseek_api)
                                    ->withBaseUri($deepseek_base_url) 
                                    ->withHttpClient($httpClient = new \GuzzleHttp\Client([])) 
                                    ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $httpClient->send($request, [
                                        'stream' => true
                                    ]))
                                    ->make();
                    
                $stream = $deepseek_client->chat()->createStreamed([
                    'model' => $model,
                    'messages' => $messages,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                    'temperature' => (float)$temperature,
                    'max_tokens' => (int)$max_words ?? 4096,
                    'stream_options'=>[
                        'include_usage' => true,
                    ]
                ]);

                foreach ($stream as $result) {

                    if (isset($result->choices[0]->delta->content)) {
                        $raw = $result->choices[0]->delta->content;
                        $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                        $text .= $raw;
    
                        if (connection_aborted()) {
                            break;
                        }
    
                        echo 'data: ' . $clean;
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }
    
                    if($result->usage !== null){
                        $input_tokens = $result->usage->promptTokens;
                        $output_tokens = $result->usage->completionTokens; 
                    }
                }
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();

            } catch (Exception $e) {
                Log::error('Deepseek API Error: ' . $e->getMessage());
                echo 'data: Deepseek Notification: <span class="font-weight-bold">' . $e->getMessage() . '</span>. Please contact support team.';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }
       

            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);   

                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* xAI stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamxAI($prompt, $content_id, $max_results, $max_words, $temperature, $xai_api, $xai_base_url)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $xai_api, $xai_base_url) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";     

            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {

                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $xai_api
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => (float)$temperature,
                    'max_tokens' => (int)$max_words ?? 16384,
                    'stream' => true
                ];
                
                $response = $client->post($xai_base_url . '/chat/completions', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "xAI API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("xAI API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: xAI API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line)) {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
    
                            if (strpos($json, '{"error":') === 0) {
                                $errorData = json_decode($json, true);
                                if (isset($errorData['error'])) {
                                    $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                                    $errorCode = $errorData['error']['code'] ?? 'unknown_error';
                                    
                                    Log::error("xAI API Stream Notification ($errorCode): $errorMessage", [
                                        'error_data' => $errorData
                                    ]);
                                    
                                    echo 'data: xAI API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                    echo "\n\n";
                                    flush();
                                    continue;
                                }
                            }
    
                            $data = json_decode($json, true);
                       
                            if (isset($data['choices'][0]['delta']['content'])) {
                                $raw = $data['choices'][0]['delta']['content'];
                                $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                $text .= $raw;
                               
                                echo 'data: ' . $clean;
                                echo "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
                
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
    
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("xAI Notification ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The xAI service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: xAI Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }
            
            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                $tokens = $this->estimateTokenUsage($messages, $text);
                HelperService::updateBalance($words, $model, $tokens['prompt_tokens'], $tokens['completion_tokens']); 

                $content->result_text = $text;
                $content->input_tokens = $tokens['prompt_tokens'];
                $content->output_tokens = $tokens['completion_tokens'];
                $content->words = $words;
                $content->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Gemini stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamGemini($prompt, $content_id, $max_results, $max_words, $temperature, $gemini_api)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $gemini_api) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";     

            $modelMap = [
                'gemini-1.5-pro' => 'models/gemini-1.5-pro',
                'gemini-1.5-flash' => 'models/gemini-1.5-flash',
                'gemini-2.0-flash' => 'models/gemini-2.0-flash',
            ];

            $apiModel = $modelMap[$model] ?? 'models/gemini-pro';
            
            $contents = [];
    
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => "User: $prompt"]
                ]
            ];
            


            try {
                $payload = [
                    'contents' => $contents,
                    'generation_config' => [
                        'temperature' => (float)$temperature,
                        'topP' => 0.95,
                        'topK' => 40,
                    ],
                ];

                // Create HTTP client
                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $apiEndpoint = "https://generativelanguage.googleapis.com/v1beta/{$apiModel}:streamGenerateContent?key={$gemini_api}";
          
                $response = $client->post($apiEndpoint, [
                    'json' => $payload,
                    'stream' => true,
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]);
           
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Gemini API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("Gemini API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Gemini API Error: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }

                $stream = $response->getBody()->getContents();
                $stream = json_decode($stream, true);
      
                foreach ($stream as $result) {

                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $raw = $result['candidates'][0]['content']['parts'][0]['text'];
                        Log::info($result);
                        $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                        $text .= $raw;
    
                        if (connection_aborted()) {
                            break;
                        }
    
                        echo 'data: ' . $clean;
                        echo "\n\n";
                        ob_flush();
                        flush();
                    }
    
                    if(isset($result['usageMetadata'])){
                        if (isset($result['usageMetadata']['promptTokenCount'])) {
                            $input_tokens = $result['usageMetadata']['promptTokenCount'];
                        }

                        if (isset($result['usageMetadata']['candidatesTokenCount'])) {
                            $output_tokens = $result['usageMetadata']['candidatesTokenCount']; 
                        } 
                    }
                }

                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
                
            } catch (\Exception $e) {
                // Handle exceptions
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Gemini Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Gemini service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'API key not valid') !== false || strpos($errorMessage, 'invalid API key') !== false) {
                    $userMessage = 'Invalid API key. Please check your Gemini API key.';
                } elseif (strpos($errorMessage, 'quota') !== false) {
                    $userMessage = 'API quota exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'PERMISSION_DENIED') !== false) {
                    $userMessage = 'Permission denied. Please check your API key permissions.';
                } elseif (strpos($errorMessage, 'INVALID_ARGUMENT') !== false) {
                    $userMessage = 'Invalid request. The prompt may contain content that violates Gemini\'s content policy.';
                }
                
                echo 'data: Gemini API Error: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }

            // Only update database if we have a response
            if (!empty($text)) {
                
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);  

                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Perplexity stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamPerplexity($prompt, $content_id, $max_results, $max_words, $temperature, $perplexity_api)
    {
        return response()->stream(function () use($prompt, $content_id, $max_results, $max_words, $temperature, $perplexity_api) {
            
            if ( (int)$max_results > 1 ) {
                $prompt .='. Create seperate distinct ' . $max_results . ' results.';
            }

            $content = Content::where('id', $content_id)->first();  
            $model = $content->model;         
            $input_tokens = 0;
            $output_tokens = 0;
            $text = "";     

            $messages[] = ['role' => 'user', 'content' => $prompt];

            try {

                $client = new \GuzzleHttp\Client([
                    'timeout' => 120,
                    'http_errors' => false
                ]);
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $perplexity_api
                ];
                
                $body = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => (float)$temperature,
                    'max_tokens' => (int)$max_words,
                    'stream' => true
                ];
                
                $response = $client->post('https://api.perplexity.ai/chat/completions', [
                    'headers' => $headers,
                    'json' => $body,
                    'stream' => true
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getBody()->getContents();
                    $errorData = json_decode($errorBody, true);
                    
                    $errorMessage = isset($errorData['error']['message']) 
                        ? $errorData['error']['message'] 
                        : "Perplexity API Error: HTTP Status $statusCode";
                    
                    $errorCode = isset($errorData['error']['code']) 
                        ? $errorData['error']['code'] 
                        : "unknown_error";
                    
                    Log::error("Perplexity API Error ($errorCode): $errorMessage", [
                        'status_code' => $statusCode,
                        'error_data' => $errorData
                    ]);
                    
                    echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                    echo "\n\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                
                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(4096);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);

                        $line = trim($line);
                        if (empty($line)) {
                            continue;
                        }

                        if ($line === 'data: [DONE]') {
                            echo "data: [DONE]\n\n";
                            flush();
                            break 2; 
                        }

                        if (strpos($line, 'data: ') === 0) {
                            $json = substr($line, 6); 
    
                            if (strpos($json, '{"error":') === 0) {
                                $errorData = json_decode($json, true);
                                if (isset($errorData['error'])) {
                                    $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                                    $errorCode = $errorData['error']['code'] ?? 'unknown_error';
                                    
                                    Log::error("Perplexity API Stream Notification ($errorCode): $errorMessage", [
                                        'error_data' => $errorData
                                    ]);
                                    
                                    echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($errorMessage) . '</span>';
                                    echo "\n\n";
                                    flush();
                                    continue;
                                }
                            }
    
                            $data = json_decode($json, true);
                          
                            if (isset($data['choices'][0]['delta']['content'])) {
                                $raw = $data['choices'][0]['delta']['content'];
                                $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                                $text .= $raw;
                               
                                echo 'data: ' . $clean;
                                echo "\n\n";
                                ob_flush();
                                flush();

                            }  
                            
                            if (isset($data['usage'])) {
                                if (isset($data['usage']['prompt_tokens'])) {
                                    $input_tokens = $data['usage']['prompt_tokens'];
                                }
                                if (isset($data['usage']['completion_tokens'])) {
                                    $output_tokens = $data['usage']['completion_tokens'];
                                }
                            }
                        }
                    }
                    
                    if (connection_aborted()) {
                        break;
                    }
                }
                
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
    
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                
                Log::error("Perplexity API Exception ($errorCode): $errorMessage", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide user-friendly error messages
                $userMessage = 'An error occurred while processing your request.';
                
                if (strpos($errorMessage, 'cURL error 28') !== false) {
                    $userMessage = 'Request timed out. The Perplexity service might be experiencing high load.';
                } elseif (strpos($errorMessage, 'cURL error 6') !== false) {
                    $userMessage = 'Could not resolve host. Please check your internet connection.';
                } elseif (strpos($errorMessage, 'rate limit') !== false) {
                    $userMessage = 'Rate limit exceeded. Please try again later.';
                } elseif (strpos($errorMessage, 'authentication') !== false || $errorCode == 401) {
                    $userMessage = 'Authentication error. Please check your API key.';
                }
                
                echo 'data: Perplexity API Notification: <span class="font-weight-bold">' . htmlspecialchars($userMessage) . '</span>';
                echo "\n\n";
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
            }

            
            if (!empty($text)) {
                # Update credit balance
                $words = count(explode(' ', ($text)));
                HelperService::updateBalance($words, $model, $input_tokens, $output_tokens); 

                $content->result_text = $text;
                $content->input_tokens = $input_tokens;
                $content->output_tokens = $output_tokens;
                $content->words = $words;
                $content->save();
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


     /**
	*
	* Bedrock stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamBedrock($content_id, $max_results, $prompt, $max_words, $temperature)
    {
        return response()->stream(function () use ($content_id, $max_results, $prompt, $max_words, $temperature) {
            $streamService = new \App\Services\AmazonBedrock($content_id, $max_results, $prompt, $max_words, $temperature);
            
            $streamService->processTemplateStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Azure Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamAzure($content_id, $max_results, $prompt, $max_words, $temperature)
    {
        return response()->stream(function () use ($content_id, $max_results, $prompt, $max_words, $temperature) {
            $streamService = new \App\Services\AzureOpenai($content_id, $max_results, $prompt, $max_words, $temperature);
            
            $streamService->processTemplateStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }


    /**
	*
	* Azure Openai stream task
	* @param - file id in DB
	* @return - text stream
	*
	*/
    private function streamOpenRouter($content_id, $max_results, $prompt, $max_words, $temperature)
    {
        return response()->stream(function () use ($content_id, $max_results, $prompt, $max_words, $temperature) {
            $streamService = new \App\Services\OpenRouter($content_id, $max_results, $prompt, $max_words, $temperature);
            
            $streamService->processTemplateStream(function ($content) {
                echo 'data: ' . $content;
                echo "\n\n";
                ob_flush();
                flush();
            });
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }
    

    /**
	*
	* Process Custom template
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function customGenerate(Request $request) 
    {
        if ($request->ajax()) {
            $prompt = '';
            $text = '';
            $max_tokens = '';
            $counter = 1;

            # Check if user has access to the template
            $template = CustomTemplate::where('template_code', $request->template)->first();
            $flag = Language::where('language_code', $request->language)->first();

            if (auth()->user()->group == 'user') {
                if (config('settings.templates_access_user') != 'all' && config('settings.templates_access_user') != 'premium') {
                    if (is_null(auth()->user()->member_of)) {
                        if ($template->package == 'professional' && config('settings.templates_access_user') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if($template->package == 'premium' && (config('settings.templates_access_user') != 'premium' && config('settings.templates_access_user') != 'all')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if(($template->package == 'standard' || $template->package == 'all') && (config('settings.templates_access_user') != 'professional' && config('settings.templates_access_user') != 'standard')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        }
                    } else {
                        $user = User::where('id', auth()->user()->member_of)->first();
                        $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                        if ($plan) {
                            if ($plan->templates != 'all' && $plan->templates != 'premium') {          
                                if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                } else if ($template->package == 'professional' && $plan->templates != 'professional') {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'standard' && $plan->templates != 'professional')) {
                                    $data['status'] = 'error';
                                    $data['message'] = __('Your team subscription plan does not include support for this template category');
                                    return $data;
                                }                     
                            }
                        } else {
                            $data['status'] = 'error';
                            $data['message'] = __('Your team subscription plan does not include support for this template category');
                            return $data;
                        }
                       
                    }
        
                }
            } elseif (auth()->user()->group == 'admin') {
                if (is_null(auth()->user()->plan_id)) {
                    if (config('settings.templates_access_admin') != 'all' && config('settings.templates_access_admin') != 'premium') {
                        if ($template->package == 'professional' && config('settings.templates_access_admin') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if(($template->package == 'standard' || $template->package == 'all') && (config('settings.templates_access_admin') != 'standard' || config('settings.templates_access_admin') != 'professional')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if ($template->package == 'premium' && (config('settings.templates_access_admin') != 'all' && config('settings.templates_access_admin') != 'premium')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This template is not available for your account, subscribe to get a proper access');
                            return $data;
                        } 
                    }
                } else {
                    $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                    if ($plan->templates != 'all' && $plan->templates != 'premium') {        
                        if ($template->package == 'professional' && $plan->templates != 'professional') {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'standard' && $plan->templates != 'professional')) {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        } else if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                            $data['status'] = 'error';
                            $data['message'] = __('Your current subscription plan does not include support for this template category');
                            return $data;
                        }                 
                    }
                }
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($plan->templates != 'all' && $plan->templates != 'premium') {        
                    if ($template->package == 'premium' && ($plan->templates != 'all' && $plan->templates != 'premium')) {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    } else if ($template->package == 'professional' && $plan->templates != 'professional') {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    } else if(($template->package == 'standard' || $template->package == 'all') && ($plan->templates != 'professional' && $plan->templates != 'standard')) {
                        $data['status'] = 'error';
                        $data['message'] = __('Your current subscription plan does not include support for this template category');
                        return $data;
                    }                     
                }
            }

            # Check personal API keys
            if (config('settings.personal_openai_api') == 'allow') {
                if (is_null(auth()->user()->personal_openai_key)) {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                    return $data;
                }     
            } elseif (!is_null(auth()->user()->plan_id)) {
                $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if ($check_api->personal_openai_api) {
                    if (is_null(auth()->user()->personal_openai_key)) {
                        $data['status'] = 'error';
                        $data['message'] = __('You must include your personal Openai API key in your profile settings first');
                        return $data;
                    } 
                }    
            } 

            # Verify word limit
            if (auth()->user()->group == 'user') {
                $max_tokens = (config('settings.max_results_limit_user') < (int)$request->words) ? config('settings.max_results_limit_user') : (int)$request->words;
            } elseif (auth()->user()->group == 'admin') {
                $max_tokens = (config('settings.max_results_limit_admin') < (int)$request->words) ? config('settings.max_results_limit_user') : (int)$request->words;
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                $max_tokens = ($plan->max_tokens < (int)$request->words) ? $plan->max_tokens : (int)$request->words;
            }
            

            # Verify if user has enough credits
            $verify = HelperService::creditCheck($request->model, $max_tokens);
            if (isset($verify['status'])) {
                if ($verify['status'] == 'error') {
                    return $verify;
                }
            }


            # Filter for sensitive words
            $bad_words = Setting::where('name', 'words_filter')->first();
            $bad_words = explode(',', $bad_words->value);
            $bad_words = array_map('trim', $bad_words);
            $count_words = count($bad_words);
            $clean_value = '';

            if ($request->language == 'en-US') {
                $prompt = $template->prompt;
            } else {
                $prompt = "Provide response in " . $flag->language . '.\n\n '. $template->prompt . '\n\n Maximum result must be ' . $request->words. ' words.';
            }

            if (isset($request->tone)) {
                $prompt = $prompt . ' \n\n Voice of tone of the response must be ' . $request->tone . '.';
            }
            
    
            foreach ($request->all() as $key=>$value) {
                if (str_contains($key, 'input-field')) {

                    if ($count_words == 1) {
                        $clean_value = $value;
                        $prompt = str_replace('###' . $key . '###', $clean_value, $prompt);
                    } else {
                        foreach ($bad_words as $position => $word) {                      
                            if ($position == 0) {
                                $clean_value = $this->check_bad_words($word, $value, '');
                            } else {
                                $clean_value = $this->check_bad_words($word, $clean_value, '');
                            }                            
                        }

                        $prompt = str_replace('###' . $key . '###', $clean_value, $prompt);
                    }                   

                } 
            }

             # Add Brand information
             if ($request->brand == 'on') {
                $brand = BrandVoice::where('id', $request->company)->first();

                if ($brand) {
                    $product = '';
                    if ($request->service != 'none') {
                        foreach($brand->products as $key => $value) {
                            if ($key == $request->service)
                                $product = $value;
                        }
                    } 
                    
                    if ($request->service != 'none') {
                        $prompt .= ".\n Focus on my company and {$product['type']}'s information: \n";
                    } else {
                        $prompt .= ".\n Focus on my company's information: \n";
                    }
                    
                    if ($brand->name) {
                        $prompt .= "The company's name is {$brand->name}. ";
                    }

                    if ($brand->description) {
                        $prompt .= "The company's description is {$brand->description}. ";
                    }

                    if ($brand->website) {
                        $prompt .= ". The company's website is {$brand->website}. ";
                    }

                    if ($brand->tagline) {
                        $prompt .= "The company's tagline is {$brand->tagline}. ";
                    }

                    if ($brand->audience) {
                        $prompt .= "The company's target audience is: {$brand->audience}. ";
                    }

                    if ($brand->industry) {
                        $prompt .= "The company focuses in: {$brand->industry}. ";
                    }
    
                    if ($product) {
                        if ($product['name']) {
                            $prompt .= "The {$product['type']}'s name is {$product['name']}. \n";
                        }

                        if ($product['description']) {
                            $prompt .= "The {$product['type']} is about {$product['description']}. ";
                        }                        
                    }
                }
            }

     
            $plan_type = (auth()->user()->plan_id) ? 'paid' : 'free';

            session()->put('realtime', $request->internet);
            
            # Update credit balance
            $flag = Language::where('language_code', $request->language)->first();

            $content = new Content();
            $content->user_id = auth()->user()->id;
            $content->input_text = $prompt;
            $content->language = $request->language;
            $content->language_name = $flag->language;
            $content->language_flag = $flag->language_flag;
            $content->template_code = $request->template;
            $content->template_name = $template->name;
            $content->icon = $template->icon;
            $content->group = $template->group;
            $content->tokens = 0;
            $content->plan_type = $plan_type;
            $content->model = $request->model;
            $content->save();

            $data['status'] = 'success';    
            $data['max_results'] = $request->max_results;    
            $data['temperature'] = $request->creativity;    
            $data['max_words'] = $max_tokens;    
            $data['id'] = $content->id;
            return $data;  
        }
	}


    public function custom(Request $request)
    {
        # Check API keys
        if (config('settings.personal_openai_api') == 'allow') {
            if (is_null(auth()->user()->personal_openai_key)) {
                return response()->json(["status" => "error", 'message' => __('You must include your personal Openai API key in your profile settings first')]);
            } else {
                $openai_api = auth()->user()->personal_openai_key; 
            } 
        } elseif (!is_null(auth()->user()->plan_id)) {
            $check_api = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($check_api->personal_openai_api) {
                if (is_null(auth()->user()->personal_openai_key)) {
                    return response()->json(["status" => "error", 'message' => __('You must include your personal Openai API key in your profile settings first')]);
                } else {
                    $openai_api = auth()->user()->personal_openai_key;
                }
            } else {
                if (config('settings.openai_key_usage') !== 'main') {
                   $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                   array_push($api_keys, config('services.openai.key'));
                   $key = array_rand($api_keys, 1);
                   $openai_api = auth()->user()->$api_keys[$key];
               } else {
                    $openai_api = config('services.openai.key');
               }
           }
        } else {
            if (config('settings.openai_key_usage') !== 'main') {
                $api_keys = ApiKey::where('engine', 'openai')->where('status', true)->pluck('api_key')->toArray();
                array_push($api_keys, config('services.openai.key'));
                $key = array_rand($api_keys, 1);
                $openai_api = auth()->user()->$api_keys[$key];
            } else {
                $openai_api = config('services.openai.key');
            }
        }

        if (isset($request->model)) {
            $model = $request->model;
        } elseif (isset(auth()->user()->default_model_template)) {
            $model = auth()->user()->default_model_template;
        } else {
            $model = 'gpt-4o-mini';
        }
        
        $verify = HelperService::creditCheck($request->model, 100);
        if (isset($verify['status'])) {
            if ($verify['status'] == 'error') {
                return $verify;
            }
        }

        if ($request->content == null || $request->content == "") {
            return response()->json(["status" => "success", "message" => ""]);
        }

        if (isset($request->language)) {
            $language = Language::where('language_code', $request->language)->first();
            $prompt = $request->prompt . '. Provide response in the following language: '. $language->language;
        } else {
            $prompt = $request->prompt . '. If the task is not related to translation, return response in the language of the content text.';
        }

        $settings = MainSetting::first();

        $realtime = session()->get('realtime'); 

        # Append real time data
        if($realtime == 'on') {
            if ($settings->realtime_data_engine == 'serper') {
                $prompt = $this->realtimeData($prompt, 'serper');
            } elseif ($settings->realtime_data_engine == 'perplexity') {
                $prompt = $this->realtimeData($prompt, 'perplexity');
            } 
        }

        try {
            // Create OpenAI client
            $openai = new \OpenAI\Client($openai_api);
            
            $completion = $openai->chat()->create([
                'model' => $model,
                'messages' => [[
                    'role' => 'user',
                    'content' => "$prompt:\n\n$request->content"
                ]]
            ]);
    
            $response_content = $completion->choices[0]->message->content;
            $input_tokens = $completion->usage->promptTokens;
            $output_tokens = $completion->usage->completionTokens;
            $words = count(explode(' ', $response_content));
            
            // Update user's balance
            HelperService::updateBalance($words, $model, $input_tokens, $output_tokens);
    
            return response()->json([
                "status" => "success", 
                "message" => $response_content,
                "input_tokens" => $input_tokens,
                "output_tokens" => $output_tokens
            ]);
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return response()->json([
                "status" => "error", 
                "message" => "OpenAI API Error: " . $e->getMessage()
            ]);
        }
    }


    /**
	*
	* Realtime internet access
	* @return - prompt
	*
	*/
    public function realtimeData($prompt, $engine)
    {   
        $settings = ExtensionSetting::first();

        if ($engine == 'serper') {

            $client = new GuzzleClient;

            $headers = [
                'X-API-KEY'    => config('services.serper.key'),
                'Content-Type' => 'application/json',
            ];

            $body = [
                'q' => $prompt,
            ];

            try {

                $response = $client->post('https://google.serper.dev/search', [
                    'headers' => $headers,
                    'json'    => $body,
                ]);
    
                $result = $response->getBody()->getContents();

                $final_prompt = 'Prompt: ' . $prompt .
                                '\n\nWeb search json results: '
                                . json_encode($result) .
                                '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results (if the Web search results in the same context). If there are links included, show them in the following format: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not mention anything about the prompt text.';

                return $final_prompt; 

            } catch (Exception $e) {
                Log::info($response->getBody());
                return false;
            }              
    
        } elseif ($engine == 'perplexity') {

            $url = 'https://api.perplexity.ai/chat/completions';
            $api = $settings->perplexity_api;

            $payload = [
                'model'    => $settings->perplexity_realtime_model,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ];

            try {
                $response = Http::withToken($api)
                                ->withHeaders([
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);

                if ($response->successful()) {

                    $data = $response->json();
                    $response = $data['choices'][0]['message']['content'];

                    $final_prompt = 'Prompt: ' . $prompt .
                        '\n\nWeb search results: '
                        . $response .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). If there are links included, show them in the following format: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not mention anything about the prompt text.';

                    return $final_prompt;

                } else {
                    Log::info($response->body());
                    return false;
                }
            } catch (Exception $e) {
                Log::info($response->body());
                return false;
            }
        }

    }


    /**
     * Estimate token usage for a set of messages
     * 
     * @param array $messages Array of message objects
     * @param string $generatedText The text generated by the model
     * @return array Token usage estimates
     */
    private function estimateTokenUsage($messages, $generatedText) {
        $promptText = '';
        foreach ($messages as $message) {
            $promptText .= $message['content'] . "\n";
        }
        
        $promptTokens = $this->estimateTokenCount($promptText);
        $completionTokens = $this->estimateTokenCount($generatedText);
        
        return [
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
        ];
    }


    /**
     * Check for sensitive words
     *
     * @param - input text
     * @return bool
     */
    public function check_bad_words($word, $prompt, $replaceWith)
    {
        return preg_replace("/\S*$word\S*/i", $replaceWith, trim($prompt));
    }


    /**
	*
	* Save changes
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function save(Request $request) 
    {
        if ($request->ajax()) {  

            $document = Content::where('id', request('id'))->first(); 

            if ($document) {
                if ($document->user_id == auth()->user()->id){

                    if ($request->title == 'New Document') {
                        // Strip HTML tags
                        $cleanText = strip_tags($request->text);
                        // Remove markdown elements (common patterns)
                        $cleanText = preg_replace('/[*#_\[\]\(\)`~>]+/', '', $cleanText);
                        // Trim whitespace
                        $cleanText = trim($cleanText);
                        $title = mb_substr($cleanText, 0, 40) . '...';
                    } else {
                        $title = strip_tags($request->title);
                    }

                    $document->result_text = $request->text;
                    $document->title = $title;
                    $document->workbook = $request->workbook;
                    $document->save();
    
                    $data['status'] = 'success';
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    return $data;
                }  
            } else {
                $data['status'] = 'error';
                return $data;
            }
           
        }
	}


     /**
	*
	* Show brand products
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function brand(Request $request) 
    {
        if ($request->ajax()) {    

            $brand = BrandVoice::where('id', request('brand'))->first(); 

            if ($brand->user_id == Auth::user()->id){

                $data['status'] = 'success';
                $data['products'] = $brand->products;
                return $data;  
    
            } else{

                $data['status'] = 'error';
                return $data;
            }  
        }
	}


    /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favorite(Request $request) 
    {
        if ($request->ajax()) {

            $uploading = new UserService();
            $upload = $uploading->upload();
            if (!$upload['status']) return;  

            $template = Template::where('template_code', request('id'))->first(); 

            $favorite = FavoriteTemplate::where('template_code', $template->template_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteTemplate();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->template_code = $template->template_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


     /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favoriteCustom(Request $request) 
    {
        if ($request->ajax()) {

            $uploading = new UserService();
            $upload = $uploading->upload();
            if (!$upload['status']) return;  

            $template = CustomTemplate::where('template_code', request('id'))->first(); 

            $favorite = FavoriteTemplate::where('template_code', $template->template_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteTemplate();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->template_code = $template->template_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


    /**
     * Initial settings 
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $limit = $plan->max_tokens;    
        } elseif (auth()->user()->group == 'admin') {
            $limit = config('settings.max_results_limit_admin');    
        } else {
            $limit = config('settings.max_results_limit_user'); 
        }

        return $limit;
    }


    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewCustomTemplate(Request $request)
    {   
        $languages = Language::orderBy('languages.language', 'asc')->get();

        $template = CustomTemplate::where('template_code', $request->code)->first();
        $favorite = FavoriteTemplate::where('user_id', auth()->user()->id)->where('template_code', $template->template_code)->first(); 
        $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();
        $fields = json_encode($template->fields, true);
        $limit = $this->settings();

        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $brand_feature = $plan->brand_voice_feature;
        } else {
            if (config('settings.brand_voice_user_access') == 'allow') {
                $brand_feature = true;
            } else {
                $brand_feature = false;
            }
        }

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $internet_feature = $plan->internet_feature;
        } else {
            if (config('settings.internet_user_access') == 'allow') {
                $internet_feature = true;
            } else {
                $internet_feature = false;
            }
        }

        return view('user.templates.custom-template', compact('languages', 'template', 'favorite', 'workbooks', 'limit', 'fields', 'brands', 'brand_feature', 'internet_feature'));
    }


    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewOriginalTemplate(Request $request)
    {   
        $languages = Language::orderBy('languages.language', 'asc')->get();
        $template = Template::where('slug', $request->slug)->first();
        $favorite = FavoriteTemplate::where('user_id', auth()->user()->id)->where('template_code', $template->template_code)->first(); 
        $workbooks = Workbook::where('user_id', auth()->user()->id)->latest()->get();
        $fields = json_decode($template->fields, true);
        $limit = $this->settings();

        $brands = BrandVoice::where('user_id', auth()->user()->id)->get();

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $brand_feature = $plan->brand_voice_feature;
        } else {
            if (config('settings.brand_voice_user_access') == 'allow') {
                $brand_feature = true;
            } else {
                $brand_feature = false;
            }
        }

        if (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $internet_feature = $plan->internet_feature;
        } else {
            if (config('settings.internet_user_access') == 'allow') {
                $internet_feature = true;
            } else {
                $internet_feature = false;
            }
        }

        return view('user.templates.original-template', compact('languages', 'template', 'favorite', 'workbooks', 'limit', 'fields', 'brands', 'brand_feature', 'internet_feature'));
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getArticleGeneratorPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate article about " . $title . ". Focus on following keywords in the article: " . $keywords . ". The maximum length of the article must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate article about " . $title . ". Focus on following keywords in the article: " . $keywords . ". Tone of the article must be " . $tone . ". The maximum length of the article must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Generate article about " . $title . ". Focus on following keywords in the article: " . $keywords . ". The maximum length of the article must be " . $words . " words.\n\n";
            } else {
                $prompt = "Generate article about " . $title . ". Focus on following keywords in the article: " . $keywords . ". Tone of the article must be " . $tone . ". The maximum length of the article must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getParagraphGeneratorPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and meaningful paragraph about " . $title . ". Use following keywords in the paragraph: " . $keywords . ". The maximum length of the paragraph must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and meaningful paragraph about " . $title . ". Use following keywords in the paragraph: " . $keywords . ". Tone of the paragraph must be " . $tone . ". The maximum length of the paragraph must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long and meaningful paragraph about " . $title . ". Use following keywords in the paragraph: " . $keywords . ". The maximum length of the paragraph must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long and meaningful paragraph about " . $title . ". Use following keywords in the paragraph: " . $keywords . ". Tone of the paragraph must be " . $tone . ". The maximum length of the paragraph must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProsAndConsPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write pros and cons of these products: " . $title . ". Use following product description: " . $description . ". The maximum length of the pros and cons must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write pros and cons of these products: " . $title . ". Use following product description: " . $description . ". Tone of voice of the pros and cons must be " . $tone . ". The maximum length of the pros and cons must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write pros and cons of these products: " . $title . ". Use following product description: " . $description . ". The maximum length of the pros and cons must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write pros and cons of these products: " . $title . ". Use following product description: " . $description . ". Tone of the pros and cons must be " . $tone . ". The maximum length of the pros and cons must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTalkingPointsPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write short, simple and informative talking points for " . $title . ". And also similar talking points for subheadings: " . $keywords . ". The maximum length of the talking points must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write short, simple and informative talking points for " . $title . ". And also similar talking points for subheadings: " . $keywords . ". Tone of the talking points must be " . $tone . ". The maximum length of the talking points must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write short, simple and informative talking points for " . $title . ". And also similar talking points for subheadings: " . $keywords . ". The maximum length of the talking points must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write short, simple and informative talking points for " . $title . ". And also similar talking points for subheadings: " . $keywords . ". Tone of the talking points must be " . $tone . ". The maximum length of the talking points must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSummarizeTextPrompt($title, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Summarize this text in a short concise way: " . $title . ". The maximum length of the summary must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Summarize this text in a short concise way: " . $title . ". Tone of the summary must be " . $tone . ". The maximum length of the summary must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Summarize this text in a short concise way: " . $title . ". The maximum length of the summary must be " . $words . " words.\n\n";
            } else {
                $prompt = "Summarize this text in a short concise way: " . $title . ". Tone of the summary must be " . $tone . ". The maximum length of the summary must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductDescriptionPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative product description for: " . $title . ". Target audience is: " . $audience . ". Use this description: " . $description . ". The maximum length of the product description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative product description for: " . $title . ". Target audience is: " . $audience . ". Use this description: " . $description . ". Tone of the product description must be " . $tone . ". The maximum length of the product description must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long creative product description for: " . $title . ". Target audience is: " . $audience . ". Use this description: " . $description . ". The maximum length of the product description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long creative product description for: " . $title . ". Target audience is: " . $audience . ". Use this description: " . $description . ". Tone of the product description must be " . $tone . ". The maximum length of the product description must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStartupNameGeneratorPrompt($keywords, $description, $language, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate cool, creative, and catchy names for startup description: " . $description . "\n\nSeed words: " . $keywords . ". The maximum length of the startup names must be " . $words . " words.\n\n";
            return $prompt;
        } else {
            $prompt = "Generate cool, creative, and catchy names for startup description: " . $description . "\n\nSeed words: " . $keywords . ". The maximum length of the startup names must be " . $words . " words.\n\n";
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductNameGeneratorPrompt($keywords, $description, $language, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 5 creative product names: " . $description . "\n\nSeed words: " . $keywords . ". The maximum length of the product names must be " . $words . " words.\n\n";
            return $prompt;
        } else {
            $prompt = "Create 5 creative product names: " . $description . "\n\nSeed words: " . $keywords . ". The maximum length of the product names must be " . $words . " words.\n\n";
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMetaDescriptionPrompt($title, $keywords, $description, $language, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write SEO meta description for: " . $description . "\n\nWebsite name is: " . $title . "\n\nSeed words: " . $keywords . ". The maximum length of the meta description must be " . $words . " words.\n\n";
            return $prompt;
        } else {
            $prompt = "Write SEO meta description for: " . $description . "\n\nWebsite name is: " . $title . "\n\nSeed words: " . $keywords . ". The maximum length of the meta description must be " . $words . " words.\n\n";
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFAQsPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate list of 10 frequently asked questions based this description: " . $description . ". Product name:" . $title . ". The maximum length of the frequently asked questions must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate list of 10 frequently asked questions based this description: " . $description . ". Product name:" . $title . ". Tone of voice of the frequently asked questions must be " . $tone . ". The maximum length of the frequently asked questions must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Generate list of 10 frequently asked questions based this description: " . $description . ". Product name:" . $title . ". The maximum length of the frequently asked questions must be " . $words . " words.\n\n";
            } else {
                $prompt = "Generate list of 10 frequently asked questions based this description: " . $description . ". Product name:" . $title . ". Tone of the frequently asked questions must be " . $tone . ". The maximum length of the frequently asked questions must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFAQAnswersPrompt($title, $question, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate creative 5 answers this question: " . $question . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the answers must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate creative 5 answers this question: " . $question . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the answers must be " . $tone . ". The maximum length of the answers must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Generate creative 5 answers this question: " . $question . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the answers must be " . $words . " words.\n\n";
            } else {
                $prompt = "Generate creative 5 answers this question: " . $question . ". Product name: " . $title . ". Product description: " . $description . ". Tone of the answers must be " . $tone . ". The maximum length of the answers must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestimonialsPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create creative customer reviews for this product. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the customer reviews must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create creative customer reviews for this product. Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the customer reviews must be " . $tone . ". The maximum length of the customer reviews must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Create creative customer reviews for this product. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the customer reviews must be " . $words . " words.\n\n";
            } else {
                $prompt = "Create creative customer reviews for this product. Product name: " . $title . ". Product description: " . $description . ". Tone of the customer reviews must be " . $tone . ". The maximum length of the customer reviews must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogTitlesPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate 10 catchy blog titles for: " . $description . ". The maximum length of the titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate 10 catchy blog titles for: " . $description . ". Tone of voice of the blog titles must be " . $tone . ". The maximum length of the blog titles must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Generate 10 catchy blog titles for: " . $description . ". The maximum length of the blog titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Generate 10 catchy blog titles for: " . $description . ". Tone of the blog titles must be " . $tone . ". The maximum length of the blog titles must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogSectionPrompt($title, $subheadings, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a full blog section with at least 5 large paragraphs about: " . $title . ". Split by subheadings: " . $subheadings . ". The maximum result length must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a full blog section with at least 5 large paragraphs about: " . $title . ". Split by subheadings: " . $subheadings . ". Tone of voice of the blog section must be " . $tone . ". The maximum result length must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a full blog section with at least 5 large paragraphs about: " . $title . ". Split by subheadings: " . $subheadings . ". The maximum result length must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a full blog section with at least 5 large paragraphs about: " . $title . ". Split by subheadings: " . $subheadings . ". Tone of the blog section must be " . $tone . ". The maximum result length must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogIdeasPrompt($title, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write interesting blog ideas and outline about: " . $title . ". The maximum length of the blog ideas must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write interesting blog ideas and outline about: " . $title . ". Tone of voice of the blog ideas must be " . $tone . ". The maximum length of the blog ideas must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write interesting blog ideas and outline about: " . $title . ". The maximum length of the blog ideas must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write interesting blog ideas and outline about: " . $title . ". Tone of the blog ideas must be " . $tone . ". The maximum length of the blog ideas must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogIntrosPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting blog post intro about: " . $description . ". Blog post title is: " . $title . ". The maximum length of the blog intro must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting blog post intro about: " . $description . ". Blog post title is: " . $title . ". Tone of voice of the blog intro must be " . $tone . ". The maximum length of the blog intro must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an interesting blog post intro about: " . $description . ". Blog post title is: " . $title . ". The maximum length of the blog intro must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an interesting blog post intro about: " . $description . ". Blog post title is: " . $title . ". Tone of the blog intro must be " . $tone . ". The maximum length of the blog intro must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogConclusionPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a comprehensive blog article conclusion for: " . $description . ". Blog article title: " . $title . ". The maximum length of the blog conclusion must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a comprehensive blog article conclusion for: " . $description . ". Blog article title: " . $title . ". Tone of voice of the blog conclusion must be " . $tone . ". The maximum length of the blog conclusion must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a comprehensive blog article conclusion for: " . $description . ". Blog article title: " . $title . ". The maximum length of the blog conclusion must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a comprehensive blog article conclusion for: " . $description . ". Blog article title: " . $title . ". Tone of the blog conclusion must be " . $tone . ". The maximum length of the blog conclusion must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContentRewriterPrompt($title, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Improve and rewrite the text in a creative and smart way: " . $title . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Improve and rewrite the text in a creative and smart way: " . $title . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Improve and rewrite the text in a creative and smart way: " . $title . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Improve and rewrite the text in a creative and smart way: " . $title . ". Tone of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFacebookAdsPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a creative ad for the following product to run on Facebook aimed at: " . $audience . ". Product name is: " . $title . ". Product description is: " . $description . ". The maximum length of the ad must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a creative ad for the following product to run on Facebook aimed at: " . $audience . ". Product name is: " . $title . ". Product description is: " . $description . ". Tone of voice of the ad must be " . $tone . ". The maximum length of the ad must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a creative ad for the following product to run on Facebook aimed at: " . $audience . ". Product name is: " . $title . ". Product description is: " . $description . ". The maximum length of the ad must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a creative ad for the following product to run on Facebook aimed at: " . $audience . ". Product name is: " . $title . ". Product description is: " . $description . ". Tone of voice of the ad must be " . $tone . ". The maximum length of the ad must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVideoDescriptionsPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write compelling YouTube description to get people interested in watching. Video description: " . $description . ". The maximum length of the video description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write compelling YouTube description to get people interested in watching. Video description: " . $description . ". Tone of voice of the video description must be " . $tone . ". The maximum length of the video description must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write compelling YouTube description to get people interested in watching. Video description: " . $description . ". The maximum length of the video description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write compelling YouTube description to get people interested in watching. Video description: " . $description . ". Tone of voice of the video description must be " . $tone . ". The maximum length of the video description must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVideoTitlesPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write compelling YouTube video title for the provided video description to get people interested in watching. Video description: " . $description . ". The maximum length of the video title must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write compelling YouTube video title for the provided video description to get people interested in watching. Video description: " . $description . ". Tone of voice of the video title must be " . $tone . ". The maximum length of the video title must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write compelling YouTube video title for the provided video description to get people interested in watching. Video description: " . $description . ". The maximum length of the video title must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write compelling YouTube video title for the provided video description to get people interested in watching. Video description: " . $description . ". Tone of voice of the video title must be " . $tone . ". The maximum length of the video title must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getYoutubeTagsGeneratorPrompt($description, $language)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Generate SEO-optimized YouTube tags and keywords for: " . $description . ".\n\n";
            return $prompt;
        } else {
            $prompt = "Generate SEO-optimized YouTube tags and keywords for: " . $description . ".\n\n";
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInstagramCaptionsPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Grab attention with catchy captions for this Instagram post: " . $description . ". The maximum length of the caption must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Grab attention with catchy captions for this Instagram post: " . $description . ". Tone of voice of the caption must be " . $tone . ". The maximum length of the caption must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Grab attention with catchy captions for this Instagram post: " . $description . ". The maximum length of the caption must be " . $words . " words.\n\n";
            } else {
                $prompt = "Grab attention with catchy captions for this Instagram post: " . $description . ". Tone of voice of the caption must be " . $tone . ". The maximum length of the caption must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInstagramHashtagsPrompt($keyword, $language, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create hashtags to use for these Instagram keywords: " . $keyword . ".\n\n";
            return $prompt;
        } else {
            $prompt = "Create hashtags to use for these Instagram keywords: " . $keyword . ".\n\n";
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSocialPostPersonalPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a personal social media post about: " . $description . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a personal social media post about: " . $description . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a personal social media post about: " . $description . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a personal social media post about: " . $description . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSocialPostBusinessPrompt($description, $title, $post, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create a large professional social media post for my company. Post description: " . $post . ". Company description: " . $description . ". Company name: " . $title . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create a large professional social media post for my company. Post description: " . $post . ". Company description: " . $description . ". Company name: " . $title . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Create a large professional social media post for my company. Post description: " . $post . ". Company description: " . $description . ". Company name: " . $title . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Create a large professional social media post for my company. Post description: " . $post . ". Company description: " . $description . ". Company name: " . $title . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFacebookHeadlinesPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative headline for the following product to run on Facebook aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative headline for the following product to run on Facebook aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long creative headline for the following product to run on Facebook aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long creative headline for the following product to run on Facebook aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGoogleHeadlinesPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write catchy 30-character headlines to promote your product with Google Ads. Product name: " . $title . ". Product description: " . $description . ". Target audience for ad: " . $audience . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write catchy 30-character headlines to promote your product with Google Ads. Product name: " . $title . ". Product description: " . $description . ". Target audience for ad: " . $audience . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write catchy 30-character headlines to promote your product with Google Ads. Product name: " . $title . ". Product description: " . $description . ". Target audience for ad: " . $audience . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write catchy 30-character headlines to promote your product with Google Ads. Product name: " . $title . ". Product description: " . $description . ". Target audience for ad: " . $audience . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGoogleAdsPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a Google Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a Google Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the ad description must be " . $tone . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a Google Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a Google Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the ad description must be " . $tone . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPASPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write problem-agitate-solution for the following product description: " . $description . ". Product name: " . $title . ". Target audience: " . $audience . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write problem-agitate-solution for the following product description: " . $description . ". Product name: " . $title . ". Target audience: " . $audience . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write problem-agitate-solution for the following product description: " . $description . ". Product name: " . $title . ". Target audience: " . $audience . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write problem-agitate-solution for the following product description: " . $description . ". Product name: " . $title . ". Target audience: " . $audience . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAcademicEssayPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an academic essay about: " . $title . ". Use following keywords in the essay: " . $keywords . ". The maximum length of the essay must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an academic essay about: " . $title . ". Use following keywords in the essay: " . $keywords . ". Tone of voice of the essay must be " . $tone . ". The maximum length of the essay must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an academic essay about: " . $title . ". Use following keywords in the essay: " . $keywords . ". The maximum length of the essay must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an academic essay about: " . $title . ". Use following keywords in the essay: " . $keywords . ". Tone of voice of the essay must be " . $tone . ". The maximum length of the essay must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWelcomeEmailPrompt($title, $description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a welcome email about: " . $description . ". Our company or product name is: " . $title . ". Target audience is: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a welcome email about: " . $description . ". Our company or product name is: " . $title . ". Target audience is: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a welcome email about: " . $description . ". Our company or product name is: " . $title . ". Target audience is: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a welcome email about: " . $description . ". Our company or product name is: " . $title . ". Target audience is: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getColdEmailPrompt($title, $description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a cold email about: " . $description . ". Our company or product name is: " . $title . ". Context to include in the cold email: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a cold email about: " . $description . ". Our company or product name is: " . $title . ". Context to include in the cold email: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a cold email about: " . $description . ". Our company or product name is: " . $title . ". Context to include in the cold email: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a cold email about: " . $description . ". Our company or product name is: " . $title . ". Context to include in the cold email: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFollowUpEmailPrompt($title, $description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a follow up email about: " . $description . ". Our company or product name is: " . $title . ". Following up after: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a follow up email about: " . $description . ". Our company or product name is: " . $title . ". Following up after: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a follow up email about: " . $description . ". Our company or product name is: " . $title . ". Following up after: " . $keywords . ". The maximum length of the email must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a follow up email about: " . $description . ". Our company or product name is: " . $title . ". Following up after: " . $keywords . ". Tone of voice of the email must be " . $tone . ". The maximum length of the email must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreativeStoriesPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative story about: " . $description . ". The maximum length of the story must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative story about: " . $description . ". Tone of voice of the story must be " . $tone . ". The maximum length of the story must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long creative story about: " . $description . ". The maximum length of the story must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long creative story about: " . $description . ". Tone of voice of the story must be " . $tone . ". The maximum length of the story must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGrammarCheckerPrompt($description, $language)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Check and correct grammar of this text: " . $description . "\n\n";
            return $prompt;
        } else {
            $prompt = "Check and correct grammar of this text: " . $description . "\n\n";
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSummarize2ndGraderPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Summarize this text for 2nd grader: " . $description . ". The maximum length of the summary must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Summarize this text for 2nd grader: " . $description . ". Tone of voice of the summary must be " . $tone . ". The maximum length of the summary must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Summarize this text for 2nd grader: " . $description . ". The maximum length of the summary must be " . $words . " words.\n\n";
            } else {
                $prompt = "Summarize this text for 2nd grader: " . $description . ". Tone of voice of the summary must be " . $tone . ". The maximum length of the summary must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVideoScriptsPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting video script about: " . $description . ". The maximum length of the video script must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting video script about: " . $description . ". Tone of voice of the video script must be " . $tone . ". The maximum length of the video script must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an interesting video script about: " . $description . ". The maximum length of the video script must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an interesting video script about: " . $description . ". Tone of voice of the video script must be " . $tone . ". The maximum length of the video script must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAmazonProductPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write attention grabbing Amazon marketplace product description for: " . $title . ". Use following keywords in the product description: " . $keywords . ". The maximum length of the product description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write attention grabbing Amazon marketplace product description for: " . $title . ". Use following keywords in the product description: " . $keywords . ". Tone of voice of the product description must be " . $tone . ". The maximum length of the product description must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write attention grabbing Amazon marketplace product description for: " . $title . ". Use following keywords in the product description: " . $keywords . ". The maximum length of the product description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write attention grabbing Amazon marketplace product description for: " . $title . ". Use following keywords in the product description: " . $keywords . ". Tone of voice of the product description must be " . $tone . ". The maximum length of the product description must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTextExtenderPrompt($description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Extend this text further with more creative content: " . $description . ". Use following keywords in the extended text: " . $keywords . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Extend this text further with more creative content: " . $description . ". Use following keywords in the extended text: " . $keywords . ". Tone of voice of the extended text must be " . $tone . ". The maximum length of the extended text must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Extend this text further with more creative content: " . $description . ". Use following keywords in the extended text: " . $keywords . ". The maximum length of the extended text must be " . $words . " words.\n\n";
            } else {
                $prompt = "Extend this text further with more creative content: " . $description . ". Use following keywords in the extended text: " . $keywords . ". Tone of voice of the extended text must be " . $tone . ". The maximum length of the extended text must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRewriteTextPrompt($description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Rewrite this text in a more creative way: " . $description . ". Use following keywords in the text: " . $keywords . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Rewrite this text in a more creative way: " . $description . ". Use following keywords in the text: " . $keywords . ". Tone of voice of the extended text must be " . $tone . ". The maximum length of the extended text must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Rewrite this text in a more creative way: " . $description . ". Use following keywords in the text: " . $keywords . ". The maximum length of the text must be " . $words . " words.\n\n";
            } else {
                $prompt = "Rewrite this text in a more creative way: " . $description . ". Use following keywords in the text: " . $keywords . ". Tone of voice of the text must be " . $tone . ". The maximum length of the text must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSongLyricsPrompt($description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a nice song lyrics that rhyme well about: " . $description . ". Use following keywords in the lyrics: " . $keywords . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a nice song lyrics that rhyme well about: " . $description . ". Use following keywords in the lyrics: " . $keywords . ". Tone of voice of the lyrics must be " . $tone . ". The maximum length of the extended lyrics must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a nice song lyrics that rhyme well about: " . $description . ". Use following keywords in the lyrics: " . $keywords . ". The maximum length of the lyrics must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a nice song lyrics that rhyme well about: " . $description . ". Use following keywords in the lyrics: " . $keywords . ". Tone of voice of the lyrics must be " . $tone . ". The maximum length of the lyrics must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessIdeasPrompt($description, $language)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Suggest innovative business ideas for this industry description: " . $description . "\n\n";
            return $prompt;
        } else {
            $prompt = "Suggest innovative business ideas for this industry description: " . $description . "\n\n";       
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLinkedinPostPrompt($description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an inspiring linkedin post about: " . $description . ". Use following keywords in the post: " . $keywords . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an inspiring linkedin post about: " . $description . ". Use following keywords in the post: " . $keywords . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an inspiring linkedin post about: " . $description . ". Use following keywords in the post: " . $keywords . ". The maximum length of the post must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an inspiring linkedin post about: " . $description . ". Use following keywords in the post: " . $keywords . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompanyBioPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write clear and interesting company bio. Company name: " . $title . ". Company description: " . $description . ". The maximum length of the bio must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write clear and interesting company bio. Company name: " . $title . ". Company description: " . $description . ". Tone of voice of the post must be " . $tone . ". The maximum length of the post must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write clear and interesting company bio. Company name: " . $title . ". Company description: " . $description . ". The maximum length of the bio must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write clear and interesting company bio. Company name: " . $title . ". Company description: " . $description . ". Tone of voice of the bio must be " . $tone . ". The maximum length of the bio must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmailSubjectPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an attention grabbing email subject line for: " . $description . ". The maximum length of the subject line must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an attention grabbing email subject line for: " . $description . ". Tone of voice of the subject line must be " . $tone . ". The maximum length of the subject line must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an attention grabbing email subject line for: " . $description . ". The maximum length of the subject line must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an attention grabbing email subject line for: " . $description . ". Tone of voice of the subject line must be " . $tone . ". The maximum length of the subject line must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductBenefitsPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 10 unique and intersting product benefits. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the product benefits must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 10 unique and intersting product benefits. Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the product benefits must be " . $tone . ". The maximum length of the product benefits must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Create 10 unique and intersting product benefits. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the product benefits must be " . $words . " words.\n\n";
            } else {
                $prompt = "Create 10 unique and intersting product benefits. Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the product benefits must be " . $tone . ". The maximum length of the product benefits must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellingTitlesPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write the most attention grabbing 5 selling titles. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write the most attention grabbing 5 selling titles. Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the titles must be " . $tone . ". The maximum length of the titles must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write the most attention grabbing 5 selling titles. Product name: " . $title . ". Product description: " . $description . ". The maximum length of the titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write the most attention grabbing 5 selling titles. Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the titles must be " . $tone . ". The maximum length of the titles must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductComparisonPrompt($title, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed product comparison between these products: " . $title . ". The maximum length of the comparison must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed product comparison between these products: " . $title . ". Tone of voice of the comparison must be " . $tone . ". The maximum length of the comparison must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a detailed product comparison between these products: " . $title . ". The maximum length of the comparison must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a detailed product comparison between these products: " . $title . ". Tone of voice of the comparison must be " . $tone . ". The maximum length of the comparison must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductCharacteristicsPrompt($title, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write detailed list of product characteristics for: " . $title . ". User following keywords: " . $keywords . ". The maximum length of the characteristics must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write detailed list of product characteristics for: " . $title . ". User following keywords: " . $keywords . ". Tone of voice of the characteristics must be " . $tone . ". The maximum length of the characteristics must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write detailed list of product characteristics for: " . $title . ". User following keywords: " . $keywords . ". The maximum length of the characteristics must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write detailed list of product characteristics for: " . $title . ". User following keywords: " . $keywords . ". Tone of voice of the characteristics must be " . $tone . ". The maximum length of the characteristics must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTwitterTweetsPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a trending tweet for a Twitter post about: " . $description . ". The maximum length of the tweet must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a trending tweet for a Twitter post about: " . $description . ". Tone of voice of the tweet must be " . $tone . ". The maximum length of the tweet must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a trending tweet for a Twitter post about: " . $description . ". The maximum length of the tweet must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a trending tweet for a Twitter post about: " . $description . ". Tone of voice of the tweet must be " . $tone . ". The maximum length of the tweet must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTiktokScriptsPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a creating step by stepvideo scripts  with actions for each step. Video is about: " . $description . ". The maximum length of the idea must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a creating step by stepvideo scripts  with actions for each step. Video is about: " . $description . ". Tone of voice of the idea must be " . $tone . ". The maximum length of the idea must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a creating step by step video scripts with actions for each step. Video is about: " . $description . ". The maximum length of the idea must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a creating step by step video scripts with actions for each step. Video is about: " . $description . ". Tone of voice of the idea must be " . $tone . ". The maximum length of the idea must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLinkedinHeadlinesPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative headline for the following product to run on Linkedin aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative headline for the following product to run on Linkedin aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long creative headline for the following product to run on Linkedin aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long creative headline for the following product to run on Linkedin aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLinkedinAdDescriptionPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a Linkedin Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a Linkedin Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the ad description must be " . $tone . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a Linkedin Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a Linkedin Ads description that makes your ad stand out and generates leads. Target audience: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the ad description must be " . $tone . ". The maximum length of the ad description must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSMSNotificationPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 10 eye catching notification messages about: " . $description . ". The maximum length of the messages must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 10 eye catching notification messages about: " . $description . ". Tone of voice of the messages must be " . $tone . ". The maximum length of the messages must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Create 10 eye catching notification messages about: " . $description . ". The maximum length of the messages must be " . $words . " words.\n\n";
            } else {
                $prompt = "Create 10 eye catching notification messages about: " . $description . ". Tone of voice of the messages must be " . $tone . ". The maximum length of the messages must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getToneChangerPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Change tone of voice of this text: " . $description . "\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Change tone of voice of this text: " . $description . ". Tone of voice of must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Change tone of voice of this text: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Change tone of voice of this text: " . $description . ". Tone of voice of must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAmazonProductFeaturesPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a interesting and detailed product descriptions to gain more sells on Amazon for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the product descriptions must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a interesting and detailed product descriptions to gain more sells on Amazon for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the product descriptions must be " . $tone . ". The maximum length of the product descriptions must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a interesting and detailed product descriptions to gain more sells on Amazon for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the product descriptions must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a interesting and detailed product descriptions to gain more sells on Amazon for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the product descriptions must be " . $tone . ". The maximum length of the product descriptions must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDictionaryPrompt($title, $language)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Act as an advanced vocabulary dictionary. Provide full breakdown details of this word as a vocabulary dictionary. Target word: " . $title . "\n\n";
            return $prompt;
        } else {
            $prompt = "Act as an advanced vocabulary dictionary. Provide full breakdown details of this word as a vocabulary dictionary. Target word: " . $title . ".\n\n";       
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrivacyPolicyPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and detailed privacy policy with sub-sections for each points. Company name: " . $title . ". Use following description for creating a privacy policy: " . $description . ". The maximum length of the privacy policy must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and detailed privacy policy with sub-sections for each points. Company name: " . $title . ". Use following description for creating a privacy policy: " . $description . ". Tone of voice of the privacy policy must be " . $tone . ". The maximum length of the privacy policy must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long and detailed privacy policy with sub-sections for each points. Company name: " . $title . ". Use following company details for creating a privacy policy: " . $description . ". The maximum length of the privacy policy must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long and detailed privacy policy with sub-sections for each points. Company name: " . $title . ". Use following company details for creating a privacy policy: " . $description . ". Tone of voice of the privacy policy must be " . $tone . ". The maximum length of the privacy policy must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTermsAndConditionsPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and detailed terms and conditions page with a sub-sections for each points. Company name: " . $title . ". Use following description for creating a terms and conditions pages: " . $description . ". The maximum length of the terms and conditions page must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long and detailed terms and conditions page with a sub-sections for each points. Company name: " . $title . ". Use following description for creating a terms and conditions pages: " . $description . ". Tone of voice of the terms and conditions page must be " . $tone . ". The maximum length of the terms and conditions page must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long and detailed terms and conditions page with a sub-sections for each points. Company name: " . $title . ". Use following company details for creating a terms and conditions pages: " . $description . ". The maximum length of the terms and conditions page must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long and detailed terms and conditions page with a sub-sections for each points. Company name: " . $title . ". Use following company details for creating a terms and conditions pages: " . $description . ". Tone of voice of the terms and conditions page must be " . $tone . ". The maximum length of the terms and conditions page must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClickbaitTitlesPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 5 attention grabbing and sale generating clickbait titles for this product description: " . $description . ". The maximum length of the titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create 5 attention grabbing and sale generating clickbait titles for this product description: " . $description . ". Tone of voice of the titles must be " . $tone . ". The maximum length of the titles must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Create 5 attention grabbing and sale generating clickbait titles for this product description: " . $description . ". The maximum length of the titles must be " . $words . " words.\n\n";
            } else {
                $prompt = "Create 5 attention grabbing and sale generating clickbait titles for this product description: " . $description . ". Tone of voice of the titles must be " . $tone . ". The maximum length of the titles must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompanyPressReleasePrompt($title, $description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed and interesting company press release about: " . $keywords . ". Company name: " . $title . ". Company information: " . $description . ". The maximum length of the press release must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed and interesting company press release about: " . $keywords . ". Company name: " . $title . ". Company information: " . $description . ". Tone of voice of the press release must be " . $tone . ". The maximum length of the press release must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a detailed and interesting company press release about: " . $keywords . ". Company name: " . $title . ". Company information: " . $description . ". The maximum length of the press release must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a detailed and interesting company press release about: " . $keywords . ". Company name: " . $title . ". Company information: " . $description . ". Tone of voice of the press release must be " . $tone . ". The maximum length of the press release must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


    /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductPressReleasePrompt($title, $description, $keywords, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed and interesting product press release about: " . $keywords . ". Product name: " . $title . ". Product information: " . $description . ". The maximum length of the press release must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a detailed and interesting product press release about: " . $keywords . ". Product name: " . $title . ". Product information: " . $description . ". Tone of voice of the press release must be " . $tone . ". The maximum length of the press release must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a detailed and interesting product press release about: " . $keywords . ". Product name: " . $title . ". Product information: " . $description . ". The maximum length of the press release must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a detailed and interesting product press release about: " . $keywords . ". Product name: " . $title . ". Product information: " . $description . ". Tone of voice of the press release must be " . $tone . ". The maximum length of the press release must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAIDAPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting formula: Attention-Interest-Desire-Action (AIDA) Framework to write a clear user actions for this product: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting formula: Attention-Interest-Desire-Action (AIDA) Framework to write a clear user actions for this product: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Use copywriting formula: Attention-Interest-Desire-Action (AIDA) Framework to write a clear user actions for this product: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Use copywriting formula: Attention-Interest-Desire-Action (AIDA) Framework to write a clear user actions for this product: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }

     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBABPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting formula: Before–After–Bridge (BAB) Framework, to write a appealing marketing statement for this product: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting formula: Before–After–Bridge (BAB) Framework, to write a appealing marketing statement for this product: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Use copywriting formula: Before–After–Bridge (BAB) Framework, to write a appealing marketing statement for this product: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Use copywriting formula: Before–After–Bridge (BAB) Framework, to write a appealing marketing statement for this product: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPPPPPrompt($title, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting 4P formula: Promise–Picture–Proof–Push (PPPP) Framework, to to craft persuasive content that moves readers to action. Produt name: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Use copywriting 4P formula: Promise–Picture–Proof–Push (PPPP) Framework, to to craft persuasive content that moves readers to action. Produt name: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Use copywriting 4P formula: Promise–Picture–Proof–Push (PPPP) Framework, to to craft persuasive content that moves readers to action. Produt name: " . $title . ". Product description: " . $description . ". The maximum length of the result must be " . $words . " words.\n\n";
            } else {
                $prompt = "Use copywriting 4P formula: Promise–Picture–Proof–Push (PPPP) Framework, to to craft persuasive content that moves readers to action. Produt name: " . $title . ". Product description: " . $description . ". Tone of voice of the result must be " . $tone . ". The maximum length of the result must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandNamesPrompt($description, $language, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            $prompt = "Provide a response in " . $target_language->language . " language.\n\n Create creative and unique brand names for: " . $description . ". The maximum length of the brand names must be " . $words . " words.\n\n";
            return $prompt;
        } else {
            $prompt = "Create creative and unique brand names for: " . $description . ". The maximum length of the brand names must be " . $words . " words.\n\n";
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdHeadlinesPrompt($title, $audience, $description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative ad headline for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write a long creative ad headline for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write a long creative ad headline for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". The maximum length of the headline must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write a long creative ad headline for the following product aimed at: " . $audience . ". Product name: " . $title . ". Product description: " . $description . ". Tone of voice of the headline must be " . $tone . ". The maximum length of the headline must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }


     /** 
     * Generate template prompt.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewsletterGeneratorPrompt($description, $language, $tone, $words)
    {   
        if ($language != 'en-US') {
            $target_language = Language::where('language_code', $language)->first();
            if ($tone == 'none') {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting and long newsletter about: " . $description . ". The maximum length of the newsletter must be " . $words . " words.\n\n";
            } else {
                $prompt = "Provide a response in " . $target_language->language . " language.\n\n Write an interesting and long newsletter about: " . $description . ". Tone of voice of the newsletter must be " . $tone . ". The maximum length of the newsletter must be " . $words . " words.\n\n";
            }
            return $prompt;
        } else {
            if ($tone == 'none') {
                $prompt = "Write an interesting and long newsletter about: " . $description . ". The maximum length of the newsletter must be " . $words . " words.\n\n";
            } else {
                $prompt = "Write an interesting and long newsletter about: " . $description . ". Tone of voice of the newsletter must be " . $tone . ". The maximum length of the newsletter must be " . $words . " words.\n\n";
            }           
            return $prompt;
        }
    }




}
