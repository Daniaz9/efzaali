<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('stores', StoreController::class);

Route::apiResource('products', ProductController::class);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

//Route::post('cart', [CartController::class, 'store']);
//Route::put('/{id}', [CartController::class, 'update']);
//Route::delete('/{id}', [CartController::class, 'destroy']);
//Route::post('/checkout', [CartController::class, 'checkout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart/', [CartController::class, 'index']);
    Route::post('/cart/', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);

    Route::post('/orders/{order}/offers', [OfferController::class, 'store']);
    Route::get('/orders/{order}/offers', [OfferController::class, 'index']);
    Route::post('/orders/{order}/offers/{offer}/accept', [OfferController::class, 'accept']);
//    ->middleware('role:normal-user')
});
