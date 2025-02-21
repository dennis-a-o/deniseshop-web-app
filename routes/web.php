<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
});

/*
|--------------------------------
| Authentication Routes
|-------------------------------
*/
Route::controller(App\Http\Controllers\Auth\AuthController::class)->group(function(){
    Route::get('/login','loginForm');
    Route::post('/login','login')->name('login');
    Route::get('/register','registerForm')->name('register');
    Route::post('/register','register');
    Route::get('/logout','logout')->name('logout');
    Route::get('/forgot-password','forgotPasswordForm');
    Route::post('/forgot-password','forgotPassword')->name('password.request');
    Route::get('/reset-password','resetPasswordForm')->name('password.reset');
    Route::post('/reset-password','resetPassword');
    Route::get('/verify-email','verifyEmailNotice')->name('verification.notify');
    Route::get('/verify-email/{id}/{hash}','verifyEmail')->name('verification.verify');
    Route::post('/verify-email','verifyEmailRequest')->name('verification.resend');

});

/*
|--------------------------------
| Installer Routes
|-------------------------------
*/
Route::middleware(['install'])->group(function(){
    Route::controller(App\Http\Controllers\Installer\InstallerController::class)->group(function(){
        Route::get('/install', 'index');
        Route::get('/install/requirements', 'requirements');
        Route::get('/install/permissions', 'permissions');
        Route::get('/install/environment', 'environment');
        Route::get('/install/environment/wizard', 'environmentWizard');
        Route::post('/install/environment/wizard', 'setEnvironmentWizard');
        Route::get('/install/environment/editor', 'environmentEditor');
        Route::post('/install/environment/editor', 'setEnvironmentEditor');
        Route::get('/install/database', 'database');
        Route::get('/install/final', 'final');
    });
});



