<?php

namespace Modules\Cocktail\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot() {
    
    }
    
    /**
     * Define the routes for the application.
     *
     * @param Router $router
     * @return void
     */
    public function map(Router $router) {
        // if (!app()->routesAreCached()) {
        //    require __DIR__ . '/Http/routes.php';
        // }
        
    }
}
