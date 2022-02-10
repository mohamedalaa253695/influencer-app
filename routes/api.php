<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Checkout\LinkController as CheckoutLinkController;
use App\Http\Controllers\Checkout\OrderController as CheckoutOrderController;
use App\Http\Controllers\Influencer\LinkController;
use App\Http\Controllers\Influencer\ProductController as InfluencerProductController;
use App\Http\Controllers\Influencer\StatsController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::put('users/info', [AuthController::class, 'updateInfo']);
    Route::put('users/password', [AuthController::class, 'updatePassword']);
});

//Admin
Route::group(['middleware' => ['auth:api', 'scope:admin'],
    'prefix' => 'admin',
    'namespace' => 'Admin'
], function () {
    Route::get('chart', [DashboardController::class, 'chart']);
    Route::get('orders/export', [OrderController::class, 'exportAsCsv']);

    Route::post('upload', [ImageController::class, 'upload']);

    Route::apiResource('users', 'UserController');
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('products', 'ProductController');
    Route::apiResource('orders', 'OrderController')->only('index', 'show');
    Route::apiResource('permissions', 'PermissionController')->only('index');
});

//Influencer
Route::group(['prefix' => 'influencer', 'namespace' => 'Influencer'], function () {
    Route::get('products', [InfluencerProductController::class, 'index']);

    Route::group(['middleware' => ['auth:api', 'scope:influencer'],
    ], function () {
        Route::post('links', [LinkController::class, 'store']);
        Route::get('stats', [StatsController::class, 'index']);
        Route::get('rankings', [StatsController::class, 'rankings']);
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
