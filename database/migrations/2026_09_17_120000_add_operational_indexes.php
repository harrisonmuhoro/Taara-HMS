<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['branch_id', 'status', 'check_in_date'], 'reservations_arrivals_index');
            $table->index(['branch_id', 'status', 'check_out_date'], 'reservations_departures_index');
        });

        Schema::table('stays', function (Blueprint $table) {
            $table->index(['branch_id', 'status'], 'stays_branch_status_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at'], 'notifications_user_read_created_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['branch_id', 'created_at'], 'audit_logs_branch_created_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['branch_id', 'expense_date', 'status'], 'expenses_branch_date_status_index');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index(['branch_id', 'purchase_date', 'status'], 'purchases_branch_date_status_index');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['branch_id', 'product_id', 'created_at'], 'stock_movements_branch_product_created_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['branch_id', 'status'], 'products_branch_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropIndex('products_branch_status_index'));
        Schema::table('stock_movements', fn (Blueprint $table) => $table->dropIndex('stock_movements_branch_product_created_index'));
        Schema::table('purchases', fn (Blueprint $table) => $table->dropIndex('purchases_branch_date_status_index'));
        Schema::table('expenses', fn (Blueprint $table) => $table->dropIndex('expenses_branch_date_status_index'));
        Schema::table('audit_logs', fn (Blueprint $table) => $table->dropIndex('audit_logs_branch_created_index'));
        Schema::table('notifications', fn (Blueprint $table) => $table->dropIndex('notifications_user_read_created_index'));
        Schema::table('stays', fn (Blueprint $table) => $table->dropIndex('stays_branch_status_index'));
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_arrivals_index');
            $table->dropIndex('reservations_departures_index');
        });
    }
};
