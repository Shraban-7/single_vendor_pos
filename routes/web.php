<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\StaticPageController;
use App\Models\Size;

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);

// Route::get('fix-category', function(){
//     $products = \App\Models\Product::get();
//     foreach($products as $product) {
//         $product->update([
//             'category_id' => null,
//             'subcategory_id' => null,
//             'sub_subcategory_id' => null
//         ]);
//     }
// });

// Route::get('/image-path', function () {
//     $products = \App\Models\Product::select('id', 'image')->get();
//     foreach ($products as $product) {
//         $product->image = str_replace(['images/spinner-fashion', 'https://slash-mart.com/storage/', 'spinner-fashion'], '', $product->image);
//         $product->save();
//     }

//     $productImages = \App\Models\ProductImage::select('id', 'image_path')->get();
//     foreach ($productImages as $productImage) {
//         $productImage->image_path = str_replace(['images/spinner-fashion', 'https://slash-mart.com/storage/', 'spinner-fashion'], '', $productImage->image_path);
//         $productImage->save();
//     }
// });

// function fixThumbnailPath($path)
// {
//     return str_replace(['images/spinner-fashion', 'https://slash-mart.com/storage/', 'spinner-fashion'], '', $path);
// }

// Route::get('save-products', function () {
//     ini_set('max_execution_time', 3600);
//     $json = file_get_contents('sp_products.json');
//     $products = json_decode($json, true);

//     foreach ($products as $product) {
//         $brandId = $brandName = null;

//         if ($product['brand'] != null) {
//             $brandName = $product['brand'];
//             $brand = \App\Models\Brand::firstOrCreate([
//                 'name' => $brandName,
//                 'slug' => str_slug($brandName),
//             ]);

//             $brandId = $brand->id;
//         }
//         $alreadyExists = \App\Models\Product::where('name', $product['name'])->first();
//         if ($alreadyExists) {
//             $alreadyExists->update([
//                 'sku' => $product['sku'],
//                 'price' => $product['price'] ?? 0,
//                 'cost_price' => $product['buying_price'] ?? 0,
//                 'stock_in' => $product['stock'],
//                 'stock_out' => 0,
//             ]);

//             if (isset($product['variants']) && is_array($product['variants'])) {
//                 saveVariants($alreadyExists, $product['variants']);
//             }

//             continue; // Skip if product already exists
//         }

//         // $category = \App\Models\Category::firstOrCreate([
//         //     'name' => $product['category'],
//         //     'slug' => str_slug($product['category']),
//         // ]);

//         // $subcategory = null;

//         // if ($product['subcategory']) {
//         //     $cat_id = $category->id;
//         //     $subcategory = \App\Models\Category::firstOrCreate([
//         //         'name' => $product['subcategory'],
//         //         'parent_id' => $cat_id,
//         //         'slug' => str_slug($product['subcategory']),
//         //     ]);
//         // }

//         $newProduct = \App\Models\Product::create([
//             'name' => ucwords($product['name']),
//             'slug' => $product['slug'],
//             'sku' => $product['sku'],
//             'image' => fixThumbnailPath($product['thumbnail']),

//             'description' => $product['description'] ?? null,
//             'short_description' => $product['short_description'] ?? null,

//             'price' => $product['price'] ?? 0,
//             'cost_price' => $product['buying_price'] ?? 0,

//             //'category_id' => $category->id ?? null,
//             //'subcategory_id' => $subcategory->id ?? null,

//             'brand_name' => $brandName,
//             'brand_id' => $brandId,
//             'stock_in' => $product['stock'],
//             'stock_out' => 0,
//         ]);

//         foreach ($product['images'] as $imageUrl) {
//             \App\Models\ProductImage::create([
//                 'product_id' => $newProduct->id,
//                 'image_path' => fixThumbnailPath($imageUrl),
//             ]);
//         }

