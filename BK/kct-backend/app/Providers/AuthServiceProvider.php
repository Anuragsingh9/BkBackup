<?php

namespace App\Providers;

use App\Models\PassportAuthCode;
use App\Models\PassportClient;
use App\Models\PassportPersonalAccessClient;
use App\Models\PassportToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

        Passport::useClientModel(PassportClient::class);
        Passport::useTokenModel(PassportToken::class);
        Passport::useAuthCodeModel(PassportAuthCode::class);
        Passport::usePersonalAccessClientModel(PassportPersonalAccessClient::class);
    }
}
