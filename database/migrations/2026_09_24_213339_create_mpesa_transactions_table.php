<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // e.g., 'STK_PUSH', 'C2B'
            $table->string('transaction_id', 100)->nullable()->unique(); // M-Pesa receipt number
            $table->string('merchant_request_id')->nullable(); // For STK Push
            $table->string('checkout_request_id')->nullable(); // For STK Push
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->text('result_desc')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
