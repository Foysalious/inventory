<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionController;
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
    Route::group(['prefix'=>'categories'], function(){
        Route::post('/', [CategoryController::class, 'store']);
    });

    Route::group(['prefix'=>'options'], function(){
        Route::resource('/', OptionController::class);
        Route::group(['prefix'=>'{options}'], function(){
            Route::post('values', [ValueController::class, 'store']);
        });
    });
});
