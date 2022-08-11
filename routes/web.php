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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
// Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index');
Route::get('/form', 'HomeController@form');
// Route::get('/school', 'FeeController@index');
// Route::get('/school', 'SchoolController@index');

//landing page route
Route::get('/', 'LandingPageController@index');
Route::get('/organization-list', 'LandingPageController@organizationList');
Route::get('/activity-list', 'LandingPageController@activitylist');
Route::get('/activity-details', 'LandingPageController@activitydetails');

//landing donation page route
Route::group(['prefix' => 'derma'], function () {
    Route::get('', 'LandingPageController@indexDonation');
    Route::get('/organization-list', 'LandingPageController@organizationListDonation');
    Route::get('/activity-list', 'LandingPageController@activitylistDonation');
    Route::get('/activity-details', 'LandingPageController@activitydetailsDonation');
    Route::get('/organization-type', 'LandingPageController@getOrganizationDatatable')->name('landingpage.donation.organization');
    Route::get('/organization-donation', 'LandingPageController@getDonationDatatable')->name('landingpage.donation.donation');
    Route::get('/organization-donation-custom', 'LandingPageController@customOrganizationTabbing')->name('landingpage.donation.custom');
    Route::get('/organization-donation-bytabbing', 'LandingPageController@getDonationByTabbing')->name('landingpage.donation.bytabbing');
    Route::get('/organization-donation-header', 'LandingPageController@getHeaderPoster')->name('landingpage.donation.header');
});

//landing fees page route
Route::group(['prefix' => 'yuran'], function () {
    Route::get('', 'LandingPageController@indexFees');
});
// feedback
Route::post('feedback', 'LandingPageController@storeMessage')->name('feedback.store');


Route::resource('school', 'SchoolController');

Route::get('getdetails/{id}', 'DetailsController@getFees')->name('details.getfees');

Route::post('parent/fetchClass', 'ParentController@fetchClass')->name('parent.fetchClass');
Route::post('parent/fetchStd', 'ParentController@fetchStd')->name('parent.fetchStd');

Route::group(['middleware' => ['auth'], 'prefix' => 'donate'], function () {
    Route::get('', 'DonationController@indexDerma')->name('donate.index');
    Route::get('donor/{id}', 'DonationController@listAllDonor')->name('donate.details');
    Route::get('donor/list/datatable', 'DonationController@getDonorDatatable')->name('donate.donor_datatable');
    Route::get('organizationList', 'DonationController@getDonationByOrganizationDatatable')->name('donate.donation_list');
    Route::get('history', 'DonationController@historyDonor')->name('donate.donor_history');
    Route::get('historyDT', 'DonationController@getHistoryDonorDT')->name('donate.donor_history_datatable');
});

Route::get('sumbangan/{link}', 'DonationController@urlDonation')->name('URLdonate');
Route::get('sumbangan_lhdn/{link}', 'DonationController@lhdndonate')->name('LHDNdonate');
Route::get('sumbangan_anonymous/{link}', 'DonationController@anonymouIndex')->name('ANONdonate');

Route::group(['prefix' => 'organization'], function () {
    Route::get('list', 'OrganizationController@getOrganizationDatatable')->name('organization.getOrganizationDatatable');
    Route::get('all', 'OrganizationController@getAllOrganization')->name('organization.getAll');
    Route::post('get-district', 'OrganizationController@getDistrict')->name('organization.get-district');
    Route::get('testRepeater', 'OrganizationController@testRepeater');
});

Route::group(['prefix' => 'teacher'], function () {
    Route::get('list', 'TeacherController@getTeacherDatatable')->name('teacher.getTeacherDatatable');
});

Route::group(['prefix' => 'class'], function () {
    Route::get('list', 'ClassController@getClassesDatatable')->name('class.getClassesDatatable');
    Route::post('/fetchTeacher', 'ClassController@fetchTeacher')->name('class.fetchTeacher');
});

Route::group(['prefix' => 'student'], function () {
    Route::get('list', 'StudentController@getStudentDatatable')->name('student.getStudentDatatable');
    Route::post('student/fetchClass', 'StudentController@fetchClass')->name('student.fetchClass');
});

