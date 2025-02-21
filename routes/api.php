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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(App\Http\Controllers\Api\AuthController::class)->group(function(){
    Route::post('/v1/auth/login','login')->name('login');
    Route::post('/v1/auth/register','register');
    Route::post('/v1/auth/logout','logout');
    Route::post('/v1/auth/refresh','refresh');
    Route::post('/v1/auth/forgot-password', 'forgotPassword');
});
//Routes requare auth
Route::middleware('jwt.auth:api')->group(function(){
    Route::controller(App\Http\Controllers\Api\ProfileController::class)->group(function(){
        Route::get('/v1/profile', 'profile');
        Route::post('/v1/profile/update', 'update');
        Route::post('/v1/profile/update-image', 'updateImage');
        Route::post('/v1/profile/change-password', 'changePassword');
        Route::delete('/v1/profile/delete', 'delete');
    });

    Route::controller(App\Http\Controllers\Api\RecentViewedController::class)->group(function(){
        Route::get('/v1/recent-viewed', 'products');
        Route::delete('/v1/recent-viewed/clear', 'clear');
    });

    Route::controller(App\Http\Controllers\Api\WishlistController::class)->group(function(){
        Route::get('/v1/wishlists', 'wishlists');
        Route::get('/v1/wishlist/products', 'wishlistProducts');
        Route::post('/v1/wishlist/add', 'wishlistAdd');
        Route::delete('/v1/wishlist/{id}/delete', 'wishlistRemove');
    });

    Route::controller(App\Http\Controllers\Api\CartController::class)->group(function(){
        Route::get('/v1/cart', 'getCart');
        Route::post('/v1/cart/add', 'addCart');
        Route::post('/v1/cart/{id}/increase', 'increaseQuantity');
        Route::post('/v1/cart/{id}/decrease', 'decreaseQuantity');
        Route::delete('/v1/cart/{id}/delete', 'deleteCart');
        Route::delete('/v1/cart/clear', 'clearCart');
    });

    Route::controller(App\Http\Controllers\Api\CouponController::class)->group(function(){
        Route::get('/v1/coupons', 'getCoupons');
        Route::post('/v1/coupon/apply', 'apply');
        Route::delete('/v1/coupon/clear', 'clear');
    });

    Route::controller(App\Http\Controllers\Api\AddressController::class)->group(function(){
        Route::get('/v1/address', 'getAll');
        Route::get('/v1/address/countries', 'getCountries');
        Route::post('/v1/address/add', 'add');
        Route::post('/v1/address/{id}/update', 'update');
        Route::put('/v1/address/{id}/default', 'makeDefault');
        Route::delete('/v1/address/{id}/delete', 'remove');
    });

    Route::controller(App\Http\Controllers\Api\CheckoutController::class)->group(function(){
        Route::get('/v1/checkout', 'checkout');
        Route::post('/v1/checkout/place-order', 'placeOrder');
    });

    Route::controller(App\Http\Controllers\Api\PaymentController::class)->group(function(){
        //Route::post('/v1/payment/create-card-payment', 'cardPayment');
        Route::post('/v1/payment/create-paypal-payment', 'paypalPayment');
        Route::get('/v1/payment/paypal-success', 'paypalSuccess');
        Route::get('/v1/payment/paypal-cancel', 'paypalCancel');
    });

      Route::controller(App\Http\Controllers\Api\OrderController::class)->group(function(){
        //Route::post('/v1/payment/create-card-payment', 'cardPayment');
        Route::get('/v1/orders', 'getOrders');
        Route::get('/v1/order/{id}', 'getOrderDetail');
        Route::post('/v1/order/add-review/{id}', 'addReView');
        Route::get('/v1/order/{id}/invoice', 'downloadInvoice');
        Route::get('/v1/order/item/{id}/download', 'downloadOrderItemFile');
    });

});

Route::get("/v1/home", [App\Http\Controllers\Api\HomeController::class, 'index']);

Route::controller(App\Http\Controllers\Api\ProductController::class)->group(function(){
    Route::get('/v1/products', 'products');
    Route::get('/v1/product/{id}', 'productDetail');
    Route::get('/v1/products/filter', 'productFilter');
    Route::post('/v1/product/{id}', 'productViewed');
});

Route::controller(App\Http\Controllers\Api\CategoryController::class)->group(function(){
    Route::get('/v1/categories', 'categories');
    Route::get('/v1/category/{id}', 'category');
    Route::get('/v1/category/{id}/products', 'categoryProducts');
});

Route::controller(App\Http\Controllers\Api\BrandController::class)->group(function(){
    Route::get('/v1/brands', 'brands');
    Route::get('/v1/brand/category/{id}', 'brandCategory');
    Route::get('/v1/brand/{id}', 'brand');
    Route::get('/v1/brand/{id}/products', 'brandProducts');
});

Route::controller(App\Http\Controllers\Api\FlashSaleController::class)->group(function(){
    Route::get('/v1/flashsale/{id}', 'flashsale');
    Route::get('/v1/flashsale/{id}/products', 'flashsaleProducts');
});

Route::controller(App\Http\Controllers\Api\ReviewController::class)->group(function(){
    Route::get('/v1/reviews/{id}', 'reviews');
    Route::get('/v1/review-stat/{id}', 'reviewStat');
});

Route::get("/v1/faqs", [App\Http\Controllers\Api\FaqController::class, 'getFaqs']);
Route::get("/v1/contact", [App\Http\Controllers\Api\ContactController::class, 'getContact']);
Route::get("/v1/page", [App\Http\Controllers\Api\PageController::class, 'getPage']);





