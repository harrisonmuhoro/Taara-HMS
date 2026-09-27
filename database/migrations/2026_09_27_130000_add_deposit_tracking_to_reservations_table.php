<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('deposit_paid')->default(false)->after('deposit_amount');
            $table->timestamp('deposit_paid_at')->nullable()->after('deposit_paid');
            $table->string('deposit_receipt_no')->nullable()->after('deposit_paid_at');
            $table->timestamp('deposit_expires_at')->nullable()->after('deposit_receipt_no');
        });

        // Existing deposit_amount values were recorded as paid deposits before
        // deposit_amount became the system-calculated required deposit.
        DB::table('reservations')
            ->where('deposit_amount', '>', 0)
            ->update(['deposit_paid' => true]);

        DB::table('branches')->orderBy('id')->each(function (object $branch): void {
            DB::table('settings')->updateOrInsert(
                ['branch_id' => $branch->id, 'setting_key' => 'deposit_rate'],
                [
                    'setting_value' => '40',
                    'setting_type' => 'string',
                    'is_public' => false,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        });
    }

    public function down(): void
    {
        DB::table('settings')->where('setting_key', 'deposit_rate')->delete();

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_paid',
                'deposit_paid_at',
                'deposit_receipt_no',
                'deposit_expires_at',
            ]);
        });
    }
};
