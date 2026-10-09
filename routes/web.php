<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FrontDeskController;
use App\Http\Controllers\GuestsController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Reservations\BookingDepositController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Terms and Conditions
Route::view('/terms', 'terms')->name('terms');

// ─── Authenticated Routes ────────────────────────────────────────────────────
Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureUserIsActive::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Hotel configuration
    Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('/configuration/branches', [ConfigurationController::class, 'storeBranch'])->name('configuration.branches.store');
    Route::post('/configuration/departments', [ConfigurationController::class, 'storeDepartment'])->name('configuration.departments.store');
    Route::post('/configuration/floors', [ConfigurationController::class, 'storeFloor'])->name('configuration.floors.store');
    Route::post('/configuration/room-types', [ConfigurationController::class, 'storeRoomType'])->name('configuration.room-types.store');
    Route::post('/configuration/amenities', [ConfigurationController::class, 'storeAmenity'])->name('configuration.amenities.store');
    Route::post('/configuration/booking-sources', [ConfigurationController::class, 'storeBookingSource'])->name('configuration.booking-sources.store');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])
        ->name('audit-logs.export');

    // ── Reservations ──────────────────────────────────────────────────────────
    Route::get('reservations/calendar', [ReservationsController::class, 'calendar'])->name('reservations.calendar');
    Route::resource('reservations', ReservationsController::class);
    Route::post('reservations/{reservation}/confirm', [ReservationsController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/cancel', [ReservationsController::class, 'cancel'])->name('reservations.cancel');
    Route::post('reservations/{reservation}/deposit/initiate', [BookingDepositController::class, 'initiate'])->name('reservations.deposit.initiate');
    Route::post('reservations/{reservation}/deposit/extend', [BookingDepositController::class, 'extend'])->name('reservations.deposit.extend');
    Route::post('reservations/{reservation}/deposit/waive', [BookingDepositController::class, 'waive'])->name('reservations.deposit.waive');

    // ── Front Desk ────────────────────────────────────────────────────────────
    Route::get('front-desk/check-in', [FrontDeskController::class, 'checkIn'])->name('front-desk.check-in');
    Route::post('front-desk/check-in/{reservation}', [FrontDeskController::class, 'processCheckIn'])->name('front-desk.check-in.process');
    Route::get('front-desk/check-out', [FrontDeskController::class, 'checkOut'])->name('front-desk.check-out');
    Route::post('front-desk/check-out/{stay}', [FrontDeskController::class, 'processCheckOut'])->name('front-desk.check-out.process');

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
    Route::get('finance/expenses', [ExpenseController::class, 'index'])->name('finance.expenses.index');
    Route::get('finance/expenses/create', [ExpenseController::class, 'create'])->name('finance.expenses.create');
    Route::post('finance/expenses', [ExpenseController::class, 'store'])->name('finance.expenses.store');
    Route::post('finance/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('finance.expenses.approve');
    Route::get('finance/expenses/{expense}/attachment', [ExpenseController::class, 'downloadAttachment'])->name('finance.expenses.attachment');

    // Refunds
    Route::get('finance/refunds', [RefundController::class, 'index'])->name('finance.refunds.index');
    Route::get('finance/refunds/create', [RefundController::class, 'create'])->name('finance.refunds.create');
    Route::post('finance/refunds', [RefundController::class, 'store'])->name('finance.refunds.store');

    // ── Maintenance ───────────────────────────────────────────────────────────
    Route::resource('maintenance', MaintenanceController::class)
        ->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('maintenance/{maintenance}/comments', [MaintenanceController::class, 'addComment'])
        ->name('maintenance.comments.store');

    // ── Inventory & Purchasing ────────────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Products
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');

        // Suppliers
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

        // Purchases
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::put('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('purchases.updateStatus');

        // Stock Adjustments
        Route::get('adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    });

    // ── Staff & Roles ─────────────────────────────────────────────────────────
    Route::resource('staff', StaffController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('roles', RoleController::class)
        ->only(['index', 'store']);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // ── Admin Override ────────────────────────────────────────────────────────
    Route::post('admin/unlock-user', function (Request $request) {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('users.update'), 403);
        $request->validate(['email' => 'required|email', 'ip' => 'nullable|ip']);
        $email = Str::transliterate(Str::lower($request->input('email')));
        RateLimiter::clear($email.'|account');
        if ($request->filled('ip')) {
            RateLimiter::clear($request->input('ip').'|ip');
        }

        return back()->with('status', 'User/IP login restrictions unlocked');
    })->name('admin.unlock-user');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/revenue/export', [ReportController::class, 'exportRevenue'])->name('revenue.export');
        Route::get('/revenue/export/pdf', [ReportController::class, 'exportRevenuePdf'])->name('revenue.export.pdf');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/export', [ReportController::class, 'exportInventory'])->name('inventory.export');
        Route::get('/inventory/export/pdf', [ReportController::class, 'exportInventoryPdf'])->name('inventory.export.pdf');
        Route::get('/export/queue/{type}', [ReportController::class, 'queueExport'])->name('export.queue');
        Route::get('/export/download/{filename}', [ReportController::class, 'downloadQueuedExport'])->name('export.download');
    });

    // ── Restaurant / POS ──────────────────────────────────────────────────────
    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/orders', [PosController::class, 'orders'])->name('orders');

        Route::resource('menu', MenuController::class)->except(['show', 'destroy']);
    });

});

Route::get('_boost/browser-logs', function () {
    return response()->json([
        'status' => 'active',
        'message' => 'Laravel Boost browser logger endpoint. Accepts POST payloads from browser console.',
    ]);
});

require __DIR__.'/auth.php';
