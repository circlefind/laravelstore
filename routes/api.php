<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$router->get('/', function () {
    return response()->json('REST API Eli Mangga Store');
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('loginadmin', 'AuthController@loginadmin');
    Route::post('register', 'AuthController@register');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
            Route::post('changepassword', 'AuthController@changepassword');
            Route::post('updateuser', 'AuthController@updateuser');
            Route::post('resetpassword', 'AuthController@resetpassword');
            Route::get('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
        });
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'users'
], function () {
    Route::get('user', 'UserController@user');
    Route::post('finduser', 'UserController@finduser');
    Route::get('usersecure', 'UserController@usersecure');
    Route::get('usernonsecure', 'UserController@usernonsecure');
    Route::get('userfield', 'UserController@userfield');
    Route::post('insertuser', 'UserController@insertuser');
    Route::post('updateuser', 'UserController@updateuser');
    Route::post('updateusersecure', 'UserController@updateusersecure');
    Route::post('deleteuser', 'UserController@deleteuser');
});

Route::group([
    'prefix' => 'configs'
], function () {
    Route::get('config', 'ConfigController@config');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
        Route::get('serverdate', 'ConfigController@serverdate');
        Route::post('updateconfig', 'ConfigController@updateconfig');
        Route::post('updateabout', 'ConfigController@updateabout');
        Route::post('updatetransmethod', 'ConfigController@updatetransmethod');
        Route::get('routeclear', 'ConfigController@routeclear');
        Route::get('routecache', 'ConfigController@routecache');
        Route::get('optimize', 'ConfigController@optimize');
        });
});

Route::group([
    'prefix' => 'banners'
], function () {
    Route::get('banner', 'BannerController@banner');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
            Route::post('updatebanner', 'BannerController@updatebanner');
            Route::get('getimage/{imgname}', 'BannerController@getimage');
        });
});

Route::group([
    'prefix' => 'banks'
], function () {
    Route::get('bank', 'BankController@bank');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
            Route::post('insertbank', 'BankController@insertbank');
            Route::post('updatebank', 'BankController@updatebank');
            Route::post('deletebank', 'BankController@deletebank');
        });
});

Route::group([
    'prefix' => 'couriers'
], function () {
    Route::get('courier', 'CourierController@courier');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
    Route::post('insertcourier', 'CourierController@insertcourier');
    Route::post('updatecourier', 'CourierController@updatecourier');
    Route::post('deletecourier', 'CourierController@deletecourier');
    });
});

Route::group([
    'prefix' => 'categories'
], function () {
    Route::get('category', 'CategoryController@category');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
        Route::post('insertcategory', 'CategoryController@insertcategory');
        Route::post('updatecategory', 'CategoryController@updatecategory');
        Route::post('deletecategory', 'CategoryController@deletecategory');
        });
});

Route::group([
    'prefix' => 'products'
], function () {
    Route::get('listproduct', 'ProductController@listproduct');
    Route::get('productpromo', 'ProductController@productpromo');
    Route::get('detailproduct/{id}', 'ProductController@detailproduct');
    Route::get('productcategory/{id}', 'ProductController@productcategory');
    Route::get('productfilter/{filter}', 'ProductController@productfilter');
    Route::get('otherproduct/{id}', 'ProductController@otherproduct');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
            Route::get('product', 'ProductController@product');
            Route::get('getimage/{imgname}', 'ProductController@getimage');
            Route::get('imgproduct/{id}', 'ProductController@imgproduct');
            Route::post('insertproduct', 'ProductController@insertproduct');
            Route::post('updateproduct', 'ProductController@updateproduct');
            Route::post('deleteproduct', 'ProductController@deleteproduct');
        });
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'carts'
], function () {
    Route::get('cart/{id}', 'CartController@cart');
    Route::post('insertcart', 'CartController@insertcart');
    Route::post('updateqty', 'CartController@updateqty');
    Route::post('updateremark', 'CartController@updateremark');
    Route::post('deletecart/{id}/{iduser}', 'CartController@deletecart');
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'checkouts'
], function () {
    Route::get('checkout/{id}', 'CheckoutController@checkout');
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'transactions'
], function () {
    Route::get('alltransaction', 'TransactionController@alltransaction');
    Route::get('reptransaction/{date}', 'TransactionController@reptransaction');
    Route::get('listtransaction/{id}', 'TransactionController@listtransaction');
    Route::get('notification/{id}', 'TransactionController@notification');
    Route::post('addnotification/{id}', 'TransactionController@addnotification');
    Route::post('removenotification/{id}', 'TransactionController@removenotification');
    Route::post('updatecourier', 'TransactionController@updatecourier');
    Route::post('updatecost', 'TransactionController@updatecost');
    Route::post('updatereceipt', 'TransactionController@updatereceipt');
    Route::post('updatetransfer', 'TransactionController@updatetransfer');
    Route::post('updatearrived', 'TransactionController@updatearrived');
});

Route::group([
    'prefix' => 'regions'
], function () {
    Route::get('region', 'RegionController@region');
    Route::group([
        'middleware' => 'auth:sanctum'
        ], function () {
        });
});


