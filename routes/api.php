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

Route::group(['middleware' => ['cors','LogRequest']], function() {

    // Customers
    Route::post('login',    'Api\Customer\Auth\JwtAuthController@login');
    Route::post('register', 'Api\Customer\Auth\JwtAuthController@register');
    Route::post('logout',   'Api\Customer\Auth\JwtAuthController@logout');
    Route::post('refresh',  'Api\Customer\Auth\JwtAuthController@refresh');
    Route::post('me',       'Api\Customer\Auth\JwtAuthController@me');

    // Route::group([ 'prefix' => 'customer'], function ($router) {
    // });

    // Route::group([ 'prefix' => 'admin'], function ($router) {
    //     Route::post('login', 'Api\Auth\AdminAuthController@login');
    //     Route::post('logout', 'Api\Auth\AdminAuthController@logout');
    //     Route::post('refresh', 'Api\Auth\AdminAuthController@refresh');
    //     Route::post('me', 'Api\Auth\AdminAuthController@me');
    // });
 

    //Old
    Route::post('send-sms',     'Api\Helper\SMSHelperController@resendSMSVerificationPin');
    Route::post('verify-phone', 'Api\Helper\SMSHelperController@resendSMSVerificationPin');



    Route::get('get-location', 'Api\Location\LocationController@getLocations');    Route::OPTIONS('get-location', 'Api\Location\LocationController@getLocations');

    //will secure with JWT Auth
    // Unanimous
    Route::post('/updateSignedOutStatus',   'Api\Helper\UnanimousHelperController@updateSignedOut');    Route::OPTIONS('/updateSignedOutStatus', 'Api\Helper\UnanimousHelperController@updateSignedOut');
    Route::get('get-available-promotions',  'Api\Helper\UnanimousHelperController@getAvailablePromotions');    Route::OPTIONS('get-available-promotions', 'Api\Helper\UnanimousHelperController@getAvailablePromotions');
    Route::get('get-customer-profile/{id?}',    'Api\Helper\UnanimousHelperController@getProfile');    Route::OPTIONS('get-customer-profile/{id?}', 'Api\Helper\UnanimousHelperController@getProfile');
    Route::get('get-personal-code',         'Api\Helper\UnanimousHelperController@getPersonalCode');    Route::OPTIONS('get-personal-code', 'Api\Helper\UnanimousHelperController@getPersonalCode');
    Route::get('get-orders',                'Api\Helper\UnanimousHelperController@getOrders');     Route::OPTIONS('get-orders', 'Api\Helper\UnanimousHelperController@getOrders');    


});