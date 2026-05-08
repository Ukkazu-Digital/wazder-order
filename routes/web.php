<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order/{encoded_trx?}', [OrderController::class, 'index']);
Route::post('/order/store', [OrderController::class, 'store']);
// Route::get('/order/status/{encoded_trx?}', [OrderController::class, 'track']);

Route::get('/order/status/{encoded_trx?}', function() {
    // Membuat objek dummy menggunakan class standar PHP
    $order = (object) [
        'order_code' => 'TRX-99283-X',
        'status' => 'shipped', // Pilihan: pending, paid, shipped, completed
        'total_price' => 245000,
        'created_at' => now()->subHours(5),
        'customer' => (object) [
            'customers_name' => 'Budi Santoso',
            'address' => 'Jl. Merdeka No. 45, Kecamatan Cicurug, Kabupaten Sukabumi, Jawa Barat 43359',
            'latitude' => -6.7844,
            'longitude' => 106.7911
        ],
        'orderDetails' => collect([
            (object) [
                'product' => (object) ['name' => 'Kaos Polos Premium Black'],
                'qty' => 2,
                'buy_price' => 75000,
                'subtotal' => 150000
            ],
            (object) [
                'product' => (object) ['name' => 'Topi Snapback Original'],
                'qty' => 1,
                'buy_price' => 95000,
                'subtotal' => 95000
            ]
        ])
    ];

    return view('order.track', compact('order'));
});