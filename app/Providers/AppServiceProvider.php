<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Employee;
use App\Policies\PosPolicy;
use App\Policies\StaffPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(125);

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->hasPermission($ability)) {
                return true;
            }
            // Let it fall through to specific policies if a specific ability like 'update' on a model is checked
        });

        Gate::policy(Order::class, PosPolicy::class);
        Gate::policy(MenuItem::class, PosPolicy::class);
        // Staff records use Employee as the model, so Laravel cannot discover
        // StaffPolicy by convention. Register it explicitly for viewAny/create/update.
        Gate::policy(Employee::class, StaffPolicy::class);
    }
}
