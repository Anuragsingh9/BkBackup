<?php
    
    
    namespace App\Services;
    use App,Auth;
    
    class AppService
    {
        public static function setUserLocale()
        {
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            if (!empty($hostname)) {
                $setting = \App\AccountSettings::where('account_id', $hostname->id)->first(['id', 'setting', 'project_enable']);
               
                if (isset(Auth::user()->setting)) {
                    $json = json_decode(Auth::user()->setting);
                    $lang = isset($json->lang) ? strtolower($json->lang) : 'fr';
                    session()->put('lang', strtoupper($lang));
                } elseif (session()->has('lang')) {
                    $lang = strtolower(session()->get('lang'));
                } elseif (isset($setting->setting['lang'])) {
                    $lang = strtolower($setting->setting['lang']);
                    session()->put('lang', strtoupper($setting->setting['lang']));
                } else {
                    $lang = 'fr';
                    session()->put('lang', strtoupper($lang));
                }
            }else{
                if (isset(Auth::user()->setting)) {
                    $json = json_decode(Auth::user()->setting);
                    $lang = isset($json->lang) ? strtolower($json->lang) : 'fr';
                    session()->put('lang', strtoupper($lang));
                }else {
                    $lang = 'fr';
                    session()->put('lang', strtoupper($lang));
                }
            }
            App::setLocale($lang);
        
        }
    }