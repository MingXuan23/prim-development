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
Route::post('directpayIndex', 'DirectpayController@directpayIndex')->name('api.directpayIndex');

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
    Route::get('sendNotification/{id}/{title}/{message}', 'ScheduleApiController@sendNotification');
    Route::get('isNoti/{id}', 'ScheduleApiController@isNoti');
    Route::get('getSchedule/{id}', 'ScheduleApiController@getSchedule');
    Route::get('getTeacherInfo/{id}', 'ScheduleApiController@getTeacherInfo');
    Route::any('submitLeave', 'ScheduleApiController@submitLeave');
    
    Route::get('getLeaveType', 'ScheduleApiController@getLeaveType');
    Route::post('getPendingRelief','ScheduleApiController@getPendingRelief');

    Route::post('submitReliefResponse','ScheduleApiController@submitReliefResponse');
    Route::get('getHistory/{id}','ScheduleApiController@getHistory');

    
});

//route for mobile orderS
Route::group(['prefix' => 'OrderS'], function (){
    Route::get('test', 'OrderSController@testData');
    Route::post('login', 'OrderSController@login');
    Route::post('isUserOrderSAdmin', 'OrderSController@isUserOrderSAdmin');
    Route::post('logout', 'OrderSController@logout');
    Route::post('updateUser', 'OrderSController@updateUser');
    Route::post('updateOrganization', 'OrderSController@updateOrganization');
    Route::get('randomDishes', 'OrderSController@randomDishes');
    Route::get('listDishes', 'OrderSController@listDishes');
    Route::get('listShops', 'OrderSController@listShops');
    Route::post('listDishesByShop', 'OrderSController@listDishesByShop');
    Route::post('listDishesByShopAdmin', 'OrderSController@listDishesByShopAdmin');
    Route::post('listDishAvailable', 'OrderSController@listDishAvailable');
    Route::post('listOrderAvailable', 'OrderSController@listOrderAvailable');
    Route::post('getOrderCart', 'OrderSController@getOrderCart');
    Route::post('createOrderCart', 'OrderSController@createOrderCart');
    Route::post('getOrderAvailableDish', 'OrderSController@getOrderAvailableDish');
    Route::get('getDishType', 'OrderSController@getDishType');
    Route::post('addDishes', 'OrderSController@addDishes');
    Route::post('updateDishes', 'OrderSController@updateDishes');
    Route::post('deleteDishes', 'OrderSController@deleteDishes');
    Route::post('addOrderAvailable', 'OrderSController@addOrderAvailable');
    Route::post('updateOrderAvailable', 'OrderSController@updateOrderAvailable');
    Route::post('deleteOrderAvailable', 'OrderSController@deleteOrderAvailable');
    Route::post('listOrderAvailableAdmin', 'OrderSController@listOrderAvailableAdmin');
    Route::post('listOADAdmin', 'OrderSController@listOADAdmin');
    Route::post('updateOADAdmin', 'OrderSController@updateOADAdmin');
    Route::post('getUsers', 'OrderSController@getUsers');
    Route::post('getReport', 'OrderSController@getReport');
});