<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PrepaidPlan;
use App\Models\SubscriptionPlan;
use App\Models\PaymentGateway;
use App\Services\HelperService;


class WalletService 
{

    public function handlePaymentSubscription(Request $request, SubscriptionPlan $id)
    {
       
    }


    public function handlePaymentPrePaid(Request $request, $id, $type)
    {   
       
    }

}