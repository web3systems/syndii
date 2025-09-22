<?php

namespace App\Traits;

use Konekt\PdfInvoice\InvoicePrinter;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\PrepaidPlan;
use App\Models\User;
use App\Models\MainSetting;
use App\Models\InvoiceSetting;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Support\Facades\Log;
use \NumberFormatter;
use Exception;

trait InvoiceGeneratorTrait
{
    /**
     * Handle and create invoice after each payment or for past payments
     * 
     */
    public function generateInvoice($order_id)
    {   
        $id = Payment::where('order_id', $order_id)->firstOrFail();

        try {

            $invoice = InvoiceSetting::first();

            $user = User::where('id', $id->user_id)->firstOrFail();
            $plan = ($id->frequency == 'prepaid') ? 
                PrepaidPlan::where('id', $id->plan_id)->first() : 
                SubscriptionPlan::where('id', $id->plan_id)->first();

            // Create Seller
            $seller = new Party([
                'name'          => $invoice->company,                
                'address'       => $invoice->address,
                'phone'         => $invoice->phone,
                'code'          => $invoice->postal_code,
                'vat'          => $invoice->vat_number,
                'custom_fields' => [
                    __('invoices::invoice.website') => $invoice->website,
                ],
            ]);


            # Customer Data
            $customerData = [];
            if (!empty($user->name)) {$customerData['name'] = $user->name;}
            if (!empty($id->billing_address) || !empty($id->billing_city) || !empty($id->billing_country)) {$customerData['address'] = $id->billing_address . ', ' . $id->billing_city . ', ' . $id->billing_postal_code . ', ' . $id->billing_country;}
            if (!empty($user->email)) {$customerData['custom_fields'][__('invoices::invoice.email')] = $user->email;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.gateway')] = $id->gateway;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.transaction')] = $id->order_id;}
            if (!empty($id->billing_vat_number)) {$customerData['custom_fields'][__('invoices::invoice.vat')] = $id->billing_vat_number;}

            $customer = new Party($customerData);


            // Calculate Tax
            $tax_rate = config('payment.payment_tax');
            $tax_amount = ($tax_rate > 0) ? ($plan->price * $tax_rate) / 100 : 0;
            $total = $tax_amount + $plan->price;

            // Create Invoice Item
            $item = (new InvoiceItem())
                ->title('Plan Name: ' . $plan->plan_name)
                ->pricePerUnit($plan->price)
                ->quantity(1);

            $notes = __('All subscription cancellations will be processed by the next month');

            foreach(config('currencies.all') as $key => $value) {     
                if (strtolower($key) == strtolower($id->currency)) {
                    $currency_symbol =  html_entity_decode($value['symbol'], ENT_QUOTES, 'UTF-8'); 
                } 
            }
           

            // Create Invoice
            if (is_null($id->billing_vat_number)) {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->taxRate($tax_rate)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            } else {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            }

