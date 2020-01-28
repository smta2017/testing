<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin', 'AdminController@index');
Route::resource('/customer', 'CustomerController');


Route::get('/login/admin', 'Auth\LoginController@showAdminLoginForm');
Route::post('/login/admin', 'Auth\LoginController@adminLogin');

Route::get('/register/admin', 'Auth\RegisterController@showAdminRegisterForm');
Route::post('/register/admin', 'Auth\RegisterController@createAdmin');

// --------------------------------------------------------------------------
Route::get('/login/customer', 'Auth\LoginController@showCustomerLoginForm');
Route::post('/login/customer', 'Auth\LoginController@customerLogin');

// Route::get('/register/customer', 'Auth\RegisterController@showCustomerRegisterForm');
// Route::post('/register/customer', 'Auth\RegisterController@createCustomer');

// --------------------------------------------------------------------------

// Admin & Facility
Route::resource('/orders', 'OrderController');
Route::post('/service-step1', 'OrderController@servicestep1');

//sortung
Route::get('/service-step2', 'OrderController@getServicestep2');
Route::post('/add-product-item', 'OrderController@addProductOrderItem');
Route::post('/confirm-pickup-sort', 'OrderController@confirmPickupSort');
Route::post('/confirm-item', 'OrderController@confirmitem');
Route::post('/confirm-delifery-sort', 'OrderController@confirmDeliverySort');
Route::post('/reset-pickup-sort', 'OrderController@resetSort');

// delivery
Route::get('/service-step3', 'OrderController@getServicestep3');


Route::get('/from-agent', 'FacilityController@fromAgent');
Route::get('/find-by-customer', 'FacilityController@orderByCustomer');








