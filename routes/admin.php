<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SaleReturnController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;
// , 'read_only'
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
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



        Route::get('/update-category', [ProductController::class, 'updateCategory'])->name('updateCategory');
        Route::post('/update-category/{product_id}', [ProductController::class, 'setCategory'])->name('setCategory');
    });

    //barcode
    Route::prefix('barcodes')->as('barcodes.')->group(function () {
        Route::get('/', [ProductController::class, 'printBarcode'])->name('index');
        Route::get('/labels', [ProductController::class, 'printBarcodeLabels'])->name('label');
    });
    Route::prefix('sale-returns')->as('saleReturns.')->group(function () {
        Route::get('/', [SaleReturnController::class, 'index'])->name('index');
        Route::get('/{return}', [SaleReturnController::class, 'show'])->name('show');
    });

    Route::prefix('expenses')->as('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::post('/store', [ExpenseController::class, 'store'])->name('store');
        Route::put('{expense}/', [ExpenseController::class, 'update'])->name('update');
        Route::delete('{expense}/', [ExpenseController::class, 'destroy'])->name('delete');
    });

    Route::prefix('cash-register')->as('cashRegister.')->group(function () {
        Route::post('open/', [CashRegisterController::class, 'open'])->name('open');
        Route::post('{register}/close/', [CashRegisterController::class, 'close'])->name('close');
    });

    Route::prefix('categories')->as('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('brands')->as('brands.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::put('/{brand}/update', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}', [BrandController::class, 'delete'])->name('delete');
    });

    Route::prefix('reviews')->as('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::post('{review}/approve', [ReviewController::class, 'approve'])->name('approve');
        Route::post('{review}/delete', [ReviewController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('customers')->as('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::put('/{customer}/update', [CustomerController::class, 'update'])->name('update');
    });

    Route::prefix('employees')->as('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}/update', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}/delete', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('banners')->as('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::put('/{banner}/update', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}/delete', [BannerController::class, 'delete'])->name('delete');
    });

    Route::prefix('static-pages')->as('static_pages.')->group(function () {
        Route::get('/', [StaticPageController::class, 'index'])->name('index');
        Route::get('/create', [StaticPageController::class, 'create'])->name('create');
        Route::post('/store', [StaticPageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StaticPageController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [StaticPageController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [StaticPageController::class, 'destroy'])->name('delete');
    });

    Route::prefix('pos')->as('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/load-orders', [PosController::class, 'getPosOrders'])->name('loadOrders');
        Route::post('/store', [PosController::class, 'store'])->name('store');
        Route::post('/update/{orderId}', [PosController::class, 'update'])->name('update');
        Route::post('/draft', [PosController::class, 'saveDraft'])->name('saveDraft');
        Route::get('/search', [PosController::class, 'searchProducts'])->name('search');
        Route::get('/search/customer', [PosController::class, 'searchCustomers'])->name('searchCustomers');
        Route::get('/{orderNumber}/receipt', [PosController::class, 'receipt'])->name('receipt');

        Route::get('/get-products', [PosController::class, 'getProducts'])->name('getProducts');



        Route::prefix('cart')->as('cart.')->group(function () {
            Route::get('/', [PosController::class, 'getCart'])->name('get');
            Route::post('/add', [PosController::class, 'addToCart'])->name('add');
            Route::put('/update/{itemId}', [PosController::class, 'updateQuantity'])->name('update');
            Route::delete('/remove/{itemId}', [PosController::class, 'removeItem'])->name('remove');
            Route::post('/update-item-price/{itemId}', [PosController::class, 'updateItemPrice'])->name('updateItemPrice');
            Route::delete('/clear', [PosController::class, 'clearCart'])->name('clear');
        });
    });

    Route::prefix('orders')->as('orders.')->group(function () {
            Route::get('/', [PosController::class, 'posSales'])->name('index');
            Route::get('/{order_number}', [PosController::class, 'saleShow'])->name('show');
            Route::delete('/{id}', [PosController::class, 'saleDelete'])->name('destroy');
    });


    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/overview', [ReportController::class, 'overview'])->name('overview');
        Route::get('/cash-registers', [ReportController::class, 'cashRegisters'])->name('cashRegisters');
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

    Route::prefix('activity-logs')->as('activityLogs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    });

    // Settings Routes
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/update', [SettingController::class, 'update'])->name('update');
    });

    // Homepage Sections Routes
    Route::prefix('home-sections')->as('home-sections.')->group(function () {
        Route::get('/', [HomeSectionController::class, 'index'])->name('index');
        Route::get('/create', [HomeSectionController::class, 'create'])->name('create');
        Route::post('/store', [HomeSectionController::class, 'store'])->name('store');
        Route::get('/{home_section}/edit', [HomeSectionController::class, 'edit'])->name('edit');
        Route::put('/{home_section}/update', [HomeSectionController::class, 'update'])->name('update');
        Route::delete('/{home_section}/delete', [HomeSectionController::class, 'destroy'])->name('destroy');
        Route::post('/{home_section}/toggle-status', [HomeSectionController::class, 'toggleStatus'])->name('toggle-status');

        // Select2 searches
        Route::get('/search/products', [HomeSectionController::class, 'searchProducts'])->name('search-products');
        Route::get('/search/categories', [HomeSectionController::class, 'searchCategories'])->name('search-categories');
        Route::get('/search/banners', [HomeSectionController::class, 'searchBanners'])->name('search-banners');

        // Dynamic Item Assignment
        Route::post('/{home_section}/items/add', [HomeSectionController::class, 'addItem'])->name('add-item');
        Route::delete('/items/{home_section_item}/delete', [HomeSectionController::class, 'removeItem'])->name('remove-item');
        Route::post('/{home_section}/items/reorder', [HomeSectionController::class, 'reorderItems'])->name('reorder-items');
    });

    // Notifications Routes
    Route::prefix('notifications')->as('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/poll', [NotificationController::class, 'pollNotifications'])->name('poll');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::get('/{notification}/read-redirect', [NotificationController::class, 'readAndRedirect'])->name('read-redirect');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    });

    Route::get('/integrations/{type}/{slug}/edit', [\App\Http\Controllers\Admin\IntegrationCredentialController::class, 'edit'])
        ->whereIn('type', ['payment', 'courier'])
        ->name('integrations.edit');

    Route::put('/integrations/{type}/{slug}', [\App\Http\Controllers\Admin\IntegrationCredentialController::class, 'update'])
        ->whereIn('type', ['payment', 'courier'])
        ->name('integrations.update');

    Route::get('/keep-alive', function () {
        return response()->json(['status' => 'alive']);
    })->name('keepAlive');
});
