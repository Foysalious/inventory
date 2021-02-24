<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ValueController;
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
    Route::apiResource('partners.options', OptionController::class);
    Route::apiResource('partners.options.values', ValueController::class)->shallow();
    Route::apiResource('partners.products', ProductController::class);
    Route::apiResource('options', OptionController::class);
    Route::group(['prefix'=>'partners/{partner_id}'], function() {
        Route::group(['prefix' => 'categories'], function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::post('{category_id}', [CategoryController::class, 'update']);
        });
    });
    Route::group(['prefix'=>'units'], function(){
        Route::get('/',[UnitController::class, 'index']);

    });
});