Route::group(['prefix' => 'fees'], function () {
    Route::post('/year', 'FeesController@fetchYear')->name('fees.fetchYear');
    Route::post('/class', 'FeesController@fetchClass')->name('fees.fetchClass');

    Route::get('/classyear', 'FeesController@fetchClassYear')->name('fees.fetchClassYear');
    Route::post('/classCateYuran', 'FeesController@fetchClassForCateYuran')->name('fees.fetchClassForCateYuran');
    Route::get('/list-debtDatatable', 'FeesController@studentDebtDatatable')->name('fees.debtDatatable');

    Route::get('/list', 'FeesController@getTypeDatatable')->name('fees.getTypeDatatable');
    Route::get('/listparent', 'FeesController@getParentDatatable')->name('fees.getParentDatatable');
    Route::get('/report', 'FeesController@feesReport')->name('fees.report');
    Route::get('/reportByOid', 'FeesController@feesReportByOrganizationId')->name('fees.reportByOid');
    Route::get('/report/{type}/class/{class_id}', 'FeesController@reportByClass')->name('fees.reportByClass');

    Route::get('/A', 'FeesController@CategoryA')->name('fees.A');
    Route::get('/add/A', 'FeesController@createCategoryA')->name('fees.createA');
    Route::post('/store/A', 'FeesController@StoreCategoryA')->name('fees.storeA');

    Route::get('/B', 'FeesController@CategoryB')->name('fees.B');
    Route::get('/add/B', 'FeesController@createCategoryB')->name('fees.createB');
    Route::post('/store/B', 'FeesController@StoreCategoryB')->name('fees.storeB');

    Route::get('/C', 'FeesController@CategoryC')->name('fees.C');
    Route::get('/add/C', 'FeesController@createCategoryC')->name('fees.createC');
    Route::post('/store/C', 'FeesController@StoreCategoryC')->name('fees.storeC');

    Route::get('/dependent_fees', 'FeesController@dependent_fees')->name('dependent_fees');
    Route::get('/pay', 'PayController@pay')->name('pay');

    Route::get('/categoryDT', 'FeesController@getCategoryDatatable')->name('fees.getCategoryDatatable');
    Route::get('/student', 'FeesController@getstudentDatatable')->name('fees.getstudentDatatable');
    Route::get('/studentfees', 'FeesController@student_fees')->name('fees.studentfees');
    Route::get('/dependent', 'FeesController@parent_dependent')->name('fees.parent_dependent');

    Route::get('/search-report', 'FeesController@searchreport')->name('fees.searchreport');
    Route::get('/list-student', 'StudentController@getStudentDatatableFees')->name('fees.getStudentDatatableFees');
    Route::get('/download-PDF', 'StudentController@generatePDFByClass')->name('fees.generatePDFByClass');

    Route::get('/history', 'ParentController@indexParentFeesHistory')->name('parent.fees.history');
    Route::get('/list-receipt', 'FeesController@getFeesReceiptDataTable')->name('fees.getFeesReceiptDataTable');
    
    Route::get('/category/report', 'FeesController@cetegoryReportIndex')->name('fees.category.report');
    Route::post('/list-fetchYuran', 'FeesController@fetchYuran')->name('fees.fetchYuran');
});

Route::group(['prefix' => 'parent'], function () {
    Route::get('list', 'ParentController@getParentDatatable')->name('parent.getParentDatatable');
    Route::get('dependent', 'ParentController@indexDependent')->name('parent.dependent');
    Route::post('dependent', 'ParentController@storeDependent')->name('parent.storeDependent');
    Route::delete('dependent/{id}', 'ParentController@deleteDependent')->name('parent.deleteDependent');
});

Route::group(['prefix' => 'dependent'], function () {
    Route::get('list', 'ParentController@getDependentDataTable')->name('parent.getDependentDataTable');
});

Route::group(['prefix' => 'category'], function () {
    Route::post('details', 'CategoryController@getDetails')->name('category.getDetails');
    Route::get('list', 'CategoryController@getCategoryDatatable')->name('category.getCategoryDatatable');
    Route::get('/{id}/getDetails', 'CategoryController@getCategoryDetails')->name('category.getCategoryDetails');
    Route::get('getDetailsDT', 'CategoryController@getCategoryDetailsDatatable')->name('category.getCategoryDetailsDatatable');
});

Route::group(['prefix' => 'activity'], function () {
    Route::get('list', 'ActivityController@getActivityDatatable')->name('ActivityDT');
});

Route::group(['prefix' => 'reminder'], function () {
    Route::get('list', 'ReminderController@getReminderDatatable')->name('reminder.getReminder');
    Route::get('testing', 'ReminderController@testingEloquent');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'profile'], function () {
    Route::get('/resetPwd', 'ProfileController@showChangePwd')->name('profile.resetPassword');
    Route::post('/updatePwd/{id}', 'ProfileController@updatePwd')->name('profile.updatePwd');
});

Route::group(['middleware' => ['auth']], function () {
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
        'donation'           => 'DonationController',
        'reminder'           => 'ReminderController',
        'activity'           => 'ActivityController',
        'session'            => 'SessionController',
        'profile'            => 'ProfileController'
    ]);
});