/*
|--------------------------------
| Admin Routes
|-------------------------------
*/
Route::middleware(['admin', 'auth', 'auth.session'])->group(function(){
    Route::controller(App\Http\Controllers\Admin\DashboardController::class)->group(function(){
        Route::get('/admin/dashboard', 'index');
        Route::get('/admin/dashboard/current-month-data', 'currentMonthData');
    });

    Route::controller(App\Http\Controllers\Admin\NotificationController::class)->group(function(){
        Route::get('/admin/notification/navbar', 'index');
        Route::post('/admin/notification/clear', 'clear');
        Route::post('/admin/notification/read', 'read');
        Route::get('/admin/notification/view/{id}', 'view');
    });

    Route::controller(App\Http\Controllers\Admin\ProductsController::class)->group(function(){
        Route::get('/admin/products', 'index');
        Route::get('/admin/product-list', 'productList');
        Route::get('/admin/product/create', 'create');
        Route::post('/admin/product/create', 'store');
        Route::get('/admin/product/edit/{id}', 'edit');
        Route::post('/admin/product/edit/{id}', 'update');
        Route::post('/admin/product/delete', 'destroy');
        Route::post('/admin/product/status', 'status');
        Route::post('/admin/product/tag/create', 'tagCreate');
        Route::post('/admin/product/tag/delete', 'tagDestroy');
        Route::post('/admin/product/gallery/delete', 'galleryDestroy');
    });

    Route::controller(App\Http\Controllers\Admin\CategoryController::class)->group(function(){
        Route::get('/admin/product-categories', 'index');
        Route::get('/admin/product-category-list', 'categoryList');
        Route::post('/admin/product-category/create', 'store');
        Route::get('/admin/product-category/edit/{id}', 'edit');
        Route::post('/admin/product-category/edit/{id}', 'update');
        Route::post('/admin/product-category/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\TagController::class)->group(function(){
        Route::get('/admin/product-tags', 'index');
        Route::get('/admin/product-tag-list', 'categoryList');
        Route::post('/admin/product-tag/create', 'store');
        Route::get('/admin/product-tag/edit/{id}', 'edit');
        Route::post('/admin/product-tag/edit/{id}', 'update');
        Route::post('/admin/product-tag/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\BrandController::class)->group(function(){
        Route::get('/admin/brands', 'index');
        Route::get('/admin/brand-list', 'brandList');
        Route::get('/admin/brand/create', 'create');
        Route::post('/admin/brand/create', 'store');
        Route::get('/admin/brand/edit/{id}', 'edit');
        Route::post('/admin/brand/edit/{id}', 'update');
        Route::post('/admin/brand/delete', 'destroy');
        Route::post('/admin/brand/status/edit', 'status');
        Route::post('/admin/brand/featured/edit', 'featured');
    });

    Route::controller(App\Http\Controllers\Admin\ReviewController::class)->group(function(){
        Route::get('/admin/reviews', 'index');
        Route::get('/admin/review-list', 'reviewList');
        Route::get('/admin/review/detail/{id}', 'detail');
        Route::post('/admin/review/delete', 'destroy');
        Route::post('/admin/review/status/edit', 'status');
    });

    Route::controller(App\Http\Controllers\Admin\OrderController::class)->group(function(){
        Route::get('/admin/orders', 'index');
        Route::get('/admin/order-list', 'orderList');
        Route::get('/admin/order/edit/{id}', 'edit');
        Route::post('/admin/order/delete', 'destroy');
        Route::post('/admin/order/confirm', 'confirm');
        Route::post('/admin/order/status/edit', 'status');
        Route::post('/admin/order/refund', 'refund');
        Route::post('/admin/order/download/access', 'downloadAccess');
        Route::post('/admin/order/payment/confirm', 'confirmPayment');
        Route::post('/admin/order/shipping-address/update', 'shippingAddressUpdate');
    });

    Route::controller(App\Http\Controllers\Admin\InvoiceController::class)->group(function(){
          Route::get('/admin/order/generate-invoice/{id}', 'index');
    });

    Route::controller(App\Http\Controllers\Admin\ShipmentController::class)->group(function(){
        Route::get('/admin/shipments', 'index');
        Route::get('/admin/shipment-list', 'shipmentList');
        Route::post('/admin/shipment/status/edit', 'status');
        Route::get('/admin/shipment/edit/{id}', 'edit');
        Route::post('/admin/shipment/edit/{id}', 'update');
        Route::post('/admin/shipment/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\CouponController::class)->group(function(){
        Route::get('/admin/coupons', 'index');
        Route::get('/admin/coupon-list', 'couponList');
        Route::get('/admin/coupon/create', 'create');
        Route::post('/admin/coupon/create', 'store');
        Route::get('/admin/coupon/edit/{id}', 'edit');
        Route::post('/admin/coupon/edit/{id}', 'update');
        Route::post('/admin/coupon/status/edit', 'status');
        Route::post('/admin/coupon/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\FlashSaleController::class)->group(function(){
        Route::get('/admin/flash-sales', 'index');
        Route::get('/admin/flash-sale-list', 'flashSaleList');
        Route::get('/admin/flash-sale/search/product', 'searchProduct');
        Route::get('/admin/flash-sale/create', 'create');
        Route::post('/admin/flash-sale/create', 'store');
        Route::get('/admin/flash-sale/edit/{id}', 'edit');
        Route::post('/admin/flash-sale/edit/{id}', 'update');
        Route::post('/admin/flash-sale/status/edit', 'status');
        Route::post('/admin/flash-sale/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\CustomerController::class)->group(function(){
        Route::get('/admin/customers', 'index');
        Route::get('/admin/customer-list', 'customerList');
    });

    Route::controller(App\Http\Controllers\Admin\FaqController::class)->group(function(){
        Route::get('/admin/faqs', 'index');
        Route::get('/admin/faq-list', 'faqList');
        Route::get('/admin/faq/create', 'create');
        Route::post('/admin/faq/create', 'store');
        Route::get('/admin/faq/edit/{id}', 'edit');
        Route::post('/admin/faq/edit/{id}', 'update');
        Route::post('/admin/faq/status/edit', 'status');
        Route::post('/admin/faq/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\FaqCategoryController::class)->group(function(){
        Route::get('/admin/faq-categories', 'index');
        Route::get('/admin/faq-category-list', 'faqCategoryList');
        Route::post('/admin/faq-category/create', 'store');
        Route::get('/admin/faq-category/edit/{id}', 'edit');
        Route::post('/admin/faq-category/edit/{id}', 'update');
        Route::post('/admin/faq-category/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\PageController::class)->group(function(){
        Route::get('/admin/pages', 'index');
        Route::get('/admin/page-list', 'pageList');
        Route::get('/admin/page/create', 'create');
        Route::post('/admin/page/create', 'store');
        Route::get('/admin/page/edit/{id}', 'edit');
        Route::post('/admin/page/edit/{id}', 'update');
        Route::post('/admin/page/status/edit', 'status');
        Route::post('/admin/page/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\ContactController::class)->group(function(){
        Route::get('/admin/contacts', 'index');
        Route::get('/admin/contact-list', 'contactList');
        Route::get('/admin/contact/read/{id}', 'read');
        Route::post('/admin/contact/update/{id}', 'update');
        Route::post('/admin/contact/reply', 'reply');
        Route::post('/admin/contact/status', 'status');
        Route::post('/admin/contact/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\UserController::class)->group(function(){
        Route::get('/admin/users', 'index');
        Route::get('/admin/user-list', 'userList');
        Route::get('/admin/user/create', 'create');
        Route::post('/admin/user/create', 'store');
        Route::get('/admin/user/edit/{id}', 'edit');
        Route::post('/admin/user/edit/{id}', 'update');
        Route::get('/admin/user/review/{id}', 'reviewList');
        Route::post('/admin/user/verification', 'verification');
        Route::post('/admin/user/address/create', 'addAddress');
        Route::post('/admin/user/address/update', 'updateAddress');
        Route::post('/admin/user/address/delete', 'deleteAddress');
        Route::post('/admin/user/status', 'status');
        Route::post('/admin/user/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\ProfileController::class)->group(function(){
        Route::get('/admin/profile', 'index');
        Route::post('/admin/profile/edit', 'update');
        Route::post('/admin/profile/password/edit', 'updatePassword');
        Route::post('/admin/profile/session/clear', 'clearSession');
    });

    Route::controller(App\Http\Controllers\Admin\SubscriberController::class)->group(function(){
        Route::get('/admin/subscribers', 'index');
        Route::get('/admin/subscriber-list', 'subscriberList');
        Route::post('/admin/subscriber/status', 'status');
        Route::post('/admin/subscriber/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\PaymentController::class)->group(function(){
        Route::get('/admin/payment/transactions', 'index');
        Route::get('/admin/payment/transaction-list', 'paymentList');
        Route::get('/admin/payment/transaction/edit/{id}', 'edit');
        Route::post('/admin/payment/transaction/edit/{id}', 'update');
        Route::post('/admin/payment/transaction/status', 'status');
        Route::post('/admin/payment/transaction/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\PaymentMethodController::class)->group(function(){
        Route::get('/admin/payment/methods', 'index');
        Route::post('/admin/payment/method/paypal', 'updatePayPal');
        Route::post('/admin/payment/method/cod', 'updateCOD');
    });

    Route::controller(App\Http\Controllers\Admin\SliderController::class)->group(function(){
        Route::get('/admin/sliders', 'index');
        Route::get('/admin/slider-list', 'sliderList');
        Route::get('/admin/slider/create', 'create');
        Route::post('/admin/slider/create', 'store');
        Route::get('/admin/slider/edit/{id}', 'edit');
        Route::post('/admin/slider/edit/{id}', 'update');
        Route::post('/admin/slider/status/edit', 'status');
        Route::post('/admin/slider/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\CountryController::class)->group(function(){
        Route::get('/admin/countries', 'index');
        Route::get('/admin/country-list', 'countryList');
        Route::get('/admin/country/create', 'create');
        Route::post('/admin/country/create', 'store');
        Route::get('/admin/country/edit/{id}', 'edit');
        Route::post('/admin/country/edit/{id}', 'update');
        Route::post('/admin/country/status/edit', 'status');
        Route::post('/admin/country/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\StateController::class)->group(function(){
        Route::get('/admin/states', 'index');
        Route::get('/admin/state-list', 'stateList');
        Route::get('/admin/state/create', 'create');
        Route::post('/admin/state/create', 'store');
        Route::get('/admin/state/edit/{id}', 'edit');
        Route::post('/admin/state/edit/{id}', 'update');
        Route::post('/admin/state/status/edit', 'status');
        Route::post('/admin/state/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\CityController::class)->group(function(){
        Route::get('/admin/cities', 'index');
        Route::get('/admin/city-list', 'cityList');
        Route::get('/admin/city/create', 'create');
        Route::post('/admin/city/create', 'store');
        Route::get('/admin/city/edit/{id}', 'edit');
        Route::post('/admin/city/edit/{id}', 'update');
        Route::post('/admin/city/status/edit', 'status');
        Route::post('/admin/city/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\PickupLocationController::class)->group(function(){
        Route::get('/admin/pickup-locations', 'index');
        Route::get('/admin/pickup-location-list', 'pickupList');
        Route::get('/admin/pickup-location/create', 'create');
        Route::post('/admin/pickup-location/create', 'store');
        Route::get('/admin/pickup-location/edit/{id}', 'edit');
        Route::post('/admin/pickup-location/edit/{id}', 'update');
        Route::post('/admin/pickup-location/status/edit', 'status');
        Route::post('/admin/pickup-location/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\ShippingZoneController::class)->group(function(){
        Route::get('/admin/shipping-zones', 'index');
        Route::get('/admin/shipping-zone-list', 'zoneList');
        Route::get('/admin/shipping-zone/create', 'create');
        Route::post('/admin/shipping-zone/create', 'store');
        Route::get('/admin/shipping-zone/edit/{id}', 'edit');
        Route::post('/admin/shipping-zone/edit/{id}', 'update');
        Route::post('/admin/shipping-zone/status/edit', 'status');
        Route::post('/admin/shipping-zone/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\OrderReturnController::class)->group(function(){
        Route::get('/admin/order-returns', 'index');
        Route::get('/admin/order-return-list', 'returnList');
        Route::get('/admin/order-return/edit/{id}', 'edit');
        Route::post('/admin/order-return/edit/{id}', 'update');
        Route::post('/admin/order-return/status/edit', 'status');
        Route::post('/admin/order-return/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\SettingController::class)->group(function(){
        Route::get('/admin/setting/general', 'general');
        Route::post('/admin/setting/general', 'updateGeneral');
        Route::get('/admin/setting/ecommerce', 'ecommerce');
        Route::post('/admin/setting/ecommerce', 'updateEcommerce');
        Route::get('/admin/setting/email', 'email');
        Route::post('/admin/setting/email', 'updateEmail');
        Route::post('/admin/setting/theme','setTheme');
        Route::post('/admin/setting/palette','setPalette');
    });

    Route::controller(App\Http\Controllers\Admin\MediaController::class)->group(function(){
        Route::get('/admin/media', 'index');
        Route::get('/admin/media/window', 'window');
        Route::get('/admin/media/list', 'mediaList');
        Route::post('/admin/media/upload', 'upload');
        Route::get('/admin/media/download', 'download');
        Route::post('/admin/media/make-copy', 'makeCopy');
        Route::post('/admin/media/crop', 'crop');
        Route::post('/admin/media/create/folder', 'createFolder');
        Route::post('/admin/media/delete', 'destroy');
    });

    Route::controller(App\Http\Controllers\Admin\ReportController::class)->group(function(){
        Route::get('/admin/reports','index');
        Route::get('/admin/report/data', 'report');
        Route::get('/admin/report/top-selling', 'topSelling');
    });

    Route::get('/mailable', function () {
        //$invoice = App\Models\Invoice::find(1);
        $order = App\Models\Order::find(1);
        $payment = App\Models\Payment::find(1);
     
        return new App\Mail\PaymentCompleted($payment);
    });
});
