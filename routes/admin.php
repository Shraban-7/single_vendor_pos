<?php

use App\Http\Controllers\Admin\BannerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('products')->as('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}/update', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}/delete', [ProductController::class, 'destroy'])->name('delete');

        // Stock Management
        Route::get('/{product}/manage-stock', [ProductController::class, 'manageStock'])->name('manage-stock');
        Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('stock-history');
        Route::post('/stock/add', [ProductController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [ProductController::class, 'removeStock'])->name('stock.remove');
    });

    Route::prefix('orders')->as('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/update-tracking', [OrderController::class, 'updateTracking'])->name('update-tracking');
        Route::post('/{id}/update-notes', [OrderController::class, 'updateNotes'])->name('update-notes');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('categories')->as('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('reviews')->as('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
    });

    Route::prefix('customers')->as('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::put('/{customer}/update', [CustomerController::class, 'update'])->name('update');
    });

    Route::prefix('banners')->as('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::put('/{banner}/update', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}/delete', [BannerController::class, 'delete'])->name('delete');
    });

    Route::prefix('pos')->as('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/store', [PosController::class, 'store'])->name('store');
        Route::get('/search', [PosController::class, 'searchProducts'])->name('search');
    });
    
    // Categories Routes (placeholder)
    // Route::get('/categories', function () {
    //     return redirect()->route('admin.dashboard');
    // })->name('categories.index');

    // Reviews Routes (placeholder)
    // Route::get('/reviews', function () {
    //     return redirect()->route('admin.dashboard');
    // })->name('reviews.index');

    // Users Routes (placeholder)
    Route::get('/users', function () {
        return redirect()->route('admin.dashboard');
    })->name('users.index');

    // Coupons Routes (placeholder)
    Route::get('/coupons', function () {
        return redirect()->route('admin.dashboard');
    })->name('coupons.index');
    Route::get('/coupons/create', function () {
        return redirect()->route('admin.dashboard');
    })->name('coupons.create');

    // Banners Routes (placeholder)
    // Route::get('/banners', function () {
    //     return redirect()->route('admin.dashboard');
    // })->name('banners.index');

    // Settings Routes
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/update', [SettingController::class, 'update'])->name('update');
    });

    Route::get('/keep-alive', function () {
        return response()->json(['status' => 'alive']);
    })->name('keepAlive');
});
