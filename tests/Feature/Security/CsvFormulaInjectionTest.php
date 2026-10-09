<?php

namespace Tests\Feature\Security;

use App\Models\Branch;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Invoice;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvFormulaInjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_safe_neutralizes_formula_triggers(): void
    {
        $this->assertSame("'=HYPERLINK(\"http://evil.test\")", Csv::safe('=HYPERLINK("http://evil.test")'));
        $this->assertSame("'+12345", Csv::safe('+12345'));
        $this->assertSame("'-calc", Csv::safe('-calc'));
        $this->assertSame("'@SUM(A1:B2)", Csv::safe('@SUM(A1:B2)'));
        $this->assertSame("'\ttab_trigger", Csv::safe("\ttab_trigger"));
        $this->assertSame("'\rreturn_trigger", Csv::safe("\rreturn_trigger"));

        // Benign text should remain untouched
        $this->assertSame('Normal Guest Name', Csv::safe('Normal Guest Name'));
        $this->assertSame('', Csv::safe(''));
    }

    public function test_revenue_csv_export_neutralizes_malicious_guest_names(): void
    {
        $hotel = Hotel::create(['name' => 'Test Hotel']);
        $branch = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Branch', 'code' => 'BR', 'status' => 'active']);

        $role = Role::firstOrCreate(['name' => 'Manager'], ['description' => 'Manager']);
        $perm = Permission::firstOrCreate(['name' => 'reports.view'], ['module' => 'reports', 'description' => 'View reports']);
        $role->permissions()->attach($perm);

        $user = User::factory()->create([
            'status' => 'active',
            'branch_id' => $branch->id,
        ]);
        $user->roles()->attach($role);

        $maliciousName = '=HYPERLINK("http://evil.test","x")';
        $guest = Guest::create([
            'branch_id' => $branch->id,
            'guest_number' => 'GST-EVIL',
            'first_name' => $maliciousName,
            'last_name' => 'Hacker',
        ]);

        Invoice::create([
            'branch_id' => $branch->id,
            'guest_id' => $guest->id,
            'invoice_number' => 'INV-001',
            'issued_at' => now(),
            'due_at' => now()->addDays(1),
            'grand_total' => 100,
            'subtotal' => 100,
            'paid_amount' => 100,
            'balance_due' => 0,
            'status' => 'PAID',
        ]);

        $response = $this->actingAs($user)->get('/reports/revenue/export?start_date=' . now()->subDay()->toDateString() . '&end_date=' . now()->addDay()->toDateString());

        $response->assertOk();
        $content = $response->streamedContent();

        // Must contain the formula neutralized with a single quote prefix
        $this->assertStringContainsString("'" . $maliciousName, $content);
        // Must NOT contain unquoted raw formula
        $this->assertStringNotContainsString('"' . $maliciousName . ' Hacker"', $content);
    }
}
