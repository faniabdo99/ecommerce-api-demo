<?php

use Illuminate\Http\Request;
// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
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
        Route::post('signup', [AuthController::class, 'postSignup'])->name('auth.signup');
        Route::post('login', [AuthController::class, 'postLogin'])->name('auth.login');
    });

    Route::middleware('auth:api')->group(function (){
        // api/v1/store/{route}
        Route::middleware('merchant')->group(function (){

           Route::prefix('store')->group(function(){
                Route::post('create', [StoreController::class, 'postNew'])->name('store.postNew');
           });
            // api/v1/product/{route}
           Route::prefix('product')->group(function(){
               Route::get('/', [ProductController::class, 'getAll'])->name('product.getAll');
               Route::get('/{product}', [ProductController::class, 'getSingle'])->name('product.getSingle');
               Route::post('/', [ProductController::class, 'postNew'])->name('product.postNew');
               Route::put('/{product}', [ProductController::class, 'postEdit'])->name('product.postEdit');
               Route::delete('/{product}', [ProductController::class, 'delete'])->name('product.delete');
               Route::post('/localize/{product}', [ProductController::class, 'postLocalize'])->name('product.localize');
           });

        });

        // api/v1/cart/{route}
        Route::prefix('cart')->group(function(){
            Route::get('/', [CartController::class, 'getAll'])->name('cart.getAll');
            Route::post('add', [CartController::class, 'postNew'])->name('cart.postNew');
            Route::post('delete/{cart_item}', [CartController::class, 'delete'])->name('cart.delete');
        });
    });
});
