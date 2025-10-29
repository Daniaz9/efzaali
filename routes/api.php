<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StoreController;
use Spatie\Permission\Models\Role;



Route::post('register/customer', [AuthController::class, 'registerCustomer']);
Route::post('register/driver', [AuthController::class, 'registerDriver']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::middleware(['role:customer'])->prefix('customer')->controller(CustomerController::class)->group(function () {
        Route::get('/orders', 'myOrders')->middleware('permission:view orders');
        Route::get('/orders/{id}', 'showOrder')->middleware('permission:view orders');
        Route::post('/orders/{order}/cancel', 'cancelOrder')->middleware('permission:cancel orders');
        Route::post('/offer/{offer}/accept', 'accept')->middleware('permission:create orders');
    });

    Route::middleware(['role:customer'])->prefix('cart')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->middleware('permission:view orders');
        Route::post('/', 'store')->middleware('permission:create orders');
        Route::put('/{id}', 'update')->middleware('permission:create orders');
        Route::delete('/{id}', 'destroy')->middleware('permission:create orders');
        Route::post('/checkout', 'checkout')->middleware('permission:create orders');
    });

    Route::middleware(['role:driver'])->prefix('driver')->controller(DriverController::class)->group(function () {
        Route::get('/orders/open', 'openOrders')->middleware('permission:view orders');
        Route::get('/orders', 'myOrders')->middleware('permission:view orders');
        Route::get('/offers', 'myOffers')->middleware('permission:view orders');
        Route::post('/orders/{order}/status', 'updateStatus')->middleware('permission:update order status');
        Route::post('/order/{order}/offer', 'storeOffer')->middleware('permission:create offers');
        Route::post('/available', 'changeAvailability')->middleware('permission:change availability');
    });

    Route::middleware(['permission:rate user'])->prefix('rate')->controller(RatingController::class)->group(function () {
        Route::post('/user/{order}', 'rateUser');
    });

    Route::middleware(['role:admin|super_admin'])->group(function () {
        Route::apiResource('stores', StoreController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    });

    Route::middleware(['role:driver|customer|super_admin'])->controller(ChatController::class)->group(function () {
        Route::get('/messages', 'index')->middleware('permission:view chat messages');
        Route::post('/messages',  'store')->middleware('permission:send message');
        Route::get('/conversations', 'conversations')->middleware('permission:view conversations');

    });
}
);
