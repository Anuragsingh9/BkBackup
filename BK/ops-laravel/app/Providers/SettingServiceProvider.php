<?php
    
    namespace App\Providers;
    
    use Illuminate\Support\ServiceProvider;
    use App, Auth;
    
    class SettingServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap the application services.
         *
         * @return void
         */
        public function boot()
        {
            
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            if (!empty($hostname)) {
                $setting = \App\AccountSettings::where('account_id', $hostname->id)->first(['id', 'setting', 'project_enable']);

                $this->setConfigVar($setting, 'ICONTACT_ACCOUNT_ID', 'ICONTACT_ACCOUNT_ID');
                $this->setConfigVar($setting, 'ICONTACT_API_APP_ID', 'ICONTACT_API_APP_ID');
                $this->setConfigVar($setting, 'ICONTACT_API_USERNAME', 'ICONTACT_API_USERNAME');
                $this->setConfigVar($setting, 'ICONTACT_API_PASSWORD', 'ICONTACT_API_PASSWORD');
                $this->setConfigVar($setting, 'ICONTACT_CLIENT_FOLDER_ID', 'ICONTACT_CLIENT_FOLDER_ID');
                $this->setConfigVar($setting, 'NEWSLETTER', 'news_letter_enable', 0);
                $this->setConfigVar($setting, 'QUALIFICATION', 'qualification_enable', 0);
                $this->setConfigVar($setting, 'CRM', 'crm_enable', 0);
                $this->setConfigVar($setting, 'PROJECT', 'project_enable', 0);
                $this->setConfigVar($setting, 'Press', 'press_enable', 0);
                $this->setConfigVar($setting, 'Instance', 'instance_enable', 0);
                $this->setConfigVar($setting, 'Reinvent', 'reinvent_enable', 0);
                if (isset($hostname->fqdn)) {
                    $fqdn = explode('.', $hostname->fqdn);
                } else {
                    $fqdn[0] = '';
                }
                
                config()->set('accountName', $fqdn[0]);
            }
            
        }
        
        /**
         * Register the application services.
         *
         * @return void
         */
        public function register()
        {
            //
        }
        
        protected function setConfigVar($setting, $field, $_field, $elseVal = 1)
        {
            if ($elseVal == 1)
                $val = env($_field);
            else
                $val = $elseVal;
            config()->set('constants.' . $field, isset($setting->setting[$_field]) ? $setting->setting[$_field] : $val);
        }
    }
