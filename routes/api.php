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
    // Route::group([ 'prefix' => 'customer'], function ($router) {
        Route::post('login',    'Api\Customer\Auth\JwtAuthController@login');
        Route::post('register', 'Api\Customer\Auth\JwtAuthController@register');
        Route::post('logout',   'Api\Customer\Auth\JwtAuthController@logout');
        Route::post('refresh',  'Api\Customer\Auth\JwtAuthController@refresh');
        Route::post('me',       'Api\Customer\Auth\JwtAuthController@me');
        Route::post('/password/email','Api\Customer\Auth\JwtAuthController@forgotPassword');
        Route::get('/password/reset/{token}','Api\Customer\Auth\JwtAuthController@resetPassword');
    // });

    // Admin
    // Route::group([ 'prefix' => 'admin'], function ($router) {
    //     Route::post('login', 'Api\Auth\AdminAuthController@login');
    //     Route::post('logout', 'Api\Auth\AdminAuthController@logout');
    //     Route::post('refresh', 'Api\Auth\AdminAuthController@refresh');
    //     Route::post('me', 'Api\Auth\AdminAuthController@me');
    // });

    //Geniral - Old
    Route::post('send-sms',                     'Api\Helper\SMSHelperController@resendSMSVerificationPin');
    Route::post('verify-phone',                 'Api\Helper\SMSHelperController@resendSMSVerificationPin');


    Route::group(['middleware' => ['JwtClientAuth']], function() {
        // Locations
        Route::get('/get-location',              'Api\Location\LocationController@getLocations');
        Route::post('/notify-location',          'Api\Location\LocationController@notifyLocation');
        
        // Orders
        Route::post('/place-order',              'Api\Order\OrderController@store');
        Route::get('/get-order-details',         'Api\Order\OrderController@show');
        Route::post('/cancel-order',             'Api\Order\OrderController@cancelOrder');
        Route::post('/rate-order',               'Api\Order\OrderController@rateOrder');
        Route::post('/reschedule-order',         'Api\Order\OrderController@reschedule');

        // Addresses
        Route::post('/add-customer-address',     'Api\Address\CustomerAddressController@store');
        Route::post('/mark-default-address',     'Api\Address\CustomerAddressController@markDefaultAddress');
        Route::post('/delete-customer-address',  'Api\Address\CustomerAddressController@destroy');
        Route::post('/update-customer-profile',  'Api\Address\CustomerAddressController@updateProfile');
        
        // Time slots
        Route::get('/get-time-slots',            'Api\TimeStlot\TimeSlotController@getAvailableSlots');
        
        // Services
        Route::get('/get-services',              'Api\Service\ServiceController@show');
        Route::get('/get-services-names',        'Api\Service\ServiceController@index');
        
        
        // Preference
        Route::post('/set-preference',            'Api\Preference\PreferenceController@setPreference');

        // Unanimous controller secify
        Route::post('/updateSignedOutStatus',    'Api\Helper\UnanimousHelperController@updateSignedOut');
        Route::get('/get-available-promotions',  'Api\Helper\UnanimousHelperController@getAvailablePromotions');
        Route::get('/get-customer-profile/{id?}','Api\Helper\UnanimousHelperController@getCustomerProfile');
        Route::get('/get-orders',                'Api\Helper\UnanimousHelperController@getCustomerOrders');
        Route::get('/get-personal-code',         'Api\Helper\UnanimousHelperController@getPersonalCode');
    });

    // will remove in future
    Route::OPTIONS('/add-customer-address',      'Api\Address\CustomerAddressController@store');
    Route::OPTIONS('/get-location',              'Api\Location\LocationController@getLocations');
    Route::OPTIONS('/updateSignedOutStatus',     'Api\Helper\UnanimousHelperController@updateSignedOut');
    Route::OPTIONS('/get-available-promotions',  'Api\Helper\UnanimousHelperController@getAvailablePromotions');
    Route::OPTIONS('/get-customer-profile/{id?}','Api\Helper\UnanimousHelperController@getCustomerProfile');
    Route::OPTIONS('/get-personal-code',         'Api\Helper\UnanimousHelperController@getPersonalCode');
    Route::OPTIONS('/get-orders',                'Api\Helper\UnanimousHelperController@getCustomerOrders');
    Route::OPTIONS('/get-time-slots',            'Api\TimeStlot\TimeSlotController@getAvailableSlots'); 
    Route::OPTIONS('/place-order',               'Api\Order\OrderController@store');

    Route::OPTIONS('/get-services',              'Api\Order\OrderController@index');
    Route::OPTIONS('/get-services-names',        'Api\Order\OrderController@index');
    Route::OPTIONS('/delete-customer-address',   'Api\Order\OrderController@index');
    Route::OPTIONS('/cancel-order',              'Api\Order\OrderController@index');
    Route::OPTIONS('/get-order-details',         'Api\Order\OrderController@index');
    Route::OPTIONS('/get-services-names',        'Api\Order\OrderController@index');
    Route::OPTIONS('/cancel-order',              'Api\Order\OrderController@index');
    Route::OPTIONS('/mark-default-address',      'Api\Order\OrderController@index');
    Route::OPTIONS('/set-preference',            'Api\Order\OrderController@index');
    Route::OPTIONS('/rate-order',                'Api\Order\OrderController@index');
    Route::OPTIONS('/delete-customer-address',   'Api\Order\OrderController@index');
    Route::OPTIONS('/update-customer-profile',   'Api\Order\OrderController@index');

});