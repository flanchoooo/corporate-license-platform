<?php

namespace App\Providers;

use App\Models\Quote;
use App\Models\Vehicle;
use App\Policies\QuotePolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(Quote::class, QuotePolicy::class);

        Gate::define('manage-platform', fn ($user) => $user->isSuperAdmin());
    }
}
