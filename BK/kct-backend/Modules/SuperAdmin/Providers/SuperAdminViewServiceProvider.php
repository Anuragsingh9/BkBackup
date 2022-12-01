<?php

namespace Modules\SuperAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

class SuperAdminViewServiceProvider extends ServiceProvider {

    use ServicesAndRepo;

    public function __construct($app) {
        parent::__construct($app);

    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        view()->composer('superadmin::components.auth_header', function ($view) {
            $view->mainLogo = $this->getMainLogo();
            return $view;
        });
        view()->composer('superadmin::components.header', function ($view) {
            $view->mainLogo = $this->getMainLogo();
            return $view;
        });
        view()->composer('superadmin::auth.signup.signup_step_1', function ($view) {
            $view->mainLogo = $this->getMainLogo();
            return $view;
        });
        view()->composer('superadmin::index', function ($view) {
            $view->mainLogo = $this->getMainLogo();
            return $view;
        });
    }

    private function getMainLogo() {
        $path = substr(config('superadmin.default_logo'), 1);
        return $this->suServices()->fileService->getFileUrl($path);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }
}
