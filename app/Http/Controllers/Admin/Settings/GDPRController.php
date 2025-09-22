<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\CookieSetting;


class GDPRController extends Controller
{   
    /**
     * Dispaly activation index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cookies = CookieSetting::first();

        return view('admin.settings.gdpr.index', compact('cookies'));
    }


    public function store(Request $request)
    {		
        $this->storeCheckbox(request('enable_cookies'), 'enable_cookies');
        $this->storeCheckbox(request('enable_dark_mode'), 'enable_dark_mode');
        $this->storeCheckbox(request('disable_page_interaction'), 'disable_page_interaction');
        $this->storeCheckbox(request('hide_from_bots'), 'hide_from_bots');

        $this->storeValues(request('consent_modal_layouts'), 'consent_modal_layouts');
        $this->storeValues(request('consent_modal_position'), 'consent_modal_position');
        $this->storeValues(request('preferences_modal_layout'), 'preferences_modal_layout');
        $this->storeValues(request('preferences_modal_position'), 'preferences_modal_position');
        $this->storeValues(request('days'), 'days');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back(); 

    }


    private function storeCheckbox($checkbox, $field_name)
    {
        if ($checkbox == 'on') {
            $status = true; 
        } else {
            $status = false;
        }

        $settings = CookieSetting::first();
        $settings->update([
            $field_name => $status
        ]);
    }


    private function storeValues($value, $field_name)
    {
        $settings = CookieSetting::first();
        $settings->update([
            $field_name => $value
        ]);
    }

}
