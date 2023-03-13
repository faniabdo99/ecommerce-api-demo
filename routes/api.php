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
    Route::prefix('auth')->group(function(){
        Route::post('signup', 'AuthController@postSignup')->name('auth.signup');
        Route::post('login', 'AuthController@postLogin')->name('auth.login');
    });
});
