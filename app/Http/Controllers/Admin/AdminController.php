<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Services\Statistics\PaymentsService;
use App\Services\Statistics\CostsService;
use App\Services\Statistics\RegistrationService;
use App\Services\Statistics\UserRegistrationMonthlyService;
use App\Services\Statistics\UserRegistrationYearlyService;
use App\Services\Statistics\DavinciUsageService;
use App\Services\Statistics\GoogleAnalyticsService;
use App\Services\Statistics\SupportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Cache;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $payment = new PaymentsService($year, $month);
        $support = new SupportService();
        $cost = new CostsService($year, $month);
        $davinci = new DavinciUsageService($month, $year);
        $registration = new RegistrationService($year, $month);
        $user_registration = new UserRegistrationMonthlyService($month);
        $registration_yearly = new UserRegistrationYearlyService($year);
        $google = new GoogleAnalyticsService();

        $total_monthly = [
            'income_current_month' => $payment->getTotalPaymentsCurrentMonth(),
            'spending_current_month' => $cost->getTotalSpendingCurrentMonth(),
        ];

        $total = [
            'total_users' => $registration->getTotalUsers(),
            'total_subscribers' => $registration->getTotalSubscribers(),
            'total_nonsubscribers' => $registration->getTotalReferred(),
            'total_referred' => $registration->getTotalNonSubscribers(),
            'total_income' => $payment->getTotalPayments(),
            'total_spending' => $cost->getTotalSpending(),
            'referral_earnings' => $payment->getTotalReferralEarnings(),
            'referral_payouts' => $payment->getTotalReferralPayouts(),
        ];

        $today = [
            'revenue' => $payment->revenueToday(),
            'new_users' => $registration->registrationsToday(),
            'subscribers' => $registration->subscribersToday(),
            'transactions' => $payment->transactionsToday(),
            'tickets' => $support->ticketsToday(),
            'online_users' => $registration->onlineToday(),
            'tokens_used' => $davinci->tokensUsedToday(),
            'media_used' => $davinci->mediaUsedToday(),
            'contents' => $davinci->contentsToday(),
        ];
       
        $total_data_monthly = [
            'new_subscribers_current_month' => $registration->getNewSubscribersCurrentMonth(),
            'new_subscribers_past_month' => $registration->getNewSubscribersPastMonth(),
            'words_current_month' => $davinci->getTotalWordsCurrentMonth(),
            'words_past_month' => $davinci->getTotalWordsPastMonth(),
            'images_current_month' => $davinci->getTotalImagesCurrentMonth(),
            'images_past_month' => $davinci->getTotalImagesCurrentMonth(),
            'contents_current_month' => $davinci->getTotalContentsCurrentMonth(),
            'contents_past_month' => $davinci->getTotalContentsPastMonth(),
            'chats_current_month' => $davinci->getTotalChatsCurrentMonth(),
            'chats_past_month' => $davinci->getTotalChatsPastMonth(),
            'transactions_current_month' => $payment->getTotalTransactionsCurrentMonth(),
            'transactions_past_month' => $payment->getTotalTransactionsPastMonth(),
            'gift_current_month' => $payment->getGiftCurrentMonth(),
            'gift_past_month' => $payment->getGiftPastMonth(),
            'gift_usage_current_month' => $payment->getGiftUsageCurrentMonth(),
            'gift_usage_past_month' => $payment->getGiftUsagePastMonth(),
            'input_tokens_current_month' => $davinci->inputTokensCurrentMonth(),
            'input_tokens_past_month' => $davinci->inputTokensPastMonth(),
            'output_tokens_current_month' => $davinci->outputTokensCurrentMonth(),
            'output_tokens_past_month' => $davinci->outputTokensPastMonth(),
            'support_tickets_current_month' => $support->ticketsCurrentMonth(),
            'support_tickets_past_month' => $support->ticketsPastMonth(),
        ];

        # Total Data Yearly
        $total_data_yearly = [
            'words_generated' => $davinci->getTotalWordsCurrentYear(),
            'images_generated' => $davinci->getTotalImagesCurrentYear(),
            'contents_generated' => $davinci->getTotalContentsCurrentYear(),
            'chats_generated' => $davinci->getTotalChatsCurrentYear(),
            'transactions_generated' => $payment->getTotalTransactionsCurrentYear(),
            'input_tokens_generated' => $davinci->inputTokensCurrentYear(),
            'output_tokens_generated' => $davinci->outputTokensCurrentYear(),
            'support_tickets_generated' => $support->ticketsCurrentYear(),
        ];
        
        $chart_data['monthly_earnings'] = json_encode($payment->getPayments());
        $chart_data['monthly_spendings'] = json_encode($cost->getSpendings());
        $chart_data['source_data'] = json_encode($payment->getSourceRevenue());
        $chart_data['cost_data'] = json_encode($cost->getCosts());
        $chart_data['total_registration_yearly'] = json_encode($registration_yearly->getFreeRegistrations());
        $chart_data['paid_registration_yearly'] = json_encode($registration_yearly->getPaidRegistrations());
        $chart_data['user_countries'] = json_encode($this->getAllCountries());
        $chart_data['monthly_input_tokens'] = json_encode($davinci->getMonthlyInputTokensChart());
        $chart_data['monthly_output_tokens'] = json_encode($davinci->getMonthlyOutputTokensChart());

        $chart_data['gpt3_words'] = $davinci->gpt3TurboWords();
        $chart_data['gpt3_tasks'] = $davinci->gpt3TurboTasks();
        $chart_data['gpt4_words'] = $davinci->gpt4Words();
        $chart_data['gpt4_tasks'] = $davinci->gpt4Tasks();
        $chart_data['gpt4o_words'] = $davinci->gpt4oWords();
        $chart_data['gpt4o_tasks'] = $davinci->gpt4oTasks();
        $chart_data['gpt4t_words'] = $davinci->gpt4TurboWords();
        $chart_data['gpt4t_tasks'] = $davinci->gpt4TurboTasks();
        $chart_data['opus_words'] = $davinci->opusWords();
        $chart_data['opus_tasks'] = $davinci->opusTasks();
        $chart_data['sonnet_words'] = $davinci->sonnetWords();
        $chart_data['sonnet_tasks'] = $davinci->sonnetTasks();
        $chart_data['haiku_words'] = $davinci->haikuWords();
        $chart_data['haiku_tasks'] = $davinci->haikuTasks();
        $chart_data['gemini_words'] = $davinci->geminiWords();
        $chart_data['gemini_tasks'] = $davinci->geminiTasks();
        
        $percentage['income_current'] = json_encode($payment->getTotalPaymentsCurrentMonth());
        $percentage['income_past'] = json_encode($payment->getTotalPaymentsPastMonth());
        $percentage['spending_current'] = json_encode($cost->getTotalSpendingCurrentMonth());
        $percentage['spending_past'] = json_encode($cost->getTotalSpendingPastMonth());

        $percentage['input_tokens_current'] = json_encode($davinci->inputTokensCurrentMonth());
        $percentage['input_tokens_past'] = json_encode($davinci->inputTokensPastMonth());
        $percentage['output_tokens_current'] = json_encode($davinci->outputTokensCurrentMonth());
        $percentage['output_tokens_past'] = json_encode($davinci->outputTokensPastMonth());

        $percentage['support_tickets_current'] = json_encode($support->ticketsCurrentMonth());
        $percentage['support_tickets_past'] = json_encode($support->ticketsPastMonth());

        $percentage['subscribers_current'] = json_encode($registration->getNewSubscribersCurrentMonth());
        $percentage['subscribers_past'] = json_encode($registration->getNewSubscribersPastMonth());
        $percentage['images_current'] = json_encode($davinci->getTotalImagesCurrentMonth());
        $percentage['images_past'] = json_encode($davinci->getTotalImagesCurrentMonth());
        $percentage['contents_current'] = json_encode($davinci->getTotalContentsCurrentMonth());
        $percentage['contents_past'] = json_encode($davinci->getTotalContentsPastMonth());
        $percentage['chats_current'] = json_encode($davinci->getTotalChatsCurrentMonth());
        $percentage['chats_past'] = json_encode($davinci->getTotalChatsPastMonth());
        $percentage['transactions_current'] = json_encode($payment->getTotalTransactionsCurrentMonth());
        $percentage['transactions_past'] = json_encode($payment->getTotalTransactionsPastMonth());
        $percentage['gift_current'] = json_encode($payment->getGiftCurrentMonth());
        $percentage['gift_past'] = json_encode($payment->getGiftPastMonth());
        $percentage['gift_funds_current'] = json_encode($payment->getGiftUsageCurrentMonth());
        $percentage['gift_funds_past'] = json_encode($payment->getGiftUsagePastMonth());

        $notifications = Auth::user()->notifications->where('type', '<>', 'App\Notifications\GeneralNotification')->all();
        $tickets = SupportTicket::whereNot('status', 'Resolved')->whereNot('status', 'Closed')->latest()->paginate(8);

        $users = User::latest()->take(10)->get();
        $transaction = Payment::latest()->take(10)->get();  
        $approvals = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('payments.status', 'pending')
            ->select('users.name', 'users.email', 'payments.plan_name', 'payments.frequency', 'payments.price', 'payments.gateway', 'payments.status', 'payments.id')
            ->orderBy('payments.created_at', 'desc')
            ->get();

        $cachedUsers = json_decode(Cache::get('isOnline', []), true);
        $users_online = count($cachedUsers);

        $users_today = User::whereNotNull('last_seen')->whereDate('last_seen', Carbon::today())->count();
 
        return view('admin.dashboard.index', compact('today', 'users_online', 'users_today', 'approvals', 'total', 'total_monthly', 'total_data_monthly', 'total_data_yearly', 'chart_data', 'percentage', 'users', 'transaction', 'notifications', 'tickets'));
    }


    /**
     * Display GA4 info
     *
     * @return \Illuminate\Http\Response
     */
    public function analytics(Request $request)
    {
        if ($request->ajax()) {

            $google = new GoogleAnalyticsService();

            if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials'))) {
                $data['traffic_label'] = json_encode($google->getTrafficLabels());
                $data['traffic_data'] = json_encode($google->getTrafficData());
                $data['google_average_session'] = $google->averageSessionDuration();
                $data['google_sessions'] = number_format($google->sessions());
                $data['google_session_views'] = number_format((float)$google->sessionViews(), 2);
                $data['google_bounce_rate'] = $google->bounceRate();
                $data['google_users'] = json_encode($google->users());
                $data['google_user_sessions'] = json_encode($google->userSessions());

                $data['google_countries'] = $this->getGACountries();
                $data['status'] = 200;
                return $data;
            }    
        }
    }


    /**
     * Show list of all countries
     */
    public function getAllCountries()
    {        
        $google = new GoogleAnalyticsService();
        $countries = collect();
    
        try {
            // Check if the credentials file exists and is a file (not a directory)
            $credentialsPath = storage_path('app/analytics/'. env('GOOGLE_SERVICE_ACCOUNT_CREDENTIALS'));
            
            if (!env('GOOGLE_SERVICE_ACCOUNT_CREDENTIALS') || !file_exists($credentialsPath) || is_dir($credentialsPath)) {
                // Return empty collection if credentials are invalid
                return $countries;
            }
            
            $gcountries = $google->userCountries();
            $data = $google->userCountriesTotal();
        
            foreach ($gcountries as $country) {
                $countries->put($country['country'], $country['totalUsers']);
            }
        } catch (\Exception $e) {
            // Log the error but don't crash
            \Log::error('Google Analytics error: ' . $e->getMessage());
        }
        return $countries;        
    }


    /**
     * Show list of all countries
     */
    public function getGACountries()
    {        
        $google = new GoogleAnalyticsService();

        $countries= $google->userCountries();
        $total = $google->userCountriesTotal();
        $list = '';

        foreach ($countries as $data) {

            $flag = theme_url('img/flags/'.strtolower($data['countryId']).'.svg');
            $width = ($data['totalUsers']/$total)*100;
            $value = ($data['totalUsers']/$total)*100;
            $list .= '<li>
                        <div class="card-body pt-2 pb-2 pl-0 pr-0 d-flex">
                            <div class="dashboard-flags overflow-hidden"><img alt="User Avatar" class="rounded-circle" src="'.$flag.'"></div>
                                <div class="template-title mt-auto mb-auto d-flex justify-content-center">
                                <h6 class="fs-12 font-weight-semibold text-muted mb-0 ml-4 mt-auto mb-auto">' . __($data['country']) . '</h6>																										
                            </div>	
                            <div class="progress mt-auto mb-auto ml-4 text-right" style="height: 5px; width: 150px">
                                <div class="progress-bar" role="progressbar" style="width: ' . $width . '%;" aria-valuenow="'.$value.'" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="template-title mt-auto mb-auto justify-content-center">
                                <h6 class="fs-10 text-muted mb-0 ml-4 mt-auto mb-auto">'. $data['totalUsers'] . '</h6>																										
                            </div>						
                        </div>
                    </li>';
        }
            											
        return $list;        
    }


    public function checkUpdate(Request $request)
    {
        if ($request->ajax()) {
            $api = new LicenseController();

            $current_version = $api->get_current_version();
            $latest_version = $api->get_latest_version();
            
            if ($current_version == $latest_version['latest_version']) {                
                $data['status'] = 400;
                return $data;
            } else {
                $data['status'] = 200;
                $data['version'] = $latest_version['latest_version'];
                return $data;
            }
        }
    }

}
