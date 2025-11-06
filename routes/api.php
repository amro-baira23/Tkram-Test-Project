<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix("/v1")->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post("/register", [AuthController::class, 'register'] );
});

Route::middleware(["auth:sanctum","throttle:api"])->prefix("/v1/products")->group(function(){
    Route::get('/', [ProductController::class, 'index']);
    Route::get("/{product}", [ProductController::class, 'show'] );
    Route::post("/", [ProductController::class, 'store'] );
    Route::put("/{product}", [ProductController::class, 'update'] );
    Route::delete("/{product}", [ProductController::class, 'destroy'] );
});

Route::middleware(["auth:sanctum","throttle:api"])->prefix("/v1/orders")->group(function(){
    Route::get('/', [OrderController::class, 'index']);
    Route::get("/{order}", [OrderController::class, 'show'] );
    Route::post("/", [OrderController::class, 'store'] );
    Route::put("/{order}/cancel", [OrderController::class, 'update'] );
});



Route::middleware(["auth:sanctum","throttle:api"])->prefix("/v1/cart")->group(function(){
    Route::get('/', [CartItemController::class, 'index']);
    Route::post("/", [CartItemController::class, 'store'] );
    Route::delete("/{cartItem}", [CartItemController::class, 'destroy'] );
    Route::delete("/", [CartItemController::class, 'destroyAll'] );
});
