<?php

namespace Modules\Cocktail\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Cocktail\Http\Middleware\CheckUserIsEventAdminMiddleware;
use Modules\Cocktail\Http\Middleware\EventMemberCheck;
use Modules\Cocktail\Http\Middleware\KctEnableCheck;
use Modules\Cocktail\Http\Middleware\KctNodeCorsMiddleware;
use Modules\Cocktail\Http\Middleware\KctS2Middleware;
use Modules\Cocktail\Http\Middleware\UserVerified;
use Modules\Cocktail\Services\Contracts\ApiExecuteFactory;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\Factory\BluejeansEventFactory;
use Modules\Cocktail\Services\Factory\CurlExecuteFactory;
use Modules\Cocktail\Services\Factory\MailableEmailFactory;
use Modules\Cocktail\Services\Factory\ZoomEventFactory;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class CocktailServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerDependencies();
        
        app()->make('router')->aliasMiddleware('userEmailVerified', UserVerified::class);
        app()->make('router')->aliasMiddleware('isEventMember', EventMemberCheck::class);
        app()->make('router')->aliasMiddleware('checkModuleEnable', KctEnableCheck::class);
        app()->make('router')->aliasMiddleware('eventAdmin', CheckUserIsEventAdminMiddleware::class);
        app()->make('router')->aliasMiddleware('kctS2Enable',KctS2Middleware::class);
        app()->make('router')->aliasMiddleware('kct-node',KctNodeCorsMiddleware::class);
    
    }
    
    public function register() {
    
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerDependencies() {
        $currentConference = KctCoreService::getInstance()->getCurrentConference();
        if($currentConference == 'zoom') {
            $this->app->bind(ExternalEventFactory::class, ZoomEventFactory::class);
        } else {
            $this->app->bind(ExternalEventFactory::class, BluejeansEventFactory::class);
        }
        $this->app->bind(ApiExecuteFactory::class, CurlExecuteFactory::class);
        $this->app->singleton(EmailFactory::class, MailableEmailFactory::class);
    }
    
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('cocktail.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'cocktail'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/kct_const.php', 'kct_const'
        );
    }
    
    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = resource_path('views/modules/cocktail');
        
        $sourcePath = __DIR__ . '/../Resources/views';
        
        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/cocktail';
        }, \Config::get('view.paths')), [$sourcePath]), 'cocktail');
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = resource_path('lang/modules/cocktail');
        
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'cocktail');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'cocktail');
        }
    }
    
    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories() {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
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
}