            // Save and download PDF
            return $invoice->stream();
        } catch (Exception $e) {
            Log::error('Invoice Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            toastr()->error(__('Failed to generate invoice. Please try again later'));
            return redirect()->back();
        }
    }  


    public function showInvoice($order_id)
    {   
        $id = Payment::where('order_id', $order_id)->first();

        try {

            $invoice = InvoiceSetting::first();

            $user = User::where('id', $id->user_id)->firstOrFail();
            $plan = ($id->frequency == 'prepaid') ? 
                PrepaidPlan::where('id', $id->plan_id)->first() : 
                SubscriptionPlan::where('id', $id->plan_id)->first();

            // Create Seller
            $seller = new Party([
                'name'          => $invoice->company,                
                'address'       => $invoice->address,
                'phone'         => $invoice->phone,
                'code'          => $invoice->postal_code,
                'vat'          => $invoice->vat_number,
                'custom_fields' => [
                    __('invoices::invoice.website') => $invoice->website,
                ],
            ]);


            # Customer Data
            $customerData = [];
            if (!empty($user->name)) {$customerData['name'] = $user->name;}
            if (!empty($id->billing_address) || !empty($id->billing_city) || !empty($id->billing_country)) {$customerData['address'] = $id->billing_address . ', ' . $id->billing_city . ', ' . $id->billing_postal_code . ', ' . $id->billing_country;}
            if (!empty($user->email)) {$customerData['custom_fields'][__('invoices::invoice.email')] = $user->email;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.gateway')] = $id->gateway;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.transaction')] = $id->order_id;}
            if (!empty($id->billing_vat_number)) {$customerData['custom_fields'][__('invoices::invoice.vat')] = $id->billing_vat_number;}

            $customer = new Party($customerData);


            // Calculate Tax
            $tax_rate = config('payment.payment_tax');
            $tax_amount = ($tax_rate > 0) ? ($plan->price * $tax_rate) / 100 : 0;
            $total = $tax_amount + $plan->price;

            // Create Invoice Item
            $item = (new InvoiceItem())
                ->title('Plan Name: ' . $plan->plan_name)
                ->pricePerUnit($plan->price)
                ->quantity(1);

            $notes = __('All subscription cancellations will be processed by the next month');

            foreach(config('currencies.all') as $key => $value) {     
                if (strtolower($key) == strtolower($id->currency)) {
                    $currency_symbol =  html_entity_decode($value['symbol'], ENT_QUOTES, 'UTF-8'); 
                } 
            }
           

            // Create Invoice
            if (is_null($id->billing_vat_number)) {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->taxRate($tax_rate)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            } else {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            }

            // Save and download PDF
            return $invoice->stream();
        } catch (Exception $e) {
            Log::error('Invoice Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            toastr()->error(__('Failed to generate invoice. Please try again later'));
            return redirect()->back();
        }
        
    }


    public function bankTransferInvoice($order_id)
    {   
        $invoice_rows = ['bank_requisites'];
        $bank = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $invoice_rows)) {
                $bank[$row['name']] = $row['value'];
            }
        }

        $id = Payment::where('order_id', $order_id)->first();

        try {

            $invoice = InvoiceSetting::first();

            $user = User::where('id', $id->user_id)->firstOrFail();
            $plan = ($id->frequency == 'prepaid') ? 
                PrepaidPlan::where('id', $id->plan_id)->first() : 
                SubscriptionPlan::where('id', $id->plan_id)->first();

            // Create Seller
            $seller = new Party([
                'name'          => $invoice->company,                
                'address'       => $invoice->address,
                'phone'         => $invoice->phone,
                'code'          => $invoice->postal_code,
                'vat'          => $invoice->vat_number,
                'custom_fields' => [
                    __('invoices::invoice.website') => $invoice->website,
                    'Bank Requisites' => $bank['bank_requisites'],
                ],
            ]);


            # Customer Data
            $customerData = [];
            if (!empty($user->name)) {$customerData['name'] = $user->name;}
            if (!empty($id->billing_address) || !empty($id->billing_city) || !empty($id->billing_country)) {$customerData['address'] = $id->billing_address . ', ' . $id->billing_city . ', ' . $id->billing_postal_code . ', ' . $id->billing_country;}
            if (!empty($user->email)) {$customerData['custom_fields'][__('invoices::invoice.email')] = $user->email;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.gateway')] = $id->gateway;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.transaction')] = $id->order_id;}
            if (!empty($id->billing_vat_number)) {$customerData['custom_fields'][__('invoices::invoice.vat')] = $id->billing_vat_number;}

            $customer = new Party($customerData);


            // Calculate Tax
            $tax_rate = config('payment.payment_tax');
            $tax_amount = ($tax_rate > 0) ? ($plan->price * $tax_rate) / 100 : 0;
            $total = $tax_amount + $plan->price;

            // Create Invoice Item
            $item = (new InvoiceItem())
                ->title('Plan Name: ' . $plan->plan_name)
                ->pricePerUnit($plan->price)
                ->quantity(1);

            $notes = __('All subscription cancellations will be processed by the next month');

            foreach(config('currencies.all') as $key => $value) {     
                if (strtolower($key) == strtolower($id->currency)) {
                    $currency_symbol =  html_entity_decode($value['symbol'], ENT_QUOTES, 'UTF-8'); 
                } 
            }
           

            // Create Invoice
            if (is_null($id->billing_vat_number)) {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->taxRate($tax_rate)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            } else {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->filename($id->order_id)
                ->notes($notes);
            }

            // Save and download PDF
            return $invoice->stream();
        } catch (Exception $e) {
            Log::error('Invoice Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            toastr()->error(__('Failed to generate invoice. Please try again later'));
            return redirect()->back();
        }
    }


    public function invoiceAttachment($order_id)
    {   

        $id = Payment::where('order_id', $order_id)->first();

        try {

            $invoice = InvoiceSetting::first();

            $user = User::where('id', $id->user_id)->firstOrFail();
            $plan = ($id->frequency == 'prepaid') ? 
                PrepaidPlan::where('id', $id->plan_id)->first() : 
                SubscriptionPlan::where('id', $id->plan_id)->first();

            // Create Seller
            if ($id->gateway == 'BankTransfer') {
                $invoice_rows = ['bank_requisites'];
                $bank = [];
                $settings = Setting::all();

                foreach ($settings as $row) {
                    if (in_array($row['name'], $invoice_rows)) {
                        $bank[$row['name']] = $row['value'];
                    }
                }

                $seller = new Party([
                    'name'          => $invoice->company,                
                    'address'       => $invoice->address,
                    'phone'         => $invoice->phone,
                    'code'          => $invoice->postal_code,
                    'vat'          => $invoice->vat_number,
                    'custom_fields' => [
                        __('invoices::invoice.website') => $invoice->website,
                        'Bank Requisites' => $bank['bank_requisites'],
                    ],
                ]);
            } else {
                $seller = new Party([
                    'name'          => $invoice->company,                
                    'address'       => $invoice->address,
                    'phone'         => $invoice->phone,
                    'code'          => $invoice->postal_code,
                    'vat'          => $invoice->vat_number,
                    'custom_fields' => [
                        __('invoices::invoice.website') => $invoice->website,
                    ],
                ]);
            }
            


            # Customer Data
            $customerData = [];
            if (!empty($user->name)) {$customerData['name'] = $user->name;}
            if (!empty($id->billing_address) || !empty($id->billing_city) || !empty($id->billing_country)) {$customerData['address'] = $id->billing_address . ', ' . $id->billing_city . ', ' . $id->billing_postal_code . ', ' . $id->billing_country;}
            if (!empty($user->email)) {$customerData['custom_fields'][__('invoices::invoice.email')] = $user->email;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.gateway')] = $id->gateway;}
            if (!empty($id->gateway)) {$customerData['custom_fields'][__('invoices::invoice.transaction')] = $id->order_id;}
            if (!empty($id->billing_vat_number)) {$customerData['custom_fields'][__('invoices::invoice.vat')] = $id->billing_vat_number;}

            $customer = new Party($customerData);


            // Calculate Tax
            $tax_rate = config('payment.payment_tax');
            $tax_amount = ($tax_rate > 0) ? ($plan->price * $tax_rate) / 100 : 0;
            $total = $tax_amount + $plan->price;

            // Create Invoice Item
            $item = (new InvoiceItem())
                ->title('Plan Name: ' . $plan->plan_name)
                ->pricePerUnit($plan->price)
                ->quantity(1);

            $notes = __('All subscription cancellations will be processed by the next month');

            foreach(config('currencies.all') as $key => $value) {     
                if (strtolower($key) == strtolower($id->currency)) {
                    $currency_symbol =  html_entity_decode($value['symbol'], ENT_QUOTES, 'UTF-8'); 
                } 
            }
           

            // Create Invoice
            if (is_null($id->billing_vat_number)) {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->taxRate($tax_rate)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->notes($notes)
                ->filename("invoice-{$id->order_id}")
                ->save('audio');
            } else {
                $invoice = Invoice::make()
                ->series(config('invoices.serial_number.series'))
                ->sequence($id->id)
                ->status($id->status === 'completed' ? __('invoices::invoice.paid') : __('invoices::invoice.pending'))
                ->seller($seller)
                ->buyer($customer)
                ->date(now())
                ->dateFormat('M d, Y')
                ->payUntilDays(3)
                ->currencySymbol($currency_symbol)
                ->currencyCode($id->currency)
                ->addItem($item)
                ->logo(public_path(MainSetting::first()->logo_dashboard))
                ->template('default')
                ->notes($notes)
                ->filename("invoice-{$id->order_id}")
                ->save('audio');
            }

            // Save and download PDF
            return "invoice-{$id->order_id}";
        } catch (Exception $e) {
            Log::error('Invoice Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
 
        }
    }
}