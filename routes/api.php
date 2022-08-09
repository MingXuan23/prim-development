<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('fpxIndex', 'PayController@fpxIndex')->name('api.fpxIndex');

Route::get('devtrans', 'PayController@devtrans')->name('devtrans');

// mobile api
Route::get('donateFromMobile', 'PayController@donateFromMobile');

Route::group(['prefix' => 'moblie'], function () {
    Route::get('/getAllDonation', 'MobileApiController@getAllDonation');
    Route::get('/getAllDonationType', 'MobileApiController@getAllDonationType');
    Route::get('/getAllDonationQuantity', 'MobileApiController@getAllDonationQuantity');
    Route::get('/getAllDonationTypeQuantity', 'MobileApiController@getAllDonationTypeQuantity');
    Route::get('/getAllStatistic', 'MobileApiController@getAllStatistic');
    Route::get('/getallmytransactionhistory', 'MobileApiController@getallmytransactionhistory');
    Route::get('/getlatesttransaction', 'MobileApiController@getlatesttransaction');
    Route::get('/gettransactionbymonth', 'MobileApiController@gettransactionbymonth');
    Route::get('/gettransactionbyyear', 'MobileApiController@gettransactionbyyear');
    Route::get('/donationnumberbyorganization', 'MobileApiController@donationnumberbyorganization');
    Route::get('/getdonationbycategory', 'MobileApiController@getdonationbycategory');
    
    Route::post('/login', 'MobileApiController@login');
    Route::post('/updateProfile', 'MobileApiController@updateProfile');
    //route for mobile order
    Route::get('/dish/getOrganizationWithDish', 'DishController@getOrganizationWithDish');
    Route::get('/dish/getAllDishes', 'DishController@getAllDishes');
    Route::get('/dish/getAllAvailableDates', 'DishController@getAllAvailableDates');
    
    Route::post('/order/orderTransaction', 'OrderController@orderTransaction');
});