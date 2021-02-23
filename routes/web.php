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

use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/form', 'HomeController@form');
// Route::get('/school', 'FeeController@index');
// Route::get('/school', 'SchoolController@index');

Route::resource('school', 'SchoolController');

Route::get('getdetails/{id}', 'DetailsController@getFees')->name('details.getfees');

Route::post('parent/fetchClass', 'ParentController@fetchClass')->name('parent.fetchClass');
Route::post('parent/fetchStd', 'ParentController@fetchStd')->name('parent.fetchStd');

Route::get('donationlist', 'DonationController@indexDerma')->name('donate.organizationlist');
Route::get('organizationList', 'DonationController@getDonationByOrganizationDatatable')->name('donate.donationlist');
Route::get('urusDermaList', 'DonationController@indexUrusDerma')->name('donate.urusDermaList');

Route::group(['prefix' => 'donate'], function () {
    
});

// Route::get('{id}',['uses'=>'FeesDetailsController@getFees']);
Route::resources([
    'school'             => 'SchoolController',
    'teacher'            => 'TeacherController',
    'class'              => 'ClassController',
    'student'            => 'StudentController',
    'category'           => 'CategoryController',
    'fees'               => 'FeesController',
    'details'            => 'DetailsController',
    'jaim'               => 'UserJaimController',
    'parent'             => 'ParentController',
    'pay'                => 'PayController',
    'organization'       => 'OrganizationController',
    'donate'             => 'DonationController'
]);

Route::post('payment', 'PayController@paymentProcess')->name('payment');
Route::post('fpxIndex', 'PayController@fpxIndex')->name('fpxIndex');
Route::get('successpay', 'PayController@successPay')->name('successpay');

Route::get('/exportteacher', 'TeacherController@teacherexport')->name('exportteacher');
Route::post('/importteacher', 'TeacherController@teacherimport')->name('importteacher');

Route::get('/exportclass', 'ClassController@classexport')->name('exportclass');
Route::post('/importclass', 'ClassController@classimport')->name('importclass');

Route::get('/exportstudent', 'StudentController@studentexport')->name('exportstudent');
Route::post('/importstudent', 'StudentController@studentimport')->name('importstudent');

Route::get('chat-user', 'MessageController@chatUser')->name('chat-user');
Route::get('chat-page/{friendId}', 'MessageController@chatPage')->name('chat-page');
Route::get('get-file/{filename}', 'MessageController@getFile')->name('get-file');
Route::post('send-message', 'MessageController@sendMessage')->name('send-message');
