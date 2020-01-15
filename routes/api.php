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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([ 'prefix' => 'customer'], function ($router) {
    Route::post('login', 'Api\Auth\CustomerAuthController@login');
    Route::post('logout', 'Api\Auth\CustomerAuthController@logout');
    Route::post('refresh', 'Api\Auth\CustomerAuthController@refresh');
    Route::post('me', 'Api\Auth\CustomerAuthController@me');
});

Route::group([ 'prefix' => 'admin'], function ($router) {
    Route::post('login', 'Api\Auth\AdminAuthController@login');
    Route::post('logout', 'Api\Auth\AdminAuthController@logout');
    Route::post('refresh', 'Api\Auth\AdminAuthController@refresh');
    Route::post('me', 'Api\Auth\AdminAuthController@me');
});