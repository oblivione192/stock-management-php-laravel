<?php

use App\Http\Controllers\Controllers\CategoryController;
use App\Http\Controllers\Controllers\DashboardController;
use App\Http\Controllers\Controllers\ProductController;
use App\Http\Controllers\Controllers\ProfileController;
use App\Http\Controllers\Controllers\StockMovementController;
use App\Http\Controllers\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('inventory')->group(function () {

    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [UserController::class, 'authenticateUser']);
        Route::post('/register', [UserController::class, 'registerUser']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', DashboardController::class);

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });

        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });

        Route::prefix('stock-movements')->group(function () {
            Route::get('/', [StockMovementController::class, 'index']);
            Route::get('/{id}', [StockMovementController::class, 'show']);
            Route::post('/', [StockMovementController::class, 'store']);
        });

        Route::get('/profile', [ProfileController::class, 'getProfile']);
    });
});
