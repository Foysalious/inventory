<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\DataMigrationController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\SkuController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ValueController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ChannelController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1'], function(){
    Route::group(['prefix'=>'partners/{partner_id}'], function() {
        Route::get('category-tree', [CategoryController::class, 'getMasterSubCat']);
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::post('{category_id}', [CategoryController::class, 'update']);
        });
        Route::apiResource('collection', CollectionController::class);
        Route::group(['prefix'=>'webstore'], function() {
            Route::get('products', [ProductController::class, 'getWebstoreProducts']);
        });
    });
    Route::apiResource('partners.options', OptionController::class);
    Route::apiResource('partners.options.values', ValueController::class)->only('store');
    Route::apiResource('partners.values', ValueController::class)->only('update');
    Route::apiResource('partners.products', ProductController::class);
    Route::apiResource('options', OptionController::class);
    Route::apiResource('options.values', ValueController::class)->shallow();
    Route::apiResource('partners.categories', CategoryController::class);
    Route::apiResource('partners.migrate', DataMigrationController::class)->only('store');
    Route::group(['prefix' => 'units'], function () {
        Route::get('/', [UnitController::class, 'index']);
    });
    Route::get('partners/{partner}/category-products', [CategoryProductController::class, 'getProducts']);
    Route::apiResource('collection', CollectionController::class);
    Route::get('/channels', [ChannelController::class, 'index']);
    Route::apiResource('partners.skus', SkuController::class);
});
