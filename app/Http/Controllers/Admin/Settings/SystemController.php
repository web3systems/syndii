<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Spatie\Sitemap\SitemapGenerator;


class SystemController extends Controller
{   
    /**
     * Dispaly activation index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.system.index');
    }


    /**
     * Start cache clearing process
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cache(Request $request)
    {		
        if ($request->ajax()) {  
            try {
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');

            } catch (\Exception $e) {
                Log::info('Cache was not cleared correctly: ' . $e->getMessage());
                $data['status'] = 500;                 
                return $data; 
            }
            
            $data['status'] = 200;                 
            return $data;      
        } 
    }


    /**
     * Generate sitemap.xml file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sitemap(Request $request)
    {		
        if ($request->ajax()) {  
            try {
                $domain = config('app.url');
                SitemapGenerator::create($domain)->getSitemap()->writeToDisk('public', 'sitemap.xml', true);

            } catch (\Exception $e) {
                Log::info('Sitemap error: ' . $e->getMessage());
                $data['status'] = 500;                 
                $data['message'] = $e->getMessage();                 
                return $data; 
            }
            
            $data['status'] = 200;                 
            return $data;      
        } 

 
    }

}
