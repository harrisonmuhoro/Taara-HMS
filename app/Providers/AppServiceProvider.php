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

use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(125);

        if (str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // F-07 – Production boot guard: refuse to start with an unsafe configuration
        if ($this->app->isProduction()) {
            $problems = [];

            if (config('app.debug'))
                $problems[] = 'APP_DEBUG must be false';
            if (! str_starts_with((string) config('app.url'), 'https://'))
                $problems[] = 'APP_URL must start with https://';
            if (! config('session.secure'))
                $problems[] = 'SESSION_SECURE_COOKIE must be true';
            if (config('session.same_site') === null)
                $problems[] = 'SESSION_SAME_SITE must be set';
            if (empty(config('services.mpesa.webhook_ips')))
                $problems[] = 'MPESA_WEBHOOK_IPS must not be empty';
            if (empty(config('app.trusted_proxies')))
                $problems[] = 'TRUSTED_PROXIES must be set';
            if (empty(env('BACKUP_ARCHIVE_PASSWORD')))
                $problems[] = 'BACKUP_ARCHIVE_PASSWORD must be set';
            if (config('services.mpesa.env') !== 'live')
                $problems[] = 'MPESA_ENV should be live in production';

            if ($problems) {
                throw new \RuntimeException(
                    "Unsafe production configuration:\n - " . implode("\n - ", $problems)
                );
            }
        }

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // F-01 / F-10 – STK push: 5 per minute per user AND 3 per 10 minutes per guest phone
        RateLimiter::for('mpesa-stk', function (Request $request) {
            $phone = Str::of((string) $request->input('phone'))
                ->replaceMatches('/\D/', '')
                ->toString();

            return [
                Limit::perMinute(5)->by('u:' . ($request->user()?->id ?? $request->ip())),
                Limit::perMinutes(10, 3)->by('p:' . $phone),
            ];
        });

        // F-10 – status query: 30 per minute per user
        RateLimiter::for('mpesa-status', fn (Request $request) =>
            Limit::perMinute(30)->by('u:' . ($request->user()?->id ?? $request->ip()))
        );

        // F-04 – password reset: 3 per minute per normalised email, 10 per minute per IP
        RateLimiter::for('password-reset', fn (Request $request) => [
            Limit::perMinute(3)->by('e:' . Str::lower((string) $request->input('email'))),
            Limit::perMinute(10)->by('ip:' . $request->ip()),
        ]);
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
