<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Term of Payment
Route::apiResource('term-of-payments', 'App\\Http\\Controllers\\TermOfPaymentController');

// Tank Management
Route::apiResource('tanks', 'App\\Http\\Controllers\\TankController');
Route::post('tanks/{tank}/update-volume', 'App\\Http\\Controllers\\TankController@updateVolume');
