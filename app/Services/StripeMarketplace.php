<?php

namespace App\Services;

use App\Http\Controllers\Admin\ExtensionController;
use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeMarketplace 
{
    protected $baseURI;
    protected $sak;

    private $extensions;

    public function __construct()
    {
        $this->extensions = new ExtensionController();
        $this->sak = $this->extensions->sak();
    }


    /**
     * Process stripe payment
     *
     * @return \Illuminate\Http\Response
     */
    public function processStripe() 
    {
        if (session()->has('type')) {
            $slug = session()->get('name');
            $type = session()->get('type'); 
            $amount = session()->get('amount'); 
            $extension_name = session()->get('extension_name'); 
        }

        if ($type == 'extension') {
            $name = "Payment for: " . ucfirst($extension_name) . ' ' . ucfirst($type);
        } elseif($type == 'theme') {
            $name = "Payment for: " . ucfirst($slug) . ' ' . ucfirst($type);
        } else {
            $name = "Payment for: " . ucfirst($extension_name);
        }
        
        $total = $amount * 100;        

        Stripe::setApiKey($this->sak);

       try {
            if ($type == 'theme') {
                $session = \Stripe\Checkout\Session::create([
                    'customer_email' => auth()->user()->email,
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'USD',
                                'product_data' => [
                                    'name' => $name,
                                ],
                                'unit_amount' => $total,
                            ],
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'payment',
                    'success_url' => route('admin.payments.theme.approved'),
                    'cancel_url' => route('admin.payments.stripe.theme.cancel'),
                ]);
            } elseif ($type == 'support') {
                $session = \Stripe\Checkout\Session::create([
                    'customer_email' => auth()->user()->email,
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'USD',
                                'product_data' => [
                                    'name' => $name,
                                ],
                                'unit_amount' => $total,
                                'recurring' => [
                                    'interval' => 'month',
                                ]
                            ],
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'subscription', 
                    'success_url' => route('admin.payments.market.approved'),
                    'cancel_url' => route('admin.payments.stripe.market.cancel'),
                ]);
            } else {
                $session = \Stripe\Checkout\Session::create([
                    'customer_email' => auth()->user()->email,
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'USD',
                                'product_data' => [
                                    'name' => $name,
                                ],
                                'unit_amount' => $total,
                            ],
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'payment',
                    'success_url' => route('admin.payments.market.approved'),
                    'cancel_url' => route('admin.payments.stripe.market.cancel'),
                ]);
            }

            if (!is_null($session->payment_intent)) {
                session()->put('paymentIntentID', $session->payment_intent);
            } else {
                session()->put('paymentIntentID', $session->id);
            }

        } catch (\Exception $e) {
            toastr()->error(__('Stripe authentication error, verify your stripe settings first ' . $e->getMessage()));
            return redirect()->back();
        } 

        return response()->json(['id' => $session->id, 'status' => 200]);
    }


    /**
     * Process stripe pament cancelation
     *
     * @return \Illuminate\Http\Response
    */
    public function processThemeCancel() 
    {
        toastr()->warning(__('Stripe payment has been cancelled'));
        return redirect()->route('admin.themes');
    }


     /**
     * Process stripe pament cancelation
     *
     * @return \Illuminate\Http\Response
    */
    public function processMarketCancel() 
    {
        toastr()->warning(__('Stripe payment has been cancelled'));
        return redirect()->route('admin.extensions');
    }


    public function handleThemeApproval(Request $request)
    {
        $paymentIntentID = session()->get('paymentIntentID');
        $slug = session()->get('name');

        $theme = $this->extensions->verify($slug, $paymentIntentID);

        if ($theme) {
            if ($slug == 'premier' || $slug == 'support') {
                session()->forget('paymentIntentID');
                session()->forget('name');
                toastr()->success(__('Payment Successfully Processed'));
                return view('admin.marketplace.success-package', compact('theme'));
            } else {
                session()->forget('paymentIntentID');
                session()->forget('name');
                toastr()->success(__('Payment Successfully Processed'));
                return view('admin.themes.success-theme', compact('theme'));
            }
           
        } else {
            toastr()->warning(__('Please contact support team, there was an issue with your payment'));
            return redirect()->route('admin.themes');
        }
        
    }


    public function handleMarketApproval(Request $request)
    {

        $paymentIntentID = session()->get('paymentIntentID');
        $slug = session()->get('name');

        $theme = $this->extensions->verify($slug, $paymentIntentID);
   
        if ($theme) {
            if ($slug == 'premier' || $slug == 'support') {
                session()->forget('paymentIntentID');
                session()->forget('name');
                toastr()->success(__('Payment Successfully Processed'));
                return view('admin.marketplace.success-package', compact('theme'));
            } else {
                session()->forget('paymentIntentID');
                session()->forget('name');
                toastr()->success(__('Payment Successfully Processed'));
                return view('admin.marketplace.success-theme', compact('theme'));
            }
            
        } else {
            toastr()->warning(__('Please contact support team, there was an issue with your payment'));
            return redirect()->route('admin.extensions');
        }
    }


}