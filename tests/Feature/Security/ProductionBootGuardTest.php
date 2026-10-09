<?php

namespace Tests\Feature\Security;

use App\Providers\AppServiceProvider;
use Tests\TestCase;

class ProductionBootGuardTest extends TestCase
{
    public function test_boot_guard_throws_exception_in_production_when_config_is_unsafe(): void
    {
        // Mock production environment
        $this->app->detectEnvironment(fn () => 'production');

        // Set unsafe configurations
        config([
            'app.debug' => true,
            'app.url' => 'http://insecure-domain.test',
            'session.secure' => false,
            'session.same_site' => null,
            'services.mpesa.webhook_ips' => [],
            'app.trusted_proxies' => [],
            'services.mpesa.env' => 'sandbox',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsafe production configuration');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();
    }

    public function test_boot_guard_passes_in_production_when_all_config_is_safe(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        config([
            'app.debug' => false,
            'app.url' => 'https://secure-domain.test',
            'session.secure' => true,
            'session.same_site' => 'lax',
            'services.mpesa.webhook_ips' => ['196.201.214.200'],
            'app.trusted_proxies' => ['10.0.0.1'],
            'services.mpesa.env' => 'live',
        ]);
        putenv('BACKUP_ARCHIVE_PASSWORD=SuperSecretBackupKey123!');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertTrue(true);
    }
}
