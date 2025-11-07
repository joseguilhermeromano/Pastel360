<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::middleware('api')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
});