Route::get('paydonate', 'PayController@donateindex')->name('paydonate');
Route::get('donateFromMobile', 'PayController@donateFromMobile');
Route::post('trn', 'PayController@transaction')->name('trn');
Route::post('trn-dev', 'PayController@transactionDev')->name('trn-dev');
Route::post('payment', 'PayController@paymentProcess')->name('payment');
Route::post('fpxIndex', 'PayController@fpxIndex')->name('fpxIndex');
Route::post('paymentStatus', 'PayController@paymentStatus')->name('paymentStatus');
Route::post('transactionReceipt', 'PayController@transactionReceipt')->name('transactionReceipt');
Route::get('successpay', 'PayController@successPay')->name('successpay');
Route::get('billIndex', 'PayController@billIndex')->name('billIndex');
Route::get('feespay', 'PayController@fees_pay')->name('feespay');
Route::get('receiptfee', 'PayController@viewReceipt')->name('receiptfee');
Route::get('viewfeereceipt/{transaction_id}', 'PayController@viewReceiptFees')->name('receipttest');

Route::get('feesparent', 'FeesController@parentpay')->name('parentpay');

Route::get('feespaydev', 'PayController@dev_fees_pay')->name('feespaydev');
Route::get('feesparentdev', 'FeesController@devpay')->name('feesparentdev');
Route::post('receiptdev', 'FeesController@devreceipt')->name('receiptdev');

Route::post('/exportteacher', 'TeacherController@teacherexport')->name('exportteacher');
Route::post('/importteacher', 'TeacherController@teacherimport')->name('importteacher');

Route::post('/exportclass', 'ClassController@classexport')->name('exportclass');
Route::post('/importclass', 'ClassController@classimport')->name('importclass');

Route::post('/exportstudent', 'StudentController@studentexport')->name('exportstudent');
Route::post('/importstudent', 'StudentController@studentimport')->name('importstudent');

Route::post('/importparent', 'ParentController@parentImport')->name('importparent');

Route::get('chat-user', 'MessageController@chatUser')->name('chat-user');
Route::get('chat-page/{friendId}', 'MessageController@chatPage')->name('chat-page');
Route::get('get-file/{filename}', 'MessageController@getFile')->name('get-file');
Route::post('send-message', 'MessageController@sendMessage')->name('send-message');


Route::group(['prefix' => 'notification'], function () {
    Route::get('/', 'HomeController@showNotification')->name('index.notification');
    Route::post('/save-token', [App\Http\Controllers\HomeController::class, 'saveToken'])->name('save-token');
    Route::post('/send-notification', [App\Http\Controllers\HomeController::class, 'sendNotification'])->name('send.notification');
});

// Route::get('/offline', 'HomeController@pwaOffline');

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/totalDonation', 'DashboardController@getTotalDonation')->name('dashboard.totalDonation');
    Route::get('/totalDonor', 'DashboardController@getTotalDonor')->name('dashboard.totalDonor');
    Route::get('/latestTransaction', 'DashboardController@getLatestTransaction')->name('dashboard.latest_transaction');
    Route::get('/getTransaction', 'DashboardController@getTransactionByOrganizationIdAndStatus')->name('dashboard.get_transaction');
});

Route::group(['prefix' => 'fpx'], function () {
    Route::get('/getBankList', 'FPXController@getBankList')->name('fpx.bank_list');
});

Route::get('/receipt', 'PayController@showReceipt');

Route::get('list', 'LandingPageController@getDonationDatatable')->name('landing-page.getOrganizationDatatable');

Route::group(['prefix' => 'session'], function () {
    Route::get('session/get', 'SessionController@accessSessionData')->name('getsession');
    Route::get('session/set', 'SessionController@storeSessionData')->name('setsession');
    Route::get('session/remove', 'SessionController@deleteSessionData');
});

Route::group(['prefix' => 'polimas'], function () {
    Route::get('/', 'PolimasController@indexLogin');
    Route::group(['middleware' => ['auth']], function () {
        Route::get('/batch', 'PolimasController@indexBatch')->name('polimas.batch');
        Route::get('/batch-list', 'PolimasController@getBatchDataTable')->name('polimas.batch.getBatchDataTable');
        Route::get('/student', 'PolimasController@indexStudent')->name('polimas.student');
        Route::get('/student-list', 'PolimasController@getStudentDatatable')->name('polimas.student.getStudentDatatable');
        Route::get('/studentfees', 'PolimasController@student_fees')->name('polimas.studentfees');
        Route::post('/allexportstudent', 'PolimasController@AllStudentExport')->name('polimas.allstudentexport');
        Route::post('/exportstudent', 'PolimasController@StudentExport')->name('polimas.studentexport');
    });
});
