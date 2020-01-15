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
// Route::group(['middleware' => ['cors']], function() {

Route::group(['middleware' => ['cors']], function() {

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
 

    //Old

    Route::post('send-sms', 'Api\Helper\SMSHelperController@resendSMSVerificationPin');
    Route::post('verify-phone', 'Api\Helper\SMSHelperController@resendSMSVerificationPin');

    Route::apiResource('register', 'Api\CustomerController');


    // Unanimous
    Route::OPTIONS('/updateSignedOutStatus', 'Api\Helper\UnanimousHelperController@updateSignedOut');
    Route::post('/updateSignedOutStatus', 'Api\Helper\UnanimousHelperController@updateSignedOut');
    
    Route::OPTIONS('get-customer-profile/{id?}', 'Api\Helper\UnanimousHelperController@getProfile');
    Route::get('get-customer-profile/{id?}',     'Api\Helper\UnanimousHelperController@getProfile');

    Route::OPTIONS('get-orders', 'Api\Helper\UnanimousHelperController@getOrders');
    Route::get('get-orders', 'Api\Helper\UnanimousHelperController@getOrders');

    Route::OPTIONS('get-available-promotions', 'Api\Helper\UnanimousHelperController@getAvailablePromotions');
    Route::get('get-available-promotions', 'Api\Helper\UnanimousHelperController@getAvailablePromotions');


     
    Route::OPTIONS('get-personal-code', 'Api\Helper\UnanimousHelperController@getPersonalCode');
    Route::get('get-personal-code', 'Api\Helper\UnanimousHelperController@getPersonalCode');


});