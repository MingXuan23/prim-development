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

Route::group(['prefix' => 'mobile'], function () {
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
    
    //route for mobile dish
    Route::group(['prefix' => 'dish'], function (){
        Route::get('/getOrganizationWithDish', 'DishController@getOrganizationWithDish');
        Route::get('/getAllDishes', 'DishController@getAllDishes');
        Route::get('/getAllAvailableDates', 'DishController@getAllAvailableDates');
        Route::post('/storeDishAvailable', 'DishController@storeDishAvailable');
    });

    //route for mobile order
    Route::group(['prefix' => 'order'], function (){
        Route::get('/getAllOrderById/{id}', 'OrderController@getAllOrderById');
        Route::get('/getAllOrderByOrganId/{id}', 'OrderController@getAllOrderByOrganId');

        Route::post('/orderTransaction', 'OrderController@orderTransaction');
        Route::post('/updateStatusToDelivering', 'OrderController@updateStatusToDelivering');
        Route::post('/updateStatusToDelivered', 'OrderController@updateStatusToDelivered');
    });

    //route for mobile order
    Route::group(['prefix' => 'yuran'], function (){
        Route::post('login', 'MobileAPI\YuranController@login');
        Route::get('getOrganizationByUserId', 'MobileAPI\YuranController@getOrganizationByUserId');
        Route::post('getReceiptByOid', 'MobileAPI\YuranController@getReceiptByOid');
        Route::post('getUserInfo', 'MobileAPI\YuranController@getUserInfo');

        Route::post('getYuran', 'MobileAPI\YuranController@getYuranByParentIdAndOrganId');
        Route::post('yuranTransaction', 'MobileAPI\YuranController@pay');
    });
});

Route::group(['prefix' => 'schedule' , 'namespace' => 'Schedule'],function () {
    Route::post('login', 'ScheduleApiController@login');
    Route::get('getTimeOff', 'ScheduleApiController@getTimeOff');
    Route::get('sendNotification/{id}', 'ScheduleApiController@sendNotification');
    Route::get('isNoti/{id}', 'ScheduleApiController@isNoti');
    
});