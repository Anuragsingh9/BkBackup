<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\UserManagement\Repositories\factory\DummyUserRepository;
use Modules\UserManagement\Repositories\factory\EntityRepository;
use Modules\UserManagement\Repositories\factory\UserRepository;
use Modules\UserManagement\Repositories\IDummyUserRepository;
use Modules\UserManagement\Repositories\IEntityRepository;
use Modules\UserManagement\Repositories\IUserRepository;
use Modules\UserManagement\Services\BusinessServices\factory\AwsFileService;
use Modules\UserManagement\Services\BusinessServices\factory\EmailService;
use Modules\UserManagement\Services\BusinessServices\factory\KctService;
use Modules\UserManagement\Services\BusinessServices\factory\TenantService;
use Modules\UserManagement\Services\BusinessServices\factory\UserService;
use Modules\UserManagement\Services\BusinessServices\IEmailService;
use Modules\UserManagement\Services\BusinessServices\IFileService;
use Modules\UserManagement\Services\BusinessServices\IKctService;
use Modules\UserManagement\Services\BusinessServices\ITenantService;
use Modules\UserManagement\Services\BusinessServices\IUserService;
use Modules\UserManagement\Services\OtherModuleCommunication\factory\KctAdminService;
use Modules\UserManagement\Services\OtherModuleCommunication\factory\SuperAdminService;
use Modules\UserManagement\Services\OtherModuleCommunication\IKctAdminService;
use Modules\UserManagement\Services\OtherModuleCommunication\ISuperAdminService;

class UserManagementServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'UserManagement';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'usermanagement';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);

        // Services
        $this->app->singleton(IFileService::class, AwsFileService::class);
        $this->app->singleton(ITenantService::class, TenantService::class);
        $this->app->singleton(IEmailService::class, EmailService::class);
        $this->app->singleton(IKctService::class, KctService::class);
        $this->app->singleton(IUserService::class, UserService::class);

        // Repositories
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->singleton(IDummyUserRepository::class, DummyUserRepository::class);
        $this->app->singleton(IEntityRepository::class, EntityRepository::class);

        // Communication With Other Modules
        $this->app->singleton(IKctAdminService::class, KctAdminService::class);
        $this->app->singleton(ISuperAdminService::class,SuperAdminService::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/auth.php'), "$this->moduleNameLower.auth"
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/constants.php'), "$this->moduleNameLower.constants"
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/modelConstants.php'), "$this->moduleNameLower.modelConstants"
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    private function getPublishableViewPaths(): array {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
