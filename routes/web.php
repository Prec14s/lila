<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\OrderStatusController;
use App\Http\Controllers\Dapur\OrderController as DapurOrderController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\MenuController as OwnerMenuController;
use App\Http\Controllers\Owner\OrderController as OwnerOrderController;
use App\Http\Controllers\Owner\PaymentSettingController;
use App\Http\Controllers\Owner\SettingController;
use App\Http\Controllers\Owner\VerificationController;
use App\Http\Controllers\SuperAdmin\AccountController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\LogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Pelanggan (tanpa login) - diakses via scan barcode meja
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('menu.index'))->name('home');

Route::get('/pesan', [MenuController::class, 'index'])->name('menu.index');
Route::post('/pesan/checkout', [CustomerOrderController::class, 'store'])->name('order.store');
Route::get('/pesan/bayar/{orderNumber}', [CustomerOrderController::class, 'pay'])->name('order.pay');
Route::post('/pesan/bayar/{orderNumber}', [CustomerOrderController::class, 'uploadProof'])->name('order.upload-proof');
Route::get('/pesan/selesai/{orderNumber}', [CustomerOrderController::class, 'success'])->name('order.success');
Route::get('/pesan/struk/{orderNumber}', [CustomerOrderController::class, 'receipt'])->name('order.receipt');

Route::get('/status', [OrderStatusController::class, 'index'])->name('order.status');

/*
|--------------------------------------------------------------------------
| Autentikasi Staf (Login Terpadu)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Portal Owner
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('menus', OwnerMenuController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('payment-settings', [PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::post('payment-settings', [PaymentSettingController::class, 'store'])->name('payment-settings.store');
    Route::patch('payment-settings/{paymentSetting}/toggle', [PaymentSettingController::class, 'toggle'])->name('payment-settings.toggle');
    Route::delete('payment-settings/{paymentSetting}', [PaymentSettingController::class, 'destroy'])->name('payment-settings.destroy');

    Route::get('verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('verification/{order}/approve', [VerificationController::class, 'approve'])->name('verification.approve');
    Route::post('verification/{order}/reject', [VerificationController::class, 'reject'])->name('verification.reject');

    Route::get('orders', [OwnerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}/receipt', [OwnerOrderController::class, 'show'])->name('orders.receipt');
    Route::post('orders/{order}/forward-kitchen', [OwnerOrderController::class, 'forwardKitchen'])->name('orders.forward-kitchen');
    Route::delete('orders/{order}', [OwnerOrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('settings/whatsapp', [SettingController::class, 'edit'])->name('settings.whatsapp');
    Route::put('settings/whatsapp', [SettingController::class, 'update'])->name('settings.whatsapp.update');
});

/*
|--------------------------------------------------------------------------
| Portal Dapur
|--------------------------------------------------------------------------
*/
Route::prefix('dapur')->name('dapur.')->middleware(['auth', 'role:dapur'])->group(function () {
    Route::get('/dashboard', [DapurOrderController::class, 'index'])->name('dashboard');
    Route::post('orders/{order}/process', [DapurOrderController::class, 'process'])->name('orders.process');
    Route::post('orders/{order}/complete', [DapurOrderController::class, 'complete'])->name('orders.complete');
});

/*
|--------------------------------------------------------------------------
| Portal Super Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('superadmin.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::patch('accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('accounts.toggle');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::delete('logs/clear', [LogController::class, 'clear'])->name('logs.clear');
});