//         if (isset($product['variants']) && is_array($product['variants'])) {
//             saveVariants($newProduct, $product['variants']);
//         }
//     }

//     echo 'done';
// });

// function saveVariants($newProduct, $variants)
// {
//     foreach ($variants as $variant) {

//         $alreadyExists = \App\Models\ProductVariant::where('sku', $variant['sku'])->first();

//         if ($alreadyExists) {
//             $alreadyExists->update([
//                 'stock_in' => $variant['stock'],
//                 'stock_out' => 0,
//                 'price' => $variant['price'] ?? 0,
//                 'cost_price' => $variant['buying_price'] ?? 0,
//             ]);
//             continue; // Skip if variant already exists
//         }

//         $size = $color = null;
//         if ($variant['size']) {
//             $size = Size::firstOrCreate([
//                 'name' => $variant['size'],
//                 'code' => strtolower($variant['size']),
//             ]);
//         }

//         if ($variant['color']) {
//             $color = \App\Models\Color::firstOrCreate(['name' => $variant['color']]);
//         }

//         try {
//             $newProduct->variants()->create([
//                 'sku' => $variant['sku'],
//                 'size_id' => $size->id ?? null,
//                 'color_id' => $color->id ?? null,
//                 'stock_in' => $variant['stock'],
//                 'price' => $variant['price'] ?? 0,
//                 'cost_price' => $variant['buying_price'] ?? 0,
//             ]);
//         } catch (\Exception $e) {
//             dump('Error creating product variant: ' . $e->getMessage());
//         }
//     }
// }

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'loginView'])->middleware('guest')->name('login');
Route::get('/register', [AuthController::class, 'registerView'])->middleware('guest')->name('register');
Route::prefix('auth')->as('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::prefix('products')->as('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/update-category', [ProductController::class, 'updateCategory'])->name('updateCategory');
    Route::post('/update-category/{product_id}', [ProductController::class, 'setCategory'])->name('setCategory');
    Route::get('search', [ProductController::class, 'search'])->name('search');
    Route::get('/suggestions', [ProductController::class, 'suggestions'])->name('suggestions');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/quickview', [ProductController::class, 'getQuickviewData'])->name('getQuickviewData');
});

Route::prefix('cart')->as('cart.')->group(function () {
    Route::get('/', [CartController::class, 'getCart'])->name('get');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::put('/update/{itemId}', [CartController::class, 'updateQuantity'])->name('update');
    Route::delete('/remove/{itemId}', [CartController::class, 'removeItem'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clearCart'])->name('clear');
});

Route::prefix('checkout')->as('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::post('/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('validate-coupon');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
});

// Public Order Tracking (no login required)
Route::prefix('track-order')->as('track-order.')->group(function () {
    Route::get('/', [OrderController::class, 'trackOrder'])->name('index');
});

Route::middleware('auth')->group(function () {
    // Customer Dashboard
    Route::as('customer.')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [CustomerController::class, 'updatePassword'])->name('password.update');
        Route::get('/addresses', [CustomerController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [CustomerController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [CustomerController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [CustomerController::class, 'deleteAddress'])->name('addresses.delete');
        Route::get('/wishlist', [CustomerController::class, 'wishlist'])->name('wishlist');
        Route::get('/reviews', [CustomerController::class, 'reviews'])->name('reviews');
    });

    // Orders
    Route::prefix('orders')->as('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order:order_number}/show', [OrderController::class, 'show'])->name('show');
        Route::post('/{order:order_number}/pay-now', [CheckoutController::class, 'payNow'])->name('payNow');
        Route::get('/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
});

Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');

Route::prefix('payment')->as('payment.')->group(function () {
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancelled', [PaymentController::class, 'cancelled'])->name('cancelled');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('failed');
    Route::post('/ipn', [PaymentController::class, 'ipn'])->name('ipn');
});

Route::get('/pages/{slug}', [StaticPageController::class, 'show'])->name('static_page.show');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.old');
