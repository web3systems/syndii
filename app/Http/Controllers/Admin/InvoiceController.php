<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceSetting;

class InvoiceController extends Controller
{
    /**
     * Display invoice settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = InvoiceSetting::first();

        return view('admin.finance.invoice.finance_invoice_index', compact('invoice'));
    }


    /**
     * Store invoice details in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'company' => 'required',
        ]);

        $invoice = InvoiceSetting::first();

        if ($invoice) {
            $invoice->update([
                'company' => request('company'),
                'website' => request('website'),
                'address' => request('address'),
                'city' => request('city'),
                'state' => request('state'),
                'postal_code' => request('postal_code'),
                'country' => request('country'),
                'phone' => request('phone'),
                'vat_number' => request('vat_number'),
            ]);
        } else {
            InvoiceSetting::create([
                'company' => request('company'),
                'website' => request('website'),
                'address' => request('address'),
                'city' => request('city'),
                'state' => request('state'),
                'postal_code' => request('postal_code'),
                'country' => request('country'),
                'phone' => request('phone'),
                'vat_number' => request('vat_number'),
            ]);

        }

        toastr()->success(__('Invoice settings successfully updated'));
        return redirect()->back();
    }
}