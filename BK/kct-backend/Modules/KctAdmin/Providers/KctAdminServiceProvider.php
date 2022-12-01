<?php

namespace Modules\KctAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

use Modules\KctAdmin\Console\RecurEvents;
use Modules\KctAdmin\Console\SyncLogsV4;
use Modules\KctAdmin\Http\Middleware\CanUserAccessGroup;
use Modules\KctAdmin\Http\Middleware\CheckUserLoginCount;
use Modules\KctAdmin\Repositories\factory\GroupRepository;
use Modules\KctAdmin\Repositories\factory\GroupUserRepository;
use Modules\KctAdmin\Repositories\factory\LabelRepository;
use Modules\KctAdmin\Repositories\factory\MomentRepository;
use Modules\KctAdmin\Repositories\IEventRepository;
use Modules\KctAdmin\Repositories\factory\EventRepository;
use Modules\KctAdmin\Repositories\IGroupRepository;
use Modules\KctAdmin\Repositories\IGroupUserRepository;
use Modules\KctAdmin\Repositories\IKctSpaceRepository;
use Modules\KctAdmin\Repositories\factory\KctSpaceRepository;
use Modules\KctAdmin\Repositories\factory\OrganiserTagsRepository;
use Modules\KctAdmin\Repositories\ILabelRepository;
use Modules\KctAdmin\Repositories\IMomentRepository;
use Modules\KctAdmin\Repositories\IOrganiserTagsRepository;
use Modules\KctAdmin\Repositories\ISettingRepository;
use Modules\KctAdmin\Repositories\factory\SettingRepository;
use Modules\KctAdmin\Services\BaseService;
use Modules\KctAdmin\Services\BusinessServices\factory\AnalyticsService;
use Modules\KctAdmin\Services\BusinessServices\factory\AwsFileService;
use Modules\KctAdmin\Services\BusinessServices\factory\EmailService;
use Modules\KctAdmin\Services\BusinessServices\factory\EventService;
use Modules\KctAdmin\Services\BusinessServices\factory\GroupService;
use Modules\KctAdmin\Services\BusinessServices\factory\CoreService;
use Modules\KctAdmin\Services\BusinessServices\factory\LeagueColorExtService;
use Modules\KctAdmin\Services\BusinessServices\factory\SpaceService;
use Modules\KctAdmin\Services\BusinessServices\factory\ValidationService;
use Modules\KctAdmin\Services\BusinessServices\factory\ZoomService;
use Modules\KctAdmin\Services\BusinessServices\IAnalyticsService;
use Modules\KctAdmin\Services\BusinessServices\IColorExtractService;
use Modules\KctAdmin\Services\BusinessServices\IEmailService;
use Modules\KctAdmin\Services\BusinessServices\IEventService;
use Modules\KctAdmin\Services\BusinessServices\IFileService;
use Modules\KctAdmin\Services\BusinessServices\IGroupService;
use Modules\KctAdmin\Services\BusinessServices\ICoreService;
use Modules\KctAdmin\Services\BusinessServices\ISpaceService;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Services\BusinessServices\IZoomService;
use Modules\KctAdmin\Services\DataServices\factory\DataService;
use Modules\KctAdmin\Services\DataServices\IDataService;
use Modules\KctAdmin\Services\OtherModuleCommunication\factory\SuperAdminService;
use Modules\KctAdmin\Services\OtherModuleCommunication\factory\UserManagementService;
use Modules\KctAdmin\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\KctAdmin\Services\OtherModuleCommunication\IUserManagementService;
use Modules\KctUser\Console\FilterOfflineUser;
use Modules\UserManagement\Repositories\factory\UserRepository;
use Modules\UserManagement\Repositories\IUserRepository;

class KctAdminServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'KctAdmin';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'kctadmin';

    protected $kctCoreService;

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

        // repository bindings
        $this->app->singleton(IEventRepository::class, EventRepository::class);
        $this->app->singleton(IKctSpaceRepository::class, KctSpaceRepository::class);
        $this->app->singleton(ISettingRepository::class, SettingRepository::class);
        $this->app->singleton(IOrganiserTagsRepository::class, OrganiserTagsRepository::class);
        $this->app->singleton(IUserRepository::class,UserRepository::class);
        $this->app->singleton(IGroupRepository::class, GroupRepository::class);
        $this->app->singleton(IUserManagementService::class,UserManagementService::class);
        $this->app->singleton(ISuperAdminService::class,SuperAdminService::class);
        $this->app->singleton(IMomentRepository::class,MomentRepository::class);
        $this->app->singleton(ILabelRepository::class,LabelRepository::class);
        $this->app->singleton(IFileService::class, AwsFileService::class);
        $this->app->singleton(ICoreService::class, CoreService::class);
        $this->app->singleton(ISpaceService::class, SpaceService::class);
        $this->app->singleton(IValidationService::class, ValidationService::class);
        $this->app->singleton(IGroupService::class, GroupService::class);
        $this->app->singleton(IZoomService::class, ZoomService::class);
        $this->app->singleton(IGroupUserRepository::class,GroupUserRepository::class);
        $this->app->singleton(IAnalyticsService::class,AnalyticsService::class);
        $this->app->singleton(IEventService::class,EventService::class);

        $this->app->singleton(IDataService::class, DataService::class);
        $this->app->singleton(IColorExtractService::class,LeagueColorExtService::class);

        $this->app->singleton(BaseService::class);

        $this->app->make('router')->aliasMiddleware('checkUserLoginCount', CheckUserLoginCount::class);
        $this->app->make('router')->aliasMiddleware('canUserAccessGroup', CanUserAccessGroup::class);


        $this->app->singleton(IEmailService::class,EmailService::class);

        $this->commands([
            RecurEvents::class,
            FilterOfflineUser::class,
            SyncLogsV4::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);


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
