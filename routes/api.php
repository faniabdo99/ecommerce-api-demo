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


Route::group([
    'as' => 'api.',
    'prefix' =>'v1'
], function(){
    // Authentication System
    Route::prefix('auth')->group(function(){
        Route::post('signup', 'AuthController@postSignup')->name('auth.signup');
        Route::post('login', 'AuthController@postLogin')->name('auth.login');
    });

    Route::middleware('auth:api')->group(function (){
        // api/v1/store/{route}
       Route::prefix('store')->group(function(){
            Route::post('create', 'StoreController@postNew')->name('store.postNew');
       });
        // api/v1/product/{route}
       Route::prefix('product')->group(function(){
           Route::get('/', 'ProductController@getAll')->name('product.getAll');
           Route::get('/{product}', 'ProductController@getSingle')->name('product.getSingle');
           Route::post('/', 'ProductController@postNew')->name('product.postNew');
           Route::post('/{product}', 'ProductController@postEdit')->name('product.postEdit');
           Route::delete('/{product}', 'ProductController@delete')->name('product.delete');
       });

        // api/v1/cart/{route}
        Route::prefix('cart')->group(function(){
            Route::get('/', 'CartController@getAll')->name('cart.getAll');
            Route::post('add', 'CartController@postNew')->name('cart.postNew');
        });
    });
});
