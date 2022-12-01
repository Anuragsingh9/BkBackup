<?php

namespace Modules\KctUser\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\KctUser\Http\Middleware\CheckUserBanMiddleware;
use Modules\KctUser\Http\Middleware\CheckUserLoginCount;
use Modules\KctUser\Http\Middleware\EventMemberCheck;
use Modules\KctUser\Http\Middleware\KctEnableCheck;
use Modules\KctUser\Http\Middleware\KctNodeCorsMiddleware;
use Modules\KctUser\Http\Middleware\SetKctLocaleMiddleware;
use Modules\KctUser\Http\Middleware\UserEmailVerifiedMiddleware;
use Modules\KctUser\Http\Middleware\UserVerified;
use Modules\KctUser\Http\Middleware\WebHookMiddleware;
use Modules\KctUser\Repositories\factory\BanUserRepository;
use Modules\KctUser\Repositories\factory\ConversationRepository;
use Modules\KctUser\Repositories\factory\ConversationUserRepository;
use Modules\KctUser\Repositories\factory\DummyConvRepository;
use Modules\KctUser\Repositories\factory\EventRepository;
use Modules\KctUser\Repositories\factory\OrgTagUserRepository;
use Modules\KctUser\Repositories\factory\SettingRepository;
use Modules\KctUser\Repositories\factory\SpaceUserRepository;
use Modules\KctUser\Repositories\factory\UserInvitesRepository;
use Modules\KctUser\Repositories\factory\UserRepository;
use Modules\KctUser\Repositories\factory\UserTagsRepository;
use Modules\KctUser\Repositories\IBanUserRepository;
use Modules\KctUser\Repositories\IConversationRepository;
use Modules\KctUser\Repositories\IConversationUserRepository;
use Modules\KctUser\Repositories\IDummyConvRepository;
use Modules\KctUser\Repositories\IEventRepository;
use Modules\KctUser\Repositories\IOrgTagUserRepository;
use Modules\KctUser\Repositories\ISettingRepository;
use Modules\KctUser\Repositories\ISpaceUserRepository;
use Modules\KctUser\Repositories\IUserInvitesRepository;
use Modules\KctUser\Repositories\IUserRepository;
use Modules\KctUser\Repositories\IUserTagsRepository;
use Modules\KctUser\Services\BusinessServices\factory\AwsFileService;
use Modules\KctUser\Services\BusinessServices\factory\CurlApiService;
use Modules\KctUser\Services\BusinessServices\factory\EventTimeService;
use Modules\KctUser\Services\BusinessServices\factory\KctService;
use Modules\KctUser\Services\BusinessServices\factory\KctUserAuthorizationService;
use Modules\KctUser\Services\BusinessServices\factory\KctUserEventService;
use Modules\KctUser\Services\BusinessServices\factory\KctUserService;
use Modules\KctUser\Services\BusinessServices\factory\KctUserSpaceService;
use Modules\KctUser\Services\BusinessServices\factory\KctUserValidationService;
use Modules\KctUser\Services\BusinessServices\factory\MailableIEmailFactory;
use Modules\KctUser\Services\BusinessServices\factory\OrganiserService;
use Modules\KctUser\Services\BusinessServices\factory\RedisService;
use Modules\KctUser\Services\BusinessServices\factory\VideoChatService;
use Modules\KctUser\Services\BusinessServices\IApiService;
use Modules\KctUser\Services\BusinessServices\IAuthorizationService;
use Modules\KctUser\Services\BusinessServices\IEmailService;
use Modules\KctUser\Services\BusinessServices\IEventTimeService;
use Modules\KctUser\Services\BusinessServices\IFileService;
use Modules\KctUser\Services\BusinessServices\IKctService;
use Modules\KctUser\Services\BusinessServices\IKctUserEventService;
use Modules\KctUser\Services\BusinessServices\IKctUserService;
use Modules\KctUser\Services\BusinessServices\IKctUserSpaceService;
use Modules\KctUser\Services\BusinessServices\IKctUserValidationService;
use Modules\KctUser\Services\BusinessServices\IOrganiserService;
use Modules\KctUser\Services\BusinessServices\IRtcService;
use Modules\KctUser\Services\BusinessServices\IVideoChatService;
use Modules\KctUser\Services\DataServices\factory\DataMapService;
use Modules\KctUser\Services\DataServices\factory\KctUserDataService;
use Modules\KctUser\Services\DataServices\IDataMapService;
use Modules\KctUser\Services\DataServices\IDataService;
use Modules\KctUser\Services\OtherModuleCommunication\factory\KctAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\factory\SuperAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\factory\UserManagementService;
use Modules\KctUser\Services\OtherModuleCommunication\IKctAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\IUserManagementService;

class KctUserServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'KctUser';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'kctuser';

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerDependencies();


        app()->make('router')->aliasMiddleware('userEmailVerified', UserVerified::class);
        app()->make('router')->aliasMiddleware('setKctLocaleMiddleware', setKctLocaleMiddleware::class);
        app()->make('router')->aliasMiddleware('checkModuleEnable', KctEnableCheck::class);
        app()->make('router')->aliasMiddleware('isEventMember', EventMemberCheck::class);
        app()->make('router')->aliasMiddleware('checkUserBanMiddleware', CheckUserBanMiddleware::class);
        app()->make('router')->aliasMiddleware('kct-node', KctNodeCorsMiddleware::class);
        app()->make('router')->aliasMiddleware('webHookMiddleware', WebHookMiddleware::class);
        app()->make('router')->aliasMiddleware('userVerified', UserEmailVerifiedMiddleware::class);

        $this->app->make('router')->aliasMiddleware('checkUserLoginCount', CheckUserLoginCount::class);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);

        // Repository model binding
        $this->app->singleton(IBanUserRepository::class, BanUserRepository::class);
        $this->app->singleton(IConversationRepository::class, ConversationRepository::class);
        $this->app->singleton(IConversationUserRepository::class, ConversationUserRepository::class);
        $this->app->singleton(IDummyConvRepository::class, DummyConvRepository::class);
        $this->app->singleton(IOrgTagUserRepository::class, OrgTagUserRepository::class);
        $this->app->singleton(ISpaceUserRepository::class, SpaceUserRepository::class);
        $this->app->singleton(IUserInvitesRepository::class, UserInvitesRepository::class);
        $this->app->singleton(IUserTagsRepository::class, UserTagsRepository::class);
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->singleton(IEventRepository::class, EventRepository::class);
        $this->app->singleton(ISettingRepository::class, SettingRepository::class);

        $this->app->singleton(IApiService::class, CurlApiService::class);
        $this->app->singleton(IAuthorizationService::class, KctUserAuthorizationService::class);
        $this->app->singleton(IEmailService::class, MailableIEmailFactory::class);
        $this->app->singleton(IEventTimeService::class, EventTimeService::class);
        $this->app->singleton(IKctService::class, KctService::class);
        $this->app->singleton(IKctUserEventService::class, KctUserEventService::class);
        $this->app->singleton(IKctUserService::class, KctUserService::class);
        $this->app->singleton(IKctUserSpaceService::class, KctUserSpaceService::class);
        $this->app->singleton(IKctUserValidationService::class, KctUserValidationService::class);
        $this->app->singleton(IOrganiserService::class, OrganiserService::class);
        $this->app->singleton(IRtcService::class, RedisService::class);
        $this->app->singleton(IVideoChatService::class, VideoChatService::class);
        $this->app->singleton(IFileService::class, AwsFileService::class);

        $this->app->singleton(IDataMapService::class, DataMapService::class);
        $this->app->singleton(IDataService::class, KctUserDataService::class);

        $this->app->singleton(IKctAdminService::class, KctAdminService::class);
        $this->app->singleton(ISuperAdminService::class, SuperAdminService::class);
        $this->app->singleton(IUserManagementService::class, UserManagementService::class);
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
            __DIR__ . '/../Config/constants.php', "$this->moduleNameLower.constants"
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/validations.php', "$this->moduleNameLower.validations"
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

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerDependencies() {
        $this->app->bind(ApiExecuteFactory::class, CurlExecuteFactory::class);
        $this->app->singleton(IEmailFactory::class, MailableIEmailFactory::class);
        $this->app->bind(RTCFactory::class, RedisFactory::class);
        $this->app->bind(IDataService::class, KctUserDataService::class);
        $this->app->bind(ExternalEventFactory::class, ZoomEventFactory::class);

    }

}
