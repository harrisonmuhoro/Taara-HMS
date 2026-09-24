<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\GuestsController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Terms and Conditions
Route::view('/terms', 'terms')->name('terms');

// ─── Authenticated Routes ────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Hotel configuration
    Route::get('/configuration', [\App\Http\Controllers\ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('/configuration/branches', [\App\Http\Controllers\ConfigurationController::class, 'storeBranch'])->name('configuration.branches.store');
    Route::post('/configuration/departments', [\App\Http\Controllers\ConfigurationController::class, 'storeDepartment'])->name('configuration.departments.store');
    Route::post('/configuration/floors', [\App\Http\Controllers\ConfigurationController::class, 'storeFloor'])->name('configuration.floors.store');
    Route::post('/configuration/room-types', [\App\Http\Controllers\ConfigurationController::class, 'storeRoomType'])->name('configuration.room-types.store');
    Route::post('/configuration/amenities', [\App\Http\Controllers\ConfigurationController::class, 'storeAmenity'])->name('configuration.amenities.store');
    Route::post('/configuration/booking-sources', [\App\Http\Controllers\ConfigurationController::class, 'storeBookingSource'])->name('configuration.booking-sources.store');

    // Notifications
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // Audit logs
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])
        ->name('audit-logs.index');
    Route::get('/audit-logs/export', [\App\Http\Controllers\AuditLogController::class, 'export'])
        ->name('audit-logs.export');

    // ── Reservations ──────────────────────────────────────────────────────────
    Route::get('reservations/calendar', [ReservationsController::class, 'calendar'])->name('reservations.calendar');
    Route::resource('reservations', ReservationsController::class);
    Route::post('reservations/{reservation}/confirm', [ReservationsController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/cancel',  [ReservationsController::class, 'cancel'])->name('reservations.cancel');

    // ── Front Desk ────────────────────────────────────────────────────────────
    Route::get('front-desk/check-in', [\App\Http\Controllers\FrontDeskController::class, 'checkIn'])->name('front-desk.check-in');
    Route::post('front-desk/check-in/{reservation}', [\App\Http\Controllers\FrontDeskController::class, 'processCheckIn'])->name('front-desk.check-in.process');
    Route::get('front-desk/check-out', [\App\Http\Controllers\FrontDeskController::class, 'checkOut'])->name('front-desk.check-out');
    Route::post('front-desk/check-out/{stay}', [\App\Http\Controllers\FrontDeskController::class, 'processCheckOut'])->name('front-desk.check-out.process');

    // ── Guests ────────────────────────────────────────────────────────────────
    Route::resource('guests', GuestsController::class);
    Route::post('guests/{guest}/documents', [GuestsController::class, 'uploadDocument'])->name('guests.documents.store');
    Route::get('guests/{guest}/documents/{document}', [GuestsController::class, 'downloadDocument'])->name('guests.documents.download');

    // ── Rooms ─────────────────────────────────────────────────────────────────
    Route::resource('rooms', RoomsController::class)->only(['index', 'show']);

    // ── Housekeeping ──────────────────────────────────────────────────────────
    Route::get('housekeeping', [HousekeepingController::class, 'index'])->name('housekeeping.index');
    Route::patch('housekeeping/rooms/{room}/status', [HousekeepingController::class, 'updateStatus'])->name('housekeeping.status.update');

    // ── Finance ───────────────────────────────────────────────────────────────
    Route::get('finance/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices');
    Route::get('finance/invoices/{invoice}', [FinanceController::class, 'showInvoice'])->name('finance.invoices.show');
    Route::get('finance/payments', [FinanceController::class, 'payments'])->name('finance.payments');
    
    // Expenses
    Route::get('finance/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('finance.expenses.index');
    Route::get('finance/expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('finance.expenses.create');
    Route::post('finance/expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('finance.expenses.store');
    Route::post('finance/expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('finance.expenses.approve');
    Route::get('finance/expenses/{expense}/attachment', [\App\Http\Controllers\ExpenseController::class, 'downloadAttachment'])->name('finance.expenses.attachment');
    
    // Refunds
    Route::get('finance/refunds', [\App\Http\Controllers\RefundController::class, 'index'])->name('finance.refunds.index');
    Route::get('finance/refunds/create', [\App\Http\Controllers\RefundController::class, 'create'])->name('finance.refunds.create');
    Route::post('finance/refunds', [\App\Http\Controllers\RefundController::class, 'store'])->name('finance.refunds.store');

    // ── Maintenance ───────────────────────────────────────────────────────────
    Route::resource('maintenance', \App\Http\Controllers\MaintenanceController::class)
        ->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('maintenance/{maintenance}/comments', [\App\Http\Controllers\MaintenanceController::class, 'addComment'])
        ->name('maintenance.comments.store');

    // ── Inventory & Purchasing ────────────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Products
        Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');

        // Suppliers
        Route::get('suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create', [\App\Http\Controllers\SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');

        // Purchases
        Route::get('purchases', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [\App\Http\Controllers\PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [\App\Http\Controllers\PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}', [\App\Http\Controllers\PurchaseController::class, 'show'])->name('purchases.show');
        Route::put('purchases/{purchase}/status', [\App\Http\Controllers\PurchaseController::class, 'updateStatus'])->name('purchases.updateStatus');

        // Stock Adjustments
        Route::get('adjustments', [\App\Http\Controllers\StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('adjustments/create', [\App\Http\Controllers\StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('adjustments', [\App\Http\Controllers\StockAdjustmentController::class, 'store'])->name('adjustments.store');
    });

    // ── Staff & Roles ─────────────────────────────────────────────────────────
    Route::resource('staff', \App\Http\Controllers\StaffController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);
    
    Route::resource('roles', \App\Http\Controllers\RoleController::class)
        ->only(['index', 'store']);
    Route::get('roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])->name('roles.permissions');
    Route::put('roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // ── Admin Override ────────────────────────────────────────────────────────
    Route::post('admin/unlock-user', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email', 'ip' => 'nullable|ip']);
        $email = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($request->input('email')));
        \Illuminate\Support\Facades\RateLimiter::clear($email.'|account');
        if ($request->filled('ip')) {
            \Illuminate\Support\Facades\RateLimiter::clear($request->input('ip').'|ip');
        }
        return back()->with('status', 'User/IP login restrictions unlocked');
    })->name('admin.unlock-user');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('revenue');
        Route::get('/revenue/export', [\App\Http\Controllers\ReportController::class, 'exportRevenue'])->name('revenue.export');
        Route::get('/revenue/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportRevenuePdf'])->name('revenue.export.pdf');
        Route::get('/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/export', [\App\Http\Controllers\ReportController::class, 'exportInventory'])->name('inventory.export');
        Route::get('/inventory/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportInventoryPdf'])->name('inventory.export.pdf');
        Route::get('/export/queue/{type}', [\App\Http\Controllers\ReportController::class, 'queueExport'])->name('export.queue');
        Route::get('/export/download/{filename}', [\App\Http\Controllers\ReportController::class, 'downloadQueuedExport'])->name('export.download');
    });

    // ── Restaurant / POS ──────────────────────────────────────────────────────
    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos');
        Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/orders', [\App\Http\Controllers\PosController::class, 'orders'])->name('orders');
        
        Route::resource('menu', \App\Http\Controllers\MenuController::class)->except(['show', 'destroy']);
    });

});

Route::get('_boost/browser-logs', function () {
    return response()->json([
        'status' => 'active',
        'message' => 'Laravel Boost browser logger endpoint. Accepts POST payloads from browser console.',
    ]);
});

require __DIR__ . '/auth.php';
