<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;

// ----------------------------------------
// Public Routes
// ----------------------------------------

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('stores', StoreController::class);
Route::apiResource('products', ProductController::class);

// ----------------------------------------
// Authenticated Routes
// ----------------------------------------

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('cart')->controller(CartController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::post('/checkout', 'checkout');
    });

    Route::prefix('driver')->controller(DriverController::class)->group(function () {
        Route::get('/orders/open', 'openOrders');
        Route::get('/orders', 'myOrders');
        Route::get('/offers', 'myOffers');
        Route::post('/orders/{order}/status', 'updateStatus');
        Route::post('/order/{order}/offer', 'storeOffer');
    });

    Route::prefix('customer')->controller(CustomerController::class)->group(function () {
        Route::get('/orders', 'myOrders');
        Route::get('/orders/{id}', 'showOrder');
        Route::post('/orders/{order}/cancel', 'cancelOrder');
        Route::post('/{order}/offer/{offer}/accept', 'accept');
    });
});
