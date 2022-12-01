<?php

namespace Modules\SuperAdmin\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\SuperAdmin\Console\SyncDummyUser;
use Modules\SuperAdmin\Http\Middleware\InSignUpProcessMiddleware;
use Modules\SuperAdmin\Http\Middleware\Localisation;
use Modules\SuperAdmin\Http\Middleware\OnlyNonLoginMiddleware;
use Modules\SuperAdmin\Http\Middleware\RootApiCorsMiddleware;
use Modules\SuperAdmin\Http\Middleware\SuAuth;
use Modules\SuperAdmin\Repositories\factory\AccountRepository;
use Modules\SuperAdmin\Repositories\factory\OrganisationRepository;
use Modules\SuperAdmin\Repositories\factory\SceneryRepository;
use Modules\SuperAdmin\Repositories\factory\SuOtpRepository;
use Modules\SuperAdmin\Repositories\factory\SuperAdminRepository;
use Modules\SuperAdmin\Repositories\factory\TagRepository;
use Modules\SuperAdmin\Repositories\factory\UserRepository;
use Modules\SuperAdmin\Repositories\IAccountRepository;
use Modules\SuperAdmin\Repositories\IOrganisationRepository;
use Modules\SuperAdmin\Repositories\ISceneryRepository;
use Modules\SuperAdmin\Repositories\ISuOtpRepository;
use Modules\SuperAdmin\Repositories\ISuperAdminRepository;
use Modules\SuperAdmin\Repositories\ITagRepository;
use Modules\SuperAdmin\Repositories\IUserRepository;
use Modules\SuperAdmin\Services\BusinessServices\factory\AccountService;
use Modules\SuperAdmin\Services\BusinessServices\factory\AwsFileService;
use Modules\SuperAdmin\Services\BusinessServices\factory\EmailService;
use Modules\SuperAdmin\Services\BusinessServices\factory\TenantService;
use Modules\SuperAdmin\Services\BusinessServices\IAccountService;
use Modules\SuperAdmin\Services\BusinessServices\IEmailService;
use Modules\SuperAdmin\Services\BusinessServices\IFileService;
use Modules\SuperAdmin\Services\BusinessServices\ITenantService;
use Modules\SuperAdmin\Services\DataServices\factory\ExcelExportService;
use Modules\SuperAdmin\Services\DataServices\factory\SessionDataService;
use Modules\SuperAdmin\Services\DataServices\IExportService;
use Modules\SuperAdmin\Services\DataServices\ITempDataService;
use Modules\SuperAdmin\Services\OtherModuleCommunication\factory\KctAdminService;
use Modules\SuperAdmin\Services\OtherModuleCommunication\factory\UserManagementService;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IKctAdminCommunication;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IUserManagement;

class SuperAdminServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'SuperAdmin';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'superadmin';

    /**
     * Boot the application events.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        app()->make('router')->aliasMiddleware('suAuth', SuAuth::class);
        app()->make('router')->aliasMiddleware('localisation', Localisation::class);
        app()->make('router')->aliasMiddleware('inSignupProcess', InSignUpProcessMiddleware::class);
        app()->make('router')->aliasMiddleware('onlyNonLogin', OnlyNonLoginMiddleware::class);
        app()->make('router')->aliasMiddleware('rootCors', RootApiCorsMiddleware::class);


        $this->commands([
            SyncDummyUser::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);

        // Service Bindings
        $this->app->singleton(IAccountService::class, AccountService::class);
        $this->app->singleton(ITenantService::class, TenantService::class);
        $this->app->singleton(ITempDataService::class, SessionDataService::class);
        $this->app->singleton(IExportService::class, ExcelExportService::class);
        $this->app->singleton(IEmailService::class, EmailService::class);
        $this->app->singleton(IFileService::class, AwsFileService::class);
        // Other module communication bindings
        $this->app->singleton(IKctAdminCommunication::class, KctAdminService::class);
        $this->app->singleton(IUserManagement::class, UserManagementService::class);

        // Repository Bindings
        $this->app->singleton(ISuOtpRepository::class, SuOtpRepository::class);
        $this->app->singleton(IAccountRepository::class, AccountRepository::class);
        $this->app->singleton(IOrganisationRepository::class, OrganisationRepository::class);
        $this->app->singleton(ITagRepository::class, TagRepository::class);
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->singleton(ISuperAdminRepository::class, SuperAdminRepository::class);
        $this->app->singleton(ISceneryRepository::class, SceneryRepository::class);

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
            module_path($this->moduleName, 'Config/constants.php'), 'superadmin.constants'
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/models.php'), 'superadmin.models'
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
