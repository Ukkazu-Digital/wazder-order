<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\v2\ProductController as ProductControllerV2;
use App\Http\Controllers\Admin\KurirController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public / Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/maintenance', function () {
    if (config('app.maintenance') !== 'on' && env('APP_MAINTENANCE') !== 'on') {
        return redirect('/');
    }
    return view('maintenance');
})->name('maintenance');

// Front-end Order Routes
Route::controller(OrderController::class)->prefix('order')->name('order.')->group(function () {
    Route::get('/{encoded_trx?}', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
    Route::get('/status/{encoded_trx?}', 'track')->name('track');
    Route::get('/success/{kode_pesanan}', 'success')->name('success');
    Route::get('/failed/{msg}', 'failed')->name('failed');
});

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Order Management
    Route::controller(AdminOrderController::class)->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::get('/{order}/invoice', 'invoice')->name('invoice');
        Route::post('/{order}/send-invoice', 'sendInvoice')->name('sendInvoice');
        Route::post('/{order}/update-status', 'updateStatus')->name('updateStatus');
        Route::delete('/{order}', 'destroy')->name('destroy');
    });

    // Customer Management
    Route::controller(CustomerController::class)->prefix('customers')->name('customers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{customer}', 'show')->name('show');
        Route::delete('/{customer}', 'destroy')->name('destroy');
    });

    // Chat Management
    Route::controller(ChatController::class)->prefix('chats')->name('chats.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{contact_wa_id}', 'show')->name('show');
        Route::post('/{contact_wa_id}/send', 'send')->name('send');
        Route::get('/{contact_wa_id}/refresh', 'refresh')->name('refresh');
        Route::post('/{contact_wa_id}/complete-case', 'completeCase')->name('complete-case');
    });

    // Kasir Management
    Route::controller(KasirController::class)->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/search-customer', 'searchCustomers')->name('search_customer');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{order}', 'show')->name('show');
        Route::post('/{order}/update-status', 'updateStatus')->name('updateStatus');
        Route::delete('/{order}', 'destroy')->name('destroy');
    });

    // Resources V1 (Products & Kurirs)
    Route::resource('products', ProductController::class);
    Route::resource('kurirs', KurirController::class);

    // Stock Management
    Route::controller(StockManagementController::class)->prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        // Route baru untuk Stok Keluar / Penyesuaian
        Route::get('/adjustment', 'createAdjustment')->name('adjustment');
        Route::post('/adjustment', 'storeAdjustment')->name('store_adjustment');
        // Route baru untuk mengambil batch aktif (untuk AJAX)
        Route::get('/active-batches/{product_id}', 'getActiveBatches')->name('active_batches');
    });

    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::prefix('profit')->name('profit.')->group(function () {
            Route::get('/', 'profitReport')->name('index');
            Route::get('/export/excel', 'exportProfitExcel')->name('excel');
            Route::get('/export/pdf', 'exportProfitPdf')->name('pdf');
        });
        Route::get('/inventory-valuation', 'inventoryValuation')->name('inventory_valuation');
    });

    // Admin API / Feature V2
    Route::prefix('v2')->name('v2.')->group(function () {
        // Custom FIFO Operations (Di atas resource agar tidak bentrok)
        Route::post('products/checkout', [ProductControllerV2::class, 'checkoutOrder'])->name('products.checkout');
        Route::post('products/cancel/{order_id}', [ProductControllerV2::class, 'cancelOrder'])->name('products.cancel');
        
        Route::resource('products', ProductControllerV2::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
