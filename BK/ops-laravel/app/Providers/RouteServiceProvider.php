<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
        protected $namespaceInventory = 'Modules\Resilience\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        // kct api map
        $this->mapEventsRoutes();
        $this->mapKctV1Routes();
        $this->mapKctV2Routes();
        $this->mapKctGenericRoutes();

        //
            // map new custom routes
            $this->mapCustomRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
        
        /**
         * Define the "custom" routes for the application.
         *
         * These routes are typically stateless.
         *
         * @return void
         */
        protected function mapCustomRoutes()
        {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespaceInventory)
                ->group(base_path('/Modules/Resilience/Http/api.php'));
        }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To map the KCT Version 1 API Routes
     * If user wants to explicitly hit the version 1 api
     * then this will provide the service to use the KCT with Version 1
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function mapEventsRoutes() {
        Route::prefix("api/v2")
            ->middleware('api')
            ->group(base_path('Modules/Events/Http/api_v2.php'));
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To map the KCT Version 1 API Routes
     * If user wants to explicitly hit the version 1 api
     * then this will provide the service to use the KCT with Version 1
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function mapKctV1Routes() {
        Route::prefix("api/v1")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/V1/api_v1_admin.php'));
        Route::prefix("api/v1")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/V1/api_v1_user.php'));
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To map the KCT Version 1 API Routes
     * If user wants to explicitly hit the version 1 api
     * then this will provide the service to use the KCT with Version 1
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function mapKctV2Routes() {
        Route::prefix("api/v2")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/V2/api_v2_admin.php'));
        Route::prefix("api/v2")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/V2/api_v2_user.php'));
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To map the KCT latest version api
     * This will auto detect the api with suitable conditions
     * and use the latest version for the each api.
     *
     * This will provide the loosely coupled with api versioning
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function mapKctGenericRoutes() {
        $latestVersion = config("constants.api_version.cocktail");
        Route::prefix("api/v$latestVersion")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/api_generic_admin.php'));
        Route::prefix("api/v$latestVersion")
            ->middleware('api')
            ->group(base_path('Modules/Cocktail/Http/routes/api_generic_user.php'));
    }
}
