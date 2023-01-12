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
Route::get('sumbangan_anonymous/{link}', 'DonationController@anonymouIndex')->name('ANONdonate');

Route::group(['prefix' => 'organization'], function () {
    Route::get('list', 'OrganizationController@getOrganizationDatatable')->name('organization.getOrganizationDatatable');
    Route::get('all', 'OrganizationController@getAllOrganization')->name('organization.getAll');
    Route::post('get-district', 'OrganizationController@getDistrict')->name('organization.get-district');
    Route::get('testRepeater', 'OrganizationController@testRepeater');
    Route::post('parent-koop', 'OrganizationController@fetchAvailableParentKoop')->name('organization.fetchAvailableParentKoop');
    Route::get('/edit-merchant/{id}', 'Merchant\AdminRegular\DashboardController@edit')->name('admin-reg.edit-merchant'); // edit for merchant
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

    Route::post('/list-fetchYuranbyOrganId', 'FeesController@fecthYuranByOrganizationId')->name('fees.fetchYuranByOrganId');
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

Route::group(['middleware' => ['auth'], 'prefix' => 'koperasi', 'namespace' => 'Cooperative'], function() {
    Route::group(['namespace' => 'User'], function() {
        // Koop School
        Route::delete('/{org_id}/edit/{id}', 'UserCooperativeController@destroyItemCart')->name('koperasi.destroyItemCart');
        Route::post('/koperasi/fetchKoop', 'UserCooperativeController@fetchKoop')->name('koperasi.fetchKoop');
        Route::get('/order', 'UserCooperativeController@indexOrder')->name('koperasi.order');
        Route::get('/order/fetchDay', 'UserCooperativeController@fetchAvailableDay')->name('koperasi.fetchDay');
        Route::post('/order/update-pick-up-date', 'UserCooperativeController@updatePickUpDate')->name('koperasi.updatePickUpDate');
        Route::delete('/order/{id}', 'UserCooperativeController@destroyUserOrder')->name('koperasi.destroyUserOrder');
        Route::get('/history', 'UserCooperativeController@indexHistory')->name('koperasi.history');
        Route::get('/{id}/list', 'UserCooperativeController@indexList')->name('koperasi.list');
        
        // Koop_Shop
        Route::get('/koop','UserCooperativeController@indexKoop')->name('koperasi.indexKoop');
        Route::get('/koop/{id}','UserCooperativeController@koopShop')->name('koperasi.koopShop');
        Route::get('/koop/store/{id}','UserCooperativeController@storeKoop')->name('koperasi.storeKoop');
        Route::get('/koop/{id}/cart','UserCooperativeController@koopCart')->name('koperasi.koopCart');
    });

    Route::group(['namespace' => 'Admin'], function() {
        Route::get('/produkmenu','AdminProductCooperativeController@productMenu')->name('koperasi.productMenu');
        Route::get('/produkmenu/delete/{id}','AdminProductCooperativeController@deleteType')->name('koperasi.deleteType');

        Route::get('/produktype','AdminProductCooperativeController@createType')->name('koperasi.addtype');
        Route::post('/produktype','AdminProductCooperativeController@storeType')->name('koperasi.storeType');

        Route::get('/admin','AdminProductCooperativeController@indexAdmin')->name('koperasi.indexAdmin');
        Route::get('/produk','AdminProductCooperativeController@createProduct')->name('koperasi.createProduct');
        Route::post('/produk','AdminProductCooperativeController@storeProduct')->name('koperasi.storeProduct');
        Route::get('/produk/update/{id}','AdminProductCooperativeController@editProduct')->name('koperasi.editProduct');
        Route::post('/produk/update/{id}','AdminProductCooperativeController@updateProduct')->name('koperasi.updateProduct');
        Route::get('/produk/delete/{id}','AdminProductCooperativeController@deleteProduct')->name('koperasi.deleteProduct');

        Route::get('/openingHours','AdminOpeningHoursCooperativeController@indexOpening')->name('koperasi.indexOpening');
        Route::post('/openingHours','AdminOpeningHoursCooperativeController@storeOpening')->name('koperasi.storeOpening');

        Route::get('/Confirm','AdminOrderCooperativeController@indexConfirm')->name('koperasi.indexConfirm');
        Route::get('/Confirm/update/{id}','AdminOrderCooperativeController@storeConfirm')->name('koperasi.storeConfirm');
        Route::get('/Confirm/delete/{id}','AdminOrderCooperativeController@notConfirm')->name('koperasi.notConfirm');
    });
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Merchant'], function() {
    Route::group(['prefix' => 'merchant', 'namespace' => 'Regular'], function() {
        // Index
        Route::get('', 'LandingPageController@index')->name('merchant-reg.index');
        Route::get('fetch-merchant', 'LandingPageController@test_index')->name('merchant.fetch-merchant');
        // Get all cart
        Route::get('get-all-cart-items', 'OrderController@getAllItemsInCart')->name('merchant-reg.get-all-items');
        // Orders
        Route::get('all-orders', 'HistoryController@index')->name('merchant.all-orders');
        Route::get('get-all-orders', 'HistoryController@getAllOrder')->name('merchant.get-all-orders');
        Route::delete('delete-order', 'HistoryController@deletePaidOrder')->name('merchant.delete-order');
        Route::get('all-orders/history', 'HistoryController@history')->name('merchant.order-history');
        Route::get('get-order-history', 'HistoryController@getOrderHistory')->name('merchant.get-order-history');
        Route::get('{order_id}/order-details', 'HistoryController@showOrderDetail')->name('merchant.order-detail');
        // Menu
        Route::get('{id}', 'OrderController@index')->name('merchant-reg.show');
        Route::post('get-counter', 'OrderController@countItemsInCart')->name('merchant-reg.count-cart');
        Route::post('fetch-item', 'OrderController@fetchItem')->name('merchant-reg.fetch-item');
        Route::post('store-item', 'OrderController@storeItemInCart')->name('merchant-reg.store-item');
        // Cart & Pay
        Route::get('{id}/cart', 'OrderController@showCart')->name('merchant-reg.cart');
        Route::delete('destroy-item', 'OrderController@destroyItemInCart')->name('merchant-reg.destroy-item');
        Route::post('fetch-disabled-dates', 'OrderController@fetchDisabledDates')->name('merchant-reg.disabled-dates');
        Route::post('fetch-hours', 'OrderController@fetchOperationHours')->name('merchant-reg.fetch-hours');
        Route::post('{org_id}/cart/{order_id}/payment', 'OrderController@store')->name('merchant-reg.store-order');
    });

    Route::group(['prefix' => 'admin-regular', 'namespace' => 'AdminRegular'], function() {
        # Main Dashboard
        Route::get('/home', 'DashboardController@index')->name('admin-reg.home');
        Route::get('get-latest-orders', 'DashboardController@getLatestOrdersByNow')->name('admin-reg.latest-orders');
        Route::get('get-total-order', 'DashboardController@getTotalOrder')->name('admin-reg.total-order');
        Route::get('get-total-income', 'DashboardController@getTotalIncome')->name('admin-reg.total-income');
        Route::get('get-all-transaction', 'DashboardController@getAllTransaction')->name('admin-reg.all-transaction');
        Route::put('update-merchant', 'DashboardController@update')->name('admin-reg.update-merchant');

        # Operation hour Dashboard
        Route::get('/operation-hours', 'OperationHourController@index')->name('admin-reg.operation-hour');
        Route::get('get-operation-hours', 'OperationHourController@getOperationHoursTable')->name('admin-reg.get-oh');
        Route::post('edit-hour', 'OperationHourController@edit')->name('admin-reg.edit-hour');
        Route::put('update-hour', 'OperationHourController@update')->name('admin-reg.update-hour');
        Route::get('/operation-hours/{id}/check-orders/{hour_id}', 'OperationHourController@editSameDateOrders')->name('admin-reg.edit-same-order');
        Route::post('change-pickup-date', 'OperationHourController@changeOrderPickupDate')->name('admin-reg.change-date');
        Route::put('new-update-hour', 'OperationHourController@updateNew')->name('admin-reg.new-update-hour');
        
        # Product Dashboard
        Route::get('/p-group-list', 'ProductController@indexProductGroup')->name('admin-reg.product-group');
        Route::get('get-group', 'ProductController@getAllProductGroup')->name('admin-reg.get-group');
        Route::post('store-group', 'ProductController@storeProductGroup')->name('admin-reg.store-group');
        Route::get('/p-group-list/{id}', 'ProductController@showProductItem')->name('admin-reg.product-item');
        Route::get('get-product-item', 'ProductController@getAllProductItem')->name('admin-reg.get-pi');
        Route::put('update-group', 'ProductController@updateProductGroup')->name('admin-reg.update-group');
        Route::delete('destroy-group', 'ProductController@destroyProductGroup')->name('admin-reg.destroy-group');
        Route::post('store-item', 'ProductController@storeProductItem')->name('admin-reg.store-item');
        Route::get('destroy-body', 'ProductController@displayDestroyItemBody')->name('admin-reg.destroy-body');
        Route::delete('destroy-item', 'ProductController@destroyProductItem')->name('admin-reg.destroy-item');
        Route::get('/p-group-list/{id}/edit/{item}', 'ProductController@editProductItem')->name('admin-reg.edit-item');
        Route::put('update-item', 'ProductController@updateProductItem')->name('admin-reg.update-item');
        
        # Order Dashboard
        Route::get('orders', 'OrderController@index')->name('admin-reg.orders');
        Route::get('all-orders', 'OrderController@getAllOrders')->name('admin-reg.all-orders');
        Route::get('count-orders', 'OrderController@countTotalOrders')->name('admin-reg.count-orders');
        Route::post('picked-up-order','OrderController@orderPickedUp')->name('admin-reg.order-picked-up');
        Route::delete('destroy-order', 'OrderController@destroy')->name('admin-reg.destroy-order');
        Route::get('orders/{id}/history', 'OrderController@showHistory')->name('admin-reg.history');
        Route::get('all-histories', 'OrderController@getAllHistories')->name('admin-reg.all-histories');

        # Order details
        Route::get('order-details/{order_id}', 'OrderController@showList')->name('admin-reg.order-detail');
    });
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
        'profile'            => 'ProfileController',
        'koperasi'           => 'Cooperative\User\UserCooperativeController',
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

Route::post('/exportAllYuranStatus', 'FeesController@ExportAllYuranStatus')->name('exportAllYuranStatus');


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
    // Route::get('/', 'PolimasController@indexLogin');
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

/* 
Route::group(['middleware' => ['auth'], 'prefix' => 'lhdn'], function () {
    Route::get('/', 'DonationController@indexLHDN')->name('lhdn.index');
    Route::get('/list/datatable', 'DonationController@getLHDNHistoryDatatable')->name('donate.lhdn_dataTable');
    Route::get('/lhdn-receipt/{id}', 'DonationController@getLHDNReceipt')->name('lhdn-receipt');
}); */