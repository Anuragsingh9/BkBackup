<?php

namespace Modules\Messenger\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Messenger\Http\Middleware\IsUserBelongToChannelMiddleware;
use Modules\Messenger\Http\Middleware\IsUserBelongToChannelOrWorkshop;
use Modules\Messenger\Http\Middleware\IsUserBelongToWorkshop;

class MessengerServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = FALSE;
    
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
        app()
            ->make('router')
            ->aliasMiddleware('IsUserBelongToChannelMiddleware', IsUserBelongToChannelMiddleware::class);
        app()
            ->make('router')
            ->aliasMiddleware('IsUserBelongToWorkshop', IsUserBelongToWorkshop::class);
        app()
            ->make('router')
            ->aliasMiddleware('IsUserBelongToChannelOrWorkshop', IsUserBelongToChannelOrWorkshop::class);
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
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('messenger.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'messenger'
        );
    }
    
    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = resource_path('views/modules/messenger');
        
        $sourcePath = __DIR__ . '/../Resources/views';
        
        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/messenger';
        }, \Config::get('view.paths')), [$sourcePath]), 'messenger');
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = resource_path('lang/modules/messenger');
        
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'messenger');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'messenger');
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
