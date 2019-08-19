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

Route::post('user/login', 'API\LoginController@login')->name('login');
Route::post('user/register', 'API\RegisterController@register')->name('register');


Route::get('products', 'ProductBrowsing@showList');
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::group(['middleware' => ['role:user']], function () {
        Route::get('products/{id}', 'ProductBrowsing@showFull')->where('id', '[0-9]+');
        Route::put('reviews/new', 'ReviewController@save');
        Route::delete('reviews/delete', 'ReviewController@delete');
//        Route::get('products/{id}/average', 'ProductBrowsing@getAverageRate')->where('id', '[0-9]+');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::post('products/save', 'ProductManagement@store');
        Route::delete('products/delete', 'ProductManagement@delete');


    });
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


