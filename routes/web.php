<?php

use App\Http\Controllers\Admin\CorporateController as AdminCorporateController;
use App\Http\Controllers\Admin\CreditApplicationController;
use App\Http\Controllers\Admin\DeliveryOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PricingRuleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Bot\VehicleLicensingController as BotVehicleLicensingController;
use App\Http\Controllers\Corporate\BulkQuoteController;
use App\Http\Controllers\Corporate\BulkUploadController;
use App\Http\Controllers\Corporate\CbzDirectPaymentController;
use App\Http\Controllers\Corporate\DeliveryOrderController as CorporateDeliveryOrderController;
use App\Http\Controllers\Corporate\EcoCashTopUpController;
use App\Http\Controllers\Corporate\LicenseDiskController;
use App\Http\Controllers\Corporate\QuoteController;
use App\Http\Controllers\Corporate\VehicleController;
use App\Http\Controllers\Corporate\WalletController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/license-disks/verify/{reference}', [LicenseDiskController::class, 'verify'])->name('license-disks.verify');
Route::post('/ecocash/callback/{topUp}', [EcoCashTopUpController::class, 'callback'])->name('ecocash.callback');
Route::post('/cbz-direct/callback/{payment}', [CbzDirectPaymentController::class, 'callback'])->name('cbz-direct.callback');
Route::post('/twilio/vehicle-licensing', [BotVehicleLicensingController::class, 'twilio'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('twilio.vehicle-licensing');
Route::post('/ussd-service/api/ussd', [BotVehicleLicensingController::class, 'ussd'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('ussd.vehicle-licensing');

Route::get('/vehicle-licensing', [BotVehicleLicensingController::class, 'index'])->name('bot.menu');
Route::get('/vehicle-licensing/track-delivery', [BotVehicleLicensingController::class, 'trackDeliveryForm'])->name('bot.delivery.track');
Route::post('/vehicle-licensing/track-delivery', [BotVehicleLicensingController::class, 'trackDelivery'])->name('bot.delivery.track.submit');
Route::get('/vehicle-licensing/{flow}/plate', [BotVehicleLicensingController::class, 'plate'])->name('bot.plate');
Route::post('/vehicle-licensing/quote', [BotVehicleLicensingController::class, 'quote'])->name('bot.quote');
Route::post('/vehicle-licensing/quotes/{quote}/continue', [BotVehicleLicensingController::class, 'continue'])->name('bot.continue');
Route::post('/vehicle-licensing/quotes/{quote}/buy', [BotVehicleLicensingController::class, 'buy'])->name('bot.buy');
Route::post('/vehicle-licensing/quotes/{quote}/credit', [BotVehicleLicensingController::class, 'credit'])->name('bot.credit');

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/corporates', [AdminCorporateController::class, 'index'])->name('admin.corporates.index');
    Route::patch('/admin/corporates/{corporate}/approve', [AdminCorporateController::class, 'approve'])->name('admin.corporates.approve');
    Route::get('/admin/pricing', [PricingRuleController::class, 'index'])->name('admin.pricing.index');
    Route::put('/admin/pricing', [PricingRuleController::class, 'update'])->name('admin.pricing.update');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/credit-applications', [CreditApplicationController::class, 'index'])->name('admin.credit-applications.index');
    Route::get('/admin/credit-applications/{application}', [CreditApplicationController::class, 'show'])->name('admin.credit-applications.show');
    Route::post('/admin/credit-applications/{application}/approve', [CreditApplicationController::class, 'approve'])->name('admin.credit-applications.approve');
    Route::post('/admin/credit-applications/{application}/reject', [CreditApplicationController::class, 'reject'])->name('admin.credit-applications.reject');
    Route::get('/admin/delivery-orders', [DeliveryOrderController::class, 'index'])->name('admin.delivery-orders.index');
    Route::post('/admin/delivery-orders/{order}/assign', [DeliveryOrderController::class, 'assign'])->name('admin.delivery-orders.assign');
    Route::post('/admin/delivery-orders/{order}/status', [DeliveryOrderController::class, 'status'])->name('admin.delivery-orders.status');
    Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');

    Route::resource('vehicles', VehicleController::class);

    Route::get('/quotes/export', [QuoteController::class, 'export'])->name('quotes.export');
    Route::get('/quotes/bulk', [BulkQuoteController::class, 'create'])->name('quotes.bulk.create');
    Route::post('/quotes/bulk', [BulkQuoteController::class, 'store'])->name('quotes.bulk.store');
    Route::post('/quotes/bulk-purchase', [BulkQuoteController::class, 'purchase'])->name('quotes.bulk.purchase');
    Route::resource('quotes', QuoteController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/quotes/{quote}/purchase', [QuoteController::class, 'purchase'])->name('quotes.purchase');
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

    Route::get('/bulk/upload', [BulkUploadController::class, 'create'])->name('bulk.upload');
    Route::post('/bulk/upload', [BulkUploadController::class, 'store'])->name('bulk.upload.store');
    Route::get('/bulk/imports/{import}', [BulkUploadController::class, 'show'])->name('bulk.imports.show');
    Route::get('/bulk/imports/{import}/errors', [BulkUploadController::class, 'errors'])->name('bulk.imports.errors');

    Route::get('/license-disks', [LicenseDiskController::class, 'index'])->name('license-disks.index');
    Route::get('/license-disks/{licenseDisk}', [LicenseDiskController::class, 'show'])->name('license-disks.show');
    Route::get('/license-disks/{licenseDisk}/pdf', [LicenseDiskController::class, 'pdf'])->name('license-disks.pdf');
    Route::get('/deliveries', [CorporateDeliveryOrderController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{deliveryOrder}', [CorporateDeliveryOrderController::class, 'show'])->name('deliveries.show');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/ecocash', [EcoCashTopUpController::class, 'store'])->name('wallet.ecocash.store');
    Route::post('/wallet/ecocash/{topUp}/approve', [EcoCashTopUpController::class, 'approve'])->name('wallet.ecocash.approve');
    Route::post('/wallet/ecocash/{topUp}/callback', [EcoCashTopUpController::class, 'callback'])->name('wallet.ecocash.callback');
    Route::post('/wallet/cbz-direct', [CbzDirectPaymentController::class, 'store'])->name('wallet.cbz-direct.store');
    Route::post('/wallet/cbz-direct/{payment}/approve', [CbzDirectPaymentController::class, 'approve'])->name('wallet.cbz-direct.approve');
    Route::post('/wallet/cbz-direct/{payment}/callback', [CbzDirectPaymentController::class, 'callback'])->name('wallet.cbz-direct.callback');
});

require __DIR__.'/auth.php';
