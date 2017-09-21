<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Api\Controllers\V2')->group(function () {

    Route::get('article.{id:[0-9]+}', 'ArticleController@show');

    Route::get('notice.{id:[0-9]+}', 'NoticeController@show');

    Route::post('order.notify.{code}', 'OrderController@notify');

    Route::get('product.intro.{id:[0-9]+}', 'GoodsController@intro');

    Route::get('product.share.{id:[0-9]+}', 'GoodsController@share');

    Route::get('ecapi.auth.web', 'UserController@webOauth');

    Route::get('ecapi.auth.web.callback/{vendor:[0-9]+}', 'UserController@webCallback');

    /**
     * Guest
     */
    Route::middleware(['xss'])->group(function () {

        Route::post('ecapi.access.dns', 'AccessController@dns');

        Route::post('ecapi.access.batch', 'AccessController@batch');

        Route::post('ecapi.category.list', 'GoodsController@category');

        Route::post('ecapi.product.list', 'GoodsController@index');

        Route::post('ecapi.home.product.list', 'GoodsController@home');

        Route::post('ecapi.search.product.list', 'GoodsController@search');

        Route::post('ecapi.review.product.list', 'GoodsController@review');

        Route::post('ecapi.review.product.subtotal', 'GoodsController@subtotal');

        Route::post('ecapi.recommend.product.list', 'GoodsController@recommendList');

        Route::post('ecapi.product.accessory.list', 'GoodsController@accessoryList');

        Route::post('ecapi.product.get', 'GoodsController@info');

        Route::post('ecapi.auth.signin', 'UserController@signin');

        Route::post('ecapi.auth.social', 'UserController@auth');

        Route::post('ecapi.auth.default.signup', 'UserController@signupByEmail');

        Route::post('ecapi.auth.mobile.signup', 'UserController@signupByMobile');

        Route::post('ecapi.user.profile.fields', 'UserController@fields');

        Route::post('ecapi.auth.mobile.verify', 'UserController@verifyMobile');

        Route::post('ecapi.auth.mobile.send', 'UserController@sendCode');

        Route::post('ecapi.auth.mobile.reset', 'UserController@resetPasswordByMobile');

        Route::post('ecapi.auth.default.reset', 'UserController@resetPasswordByEmail');

        // Route::post('ecapi.cardpage.get', 'CardPageController@view');

        // Route::post('ecapi.cardpage.preview', 'CardPageController@preview');

        Route::post('ecapi.config.get', 'ConfigController@index');

        Route::post('ecapi.article.list', 'ArticleController@index');

        Route::post('ecapi.brand.list', 'BrandController@index');

        Route::post('ecapi.search.keyword.list', 'SearchController@index');

        Route::post('ecapi.region.list', 'RegionController@index');

        Route::post('ecapi.invoice.type.list', 'InvoiceController@type');

        Route::post('ecapi.invoice.content.list', 'InvoiceController@content');

        Route::post('ecapi.invoice.status.get', 'InvoiceController@status');

        Route::post('ecapi.notice.list', 'NoticeController@index');

        Route::post('ecapi.banner.list', 'BannerController@index');

        Route::post('ecapi.version.check', 'VersionController@check');

        Route::post('ecapi.recommend.brand.list', 'BrandController@recommend');

        Route::post('ecapi.message.system.list', 'MessageController@system');

        Route::post('ecapi.message.count', 'MessageController@unread');

        Route::post('ecapi.site.get', 'SiteController@index');

        // Route::post('ecapi.splash.list', 'SplashController@index');

        // Route::post('ecapi.splash.preview', 'SplashController@view');

        // Route::post('ecapi.theme.list', 'ThemeController@index');

        // Route::post('ecapi.theme.preview', 'ThemeController@view');

        Route::post('ecapi.search.category.list', 'GoodsController@categorySearch');

        Route::post('ecapi.order.reason.list', 'OrderController@reasonList');

        // Route::post('ecapi.search.shop.list', 'ShopController@search');

        // Route::post('ecapi.recommend.shop.list', 'ShopController@recommand');

        // Route::post('ecapi.shop.list', 'ShopController@index');

        // Route::post('ecapi.shop.get', 'ShopController@info');

        Route::post('ecapi.areacode.list', 'AreaCodeController@index');

    });

    /**
     * Authorization
     */
    Route::middleware(['token', 'xss'])->group(function () {

        Route::post('ecapi.user.profile.get', 'UserController@profile');

        Route::post('ecapi.user.profile.update', 'UserController@updateProfile');

        Route::post('ecapi.user.password.update', 'UserController@updatePassword');

        Route::post('ecapi.order.list', 'OrderController@index');

        Route::post('ecapi.order.get', 'OrderController@view');

        Route::post('ecapi.order.confirm', 'OrderController@confirm');

        Route::post('ecapi.order.cancel', 'OrderController@cancel');

        Route::post('ecapi.order.price', 'OrderController@price');

        Route::post('ecapi.product.like', 'GoodsController@setLike');

        Route::post('ecapi.product.unlike', 'GoodsController@setUnlike');

        Route::post('ecapi.product.liked.list', 'GoodsController@likedList');

        Route::post('ecapi.order.review', 'OrderController@review');

        Route::post('ecapi.order.subtotal', 'OrderController@subtotal');

        Route::post('ecapi.payment.types.list', 'OrderController@paymentList');

        Route::post('ecapi.payment.pay', 'OrderController@pay');

        Route::post('ecapi.shipping.vendor.list', 'ShippingController@index');

        Route::post('ecapi.shipping.status.get', 'ShippingController@info');

        Route::post('ecapi.consignee.list', 'ConsigneeController@index');

        Route::post('ecapi.consignee.update', 'ConsigneeController@modify');

        Route::post('ecapi.consignee.add', 'ConsigneeController@add');

        Route::post('ecapi.consignee.delete', 'ConsigneeController@remove');

        Route::post('ecapi.consignee.setDefault', 'ConsigneeController@setDefault');

        Route::post('ecapi.score.get', 'ScoreController@view');

        Route::post('ecapi.score.history.list', 'ScoreController@history');

        Route::post('ecapi.cashgift.list', 'CashGiftController@index');

        Route::post('ecapi.cashgift.available', 'CashGiftController@available');

        Route::post('ecapi.push.update', 'MessageController@updateDeviceId');

        Route::post('ecapi.cart.add', 'CartController@add');

        Route::post('ecapi.cart.clear', 'CartController@clear');

        Route::post('ecapi.cart.delete', 'CartController@delete');

        Route::post('ecapi.cart.get', 'CartController@index');

        Route::post('ecapi.cart.update', 'CartController@update');

        Route::post('ecapi.cart.checkout', 'CartController@checkout');

        Route::post('ecapi.cart.promos', 'CartController@promos');

        Route::post('ecapi.product.purchase', 'GoodsController@purchase');

        Route::post('ecapi.product.validate', 'GoodsController@checkProduct');

        Route::post('ecapi.message.order.list', 'MessageController@order');

        // Route::post('ecapi.shop.watch', 'ShopController@watch');

        // Route::post('ecapi.shop.unwatch', 'ShopController@unwatch');

        // Route::post('ecapi.shop.watching.list', 'ShopController@watchingList');

        Route::post('ecapi.coupon.list', 'CouponController@index');

        Route::post('ecapi.coupon.available', 'CouponController@available');

        Route::post('ecapi.recommend.bonus.list', 'AffiliateController@index');

        Route::post('ecapi.recommend.bonus.info', 'AffiliateController@info');

        Route::post('ecapi.withdraw.submit', 'AccountController@submit');

        Route::post('ecapi.withdraw.cancel', 'AccountController@cancel');

        Route::post('ecapi.withdraw.list', 'AccountController@index');

        Route::post('ecapi.withdraw.info', 'AccountController@getDetail');

        Route::post('ecapi.balance.get', 'AccountController@surplus');

        Route::post('ecapi.balance.list', 'AccountController@accountDetail');

    });

});