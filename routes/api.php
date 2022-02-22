<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Checkout\LinkController as CheckoutLinkController;
use App\Http\Controllers\Checkout\OrderController as CheckoutOrderController;

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

    Route::get('user', [AuthController::class, 'user']);

//Admin

Route::group([
    'middleware' => 'scope.admin',
    'prefix' => 'admin',
    'namespace' => 'Admin'
], function () {
    Route::post('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::put('users/info', 'AuthController@updateInfo');
    Route::put('users/password', 'AuthController@updatePassword');
    Route::get('chart', 'DashboardController@chart');
    Route::post('upload', 'ImageController@upload');
    Route::get('export', 'OrderController@export');

    Route::apiResource('users', 'UserController');
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('products', 'ProductController');
    Route::apiResource('orders', 'OrderController')->only('index', 'show');
    Route::apiResource('permissions', 'PermissionController')->only('index');
});

//Influencer

Route::group([
    'prefix' => 'influencer',
    'namespace' => 'Influencer'
], function () {
    Route::get('products', 'Influencer\ProductController@index');
    Route::group([
        'middleware' => 'scope.influencer'
    ], function () {
        Route::post('links', 'LinkController@store');
        Route::get('stats', 'StatsController@index');
        Route::get('rankings', 'StatsController@rankings');
    });
});

Route::group([
    'prefix' => 'checkout',
    'namespace' => 'Checkout',
], function () {
    Route::get('links/{code}', [CheckoutLinkController::class, 'show']);
    Route::post('orders', [CheckoutOrderController::class, 'store']);
    Route::post('orders/confirm', [CheckoutOrderController::class, 'confirm']);
});
