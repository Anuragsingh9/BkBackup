<?php

namespace Modules\Crm\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Crm\Http\Middleware\AccessDashboard;
use Modules\Crm\Http\Middleware\AssistanceResource;
use Modules\Crm\Http\Middleware\AssistanceDelete;
use Modules\Crm\Http\Middleware\AssistanceEdit;
use Modules\Crm\Http\Middleware\AssistanceTabView;
use Modules\Crm\Http\Middleware\BulkModify;
use Modules\Crm\Http\Middleware\FileResource;
use Modules\Crm\Http\Middleware\FileTabView;
use Modules\Crm\Http\Middleware\FilterAdd;
use Modules\Crm\Http\Middleware\FilterDelete;
use Modules\Crm\Http\Middleware\FilterEdit;
use Modules\Crm\Http\Middleware\NotesResource;
use Modules\Crm\Http\Middleware\TranscribeCheck;

class CrmServiceProvider extends ServiceProvider
{
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
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        app()->make('router')->aliasMiddleware('TranscribeCheck', TranscribeCheck::class);
        app()->make('router')->aliasMiddleware('AccessDashboard', AccessDashboard::class);
        app()->make('router')->aliasMiddleware('AssistanceResource', AssistanceResource::class);
        app()->make('router')->aliasMiddleware('AssistanceTabView', AssistanceTabView::class);
        app()->make('router')->aliasMiddleware('BulkModify', BulkModify::class);
        app()->make('router')->aliasMiddleware('FileResource', FileResource::class);
        app()->make('router')->aliasMiddleware('FileTabView', FileTabView::class);
        app()->make('router')->aliasMiddleware('FilterEdit', FilterEdit::class);
        app()->make('router')->aliasMiddleware('FilterDelete', FilterDelete::class);
        app()->make('router')->aliasMiddleware('FilterAdd', FilterAdd::class);
        app()->make('router')->aliasMiddleware('NotesResource', NotesResource::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('crm.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'crm'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/crm');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/crm';
        }, \Config::get('view.paths')), [$sourcePath]), 'crm');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/crm');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'crm');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'crm');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
