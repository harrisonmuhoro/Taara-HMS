<?php

namespace Tests\Feature\Security;

use App\Models\MpesaTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_mpesa_webhooks_fail_closed_without_an_allowlist(): void
    {
        config(['services.mpesa.webhook_ips' => []]);

        $this->postJson('/api/mpesa/callback', [])->assertForbidden();
    }

    public function test_application_uses_csp_and_does_not_emit_deprecated_xss_header(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Content-Security-Policy');
        $this->assertFalse($response->headers->has('X-XSS-Protection'));
    }

    public function test_malformed_stk_callbacks_are_rejected_after_ip_authentication(): void
    {
        config(['services.mpesa.webhook_ips' => ['127.0.0.1']]);

        $this->postJson('/api/mpesa/callback', [])->assertBadRequest();
    }

    public function test_duplicate_c2b_callbacks_do_not_create_duplicate_transactions(): void
    {
        config(['services.mpesa.webhook_ips' => ['127.0.0.1']]);
        $payload = [
            'TransID' => 'TEST-RECEIPT-001',
            'TransAmount' => '100.00',
            'MSISDN' => '254700000001',
            'BillRefNumber' => 'UNMATCHED-TEST',
        ];

        $this->postJson('/api/c2b/confirm', $payload)->assertOk();
        $this->postJson('/api/c2b/confirm', $payload)->assertOk();

        $this->assertSame(1, MpesaTransaction::where('transaction_id', 'TEST-RECEIPT-001')->count());
    }

    public function test_stk_initiation_requires_an_authenticated_staff_session(): void
    {
        $this->postJson('/api/mpesa/stkpush/initiate', [
            'phone' => '0712345678',
            'amount' => '100.00',
            'reservation_id' => 1,
        ])->assertUnauthorized();
    }
}
