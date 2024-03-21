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
//mingxuan comment
// fei comments
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('registerAdmin', 'Auth\RegisterController@AdminRegisterIndex')->name('register.admin');
//Route::post('registerAdmin', 'Auth\RegisterController@registerAdmin');
// Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index');
Route::get('/form', 'HomeController@form');
// Route::get('/school', 'FeeController@index');
// Route::get('/school', 'SchoolController@index');

//landing page route
// Route::get('/', 'LandingPageController@index'); //maintenance page
// Route::get('/organization-list', 'LandingPageController@organizationList');
Route::get('/activity-list', 'LandingPageController@activitylist');
Route::get('/activity-details', 'LandingPageController@activitydetails');




//wan add
//landing page route
Route::group(['prefix' => ''], function () {
    Route::get('', 'LandingPageController@indexprim');
    //Route::get('/school-list', 'LandingPageController@getSchoolList')->name('landingpage.school');
});
//end wan add

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
    Route::get('/organization-list', 'LandingPageController@indexOrganizationList');
    Route::get('organization-all-list', 'LandingPageController@getAllOrganizationList')->name('landingpage.organization.list');
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

    Route::get('getReferralCode', 'DonationController@getReferralCode')->name('donate.getReferralCode');
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
    Route::get('listperanan', 'TeacherController@getPerananDatatable')->name('teacher.getPerananDatatable');
    Route::get('peranan', 'TeacherController@perananindex')->name('teacher.perananindex');
    Route::get('storeperanan', 'TeacherController@perananstore')->name('teacher.perananstore');
    Route::get('createperanan', 'TeacherController@peranancreate')->name('teacher.peranancreate');
    Route::get('editperanan/{id}', 'TeacherController@perananedit')->name('teacher.perananedit');
    Route::post('updateperanan/{id}', 'TeacherController@perananupdate')->name('teacher.perananupdate');
    Route::post('destroyperanan/{id}/{role_id}', 'TeacherController@peranandestroy')->name('teacher.peranandestroy');
});

// Relief System
Route::group(['prefix' => 'subject'], function () {
    Route::get('getSubjectDatatable', 'SubjectController@getSubjectDatatable')->name('subject.getSubjectDatatable');
    Route::delete('/destroysubject/{id}', 'SubjectController@subjectdestroy')->name('subject.subjectdestroy');
});
Route::post('importSubject', 'SubjectController@subjectImport')->name('importSubject');

Route::get('leaveReliefPolicy', 'Schedule\ScheduleController@leaveReliefPolicy')->name('schedule.leaveReliefPolicy');

Route::group(['prefix' => 'schedule','namespace' => 'Schedule','middleware' => ['auth']], function () {

    Route::get('getVersion/{oid}','ScheduleController@getVersion')->name('schedule.getVersion');

    Route::get('getScheduleStatus/{id}','ScheduleController@getScheduleStatus')->name('schedule.getScheduleStatus');
    Route::post('updateSchedule','ScheduleController@updateSchedule')->name('schedule.updateSchedule');
    Route::get('getScheduleView/{class_id}','ScheduleController@getScheduleView')->name('schedule.getScheduleView');
    
    Route::get('manageRelief','ScheduleController@manageReliefIndex')->name('schedule.manageRelief');
    Route::post('getAllTeacher','ScheduleController@getAllTeacher')->name('schedule.getAllTeacher');
    Route::post('datatablePendingRelief','ScheduleController@datatablePendingRelief')->name('schedule.datatablePendingRelief');


    Route::any('getFreeTeacher','ScheduleController@getFreeTeacher')->name('schedule.getFreeTeacher');
    Route::post('autoSuggestRelief','ScheduleController@autoSuggestRelief')->name('schedule.autoSuggestRelief');

    Route::get('reliefReport','ScheduleController@reliefReportIndex')->name('schedule.reliefReport');
    Route::post('getReliefReport','ScheduleController@getReliefReport')->name('schedule.getReliefReport');

    Route::post('saveRelief','ScheduleController@saveRelief')->name('schedule.saveRelief');
    Route::post('addTeacherLeave','ScheduleController@addTeacherLeave')->name('schedule.addTeacherLeave');
    Route::post('getTeacherOfOrg','ScheduleController@getTeacherOfOrg')->name('schedule.getTeacherOfOrg');

    Route::post('getTeacherSlot','ScheduleController@getTeacherSlot')->name('schedule.getTeacherSlot');
    Route::post('adminManageRelief','ScheduleController@adminManageRelief')->name('schedule.adminManageRelief');
});
Route::post('importSchedule', 'Schedule\ScheduleController@scheduleImport')->name('importSchedule');
Route::post('importScheduleSubject', 'Schedule\ScheduleController@scheduleSubjectImport')->name('importScheduleSubject');

Route::group(['prefix' => 'class'], function () {
    Route::get('list', 'ClassController@getClassesDatatable')->name('class.getClassesDatatable');
    Route::get('/getDummyClassStatus', 'ClassController@getDummyClassStatus')->name('class.getDummyClassStatus');
    Route::get('/dummyclass/{id}', 'ClassController@storeDummyClass')->name('class.storeDummyClass');
    Route::post('/fetchTeacher', 'ClassController@fetchTeacher')->name('class.fetchTeacher');
});

Route::group(['prefix' => 'student'], function () {
    Route::get('list', 'StudentController@getStudentDatatable')->name('student.getStudentDatatable');
    Route::get('compareAddNewStudent', 'StudentController@compareAddNewStudent')->name('student.compareAddNewStudent');
    Route::get('compareTransferStudent', 'StudentController@compareTransferStudent')->name('student.compareTransferStudent');
    Route::get('compareTransferStudentDiffOrg', 'StudentController@compareTransferStudentDiffOrg')->name('student.compareTransferStudentDiffOrg');

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
    Route::get('/reportByCid', 'FeesController@feesReportByClassId')->name('fees.feesReportByClassId');
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

    //wan add
    Route::get('/Recurring', 'FeesController@CategoryRecurring')->name('fees.Recurring');
    Route::get('/add/Recurring', 'FeesController@createCategoryRecurring')->name('fees.createRecurring');
    Route::post('/store/Recurring', 'FeesController@StoreCategoryRecurring')->name('fees.storeRecurring');

    Route::get('/dependent_fees', 'FeesController@dependent_fees')->name('dependent_fees');
    Route::get('/pay', 'PayController@pay')->name('pay');

    Route::get('/categoryDT', 'FeesController@getCategoryDatatable')->name('fees.getCategoryDatatable');
    Route::get('/student', 'FeesController@getstudentDatatable')->name('fees.getstudentDatatable');
    Route::get('/studentfees', 'FeesController@student_fees')->name('fees.studentfees');
    Route::get('/dependent', 'FeesController@parent_dependent')->name('fees.parent_dependent');

    Route::get('/search-report', 'FeesController@searchreport')->name('fees.searchreport');
    Route::get('/search-report-swasta', 'FeesController@searchreportswasta')->name('fees.searchreportswasta');
    Route::get('/list-student', 'StudentController@getStudentDatatableFees')->name('fees.getStudentDatatableFees');
    Route::get('/list-student-swasta', 'StudentController@getStudentSwastaDatatableFees')->name('fees.getStudentSwastaDatatableFees');
    Route::get('/download-PDF', 'StudentController@generatePDFByClass')->name('fees.generatePDFByClass');

    Route::get('/history', 'ParentController@indexParentFeesHistory')->name('parent.fees.history');
    Route::get('/list-receipt', 'FeesController@getFeesReceiptDataTable')->name('fees.getFeesReceiptDataTable');

    Route::get('/category/report', 'FeesController@cetegoryReportIndex')->name('fees.category.report');
    Route::get('/category/report-swasta', 'FeesController@cetegoryReportIndexSwasta')->name('fees.category.report_swasta');
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
        Route::delete('/{org_id}/edit', 'UserCooperativeController@destroyItemCart')->name('koperasi.destroyItemCart');
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
        Route::post('/koop/productsListByGroup','UserCooperativeController@productsListByGroup')->name('koperasi.productsListByGroup');
        Route::get('/koop/store/{id}','UserCooperativeController@storeKoop')->name('koperasi.storeKoop');
        Route::get('/koop/{id}/cart','UserCooperativeController@koopCart')->name('koperasi.koopCart');
        Route::post('/koop/{id}/fetchItemToModel','UserCooperativeController@fetchItemToModel')->name('koperasi.fetchItemToModel');
        Route::post('/koop/storeInCart','UserCooperativeController@storeInCart')->name('koperasi.storeInCart');
        Route::post('/checkCart','UserCooperativeController@checkCart')->name('koperasi.checkCart');

        //Route::get('/testPay','UserCooperativeController@testPay')->name('koperasi.testPay');
        //created for test action after payment only
        
        
    });

    Route::group(['namespace' => 'Admin'], function() {
        Route::get('/produkmenu','AdminProductCooperativeController@productMenu')->name('koperasi.productMenu');
        Route::get('/fetchprodukmenu/{koopId}','AdminProductCooperativeController@getProductMenuByOrgId')->name('koperasi.changeProductMenu');
        Route::get('/produkmenu/delete/{id}','AdminProductCooperativeController@deleteType')->name('koperasi.deleteType');
        Route::post('/produkmenu/deleteSelectedProducts','AdminProductCooperativeController@deleteSelectedProducts')->name('koperasi.deleteSelectedProducts');
        Route::get('/produkmenu/getProductList','AdminProductCooperativeController@getProductList')->name('koperasi.getProductList');
        Route::post('/produktype/getProductNumOfGroup','AdminProductCooperativeController@getProductNumOfGroup')->name('koperasi.getProductNumOfGroup');

        
        Route::any('/produktype','AdminProductCooperativeController@createType')->name('koperasi.addtype');
        Route::post('/produktype/add','AdminProductCooperativeController@storeType')->name('koperasi.storeType');
        Route::post('/produktype/update/{id}','AdminProductCooperativeController@updateType')->name('koperasi.updateType');
        Route::get('/produktype/edit/{id}','AdminProductCooperativeController@editType')->name('koperasi.editType');
        Route::post('/importproducttype', 'AdminProductCooperativeController@importproducttype')->name('importproducttype');
        Route::post('/importproduct', 'AdminProductCooperativeController@importproduct')->name('importproduct');

        Route::get('/admin','AdminProductCooperativeController@indexAdmin')->name('koperasi.indexAdmin');
        Route::post('/produk','AdminProductCooperativeController@createProduct')->name('koperasi.createProduct');
        Route::post('/storeproduk','AdminProductCooperativeController@storeProduct')->name('koperasi.storeProduct');
        Route::get('/produk/update/{id}','AdminProductCooperativeController@editProduct')->name('koperasi.editProduct');
        Route::post('/produk/update/{id}','AdminProductCooperativeController@updateProduct')->name('koperasi.updateProduct');
        Route::get('/produk/delete/{id}','AdminProductCooperativeController@deleteProduct')->name('koperasi.deleteProduct');


        Route::get('/openingHours','AdminOpeningHoursCooperativeController@indexOpening')->name('koperasi.indexOpening');
        Route::get('/openingChangeKoperasi','AdminOpeningHoursCooperativeController@openingChangeKoperasi')->name('koperasi.openingChangeKoperasi');
        Route::post('/openingHours','AdminOpeningHoursCooperativeController@storeOpening')->name('koperasi.storeOpening');

        Route::get('/Confirm','AdminOrderCooperativeController@indexConfirm')->name('koperasi.indexConfirm');
        Route::get('/fetchConfirmTable','AdminOrderCooperativeController@fetchConfirmTable')->name('koperasi.fetchConfirmTable');

        Route::get('/Confirm/update/{id}','AdminOrderCooperativeController@storeConfirm')->name('koperasi.storeConfirm');
        Route::get('/Confirm/delete/{id}','AdminOrderCooperativeController@notConfirm')->name('koperasi.notConfirm');

        Route::get('/returnProdukMenu/{page}','AdminProductCooperativeController@returnProdukMenu')->name('koperasi.return');
        Route::get('/fetchClassyear','AdminProductCooperativeController@fetchClassyear')->name('koperasi.fetchClassYear');

        Route::get('/{id}/{customerID}/list', 'AdminOrderCooperativeController@viewPgngList')->name('koperasi.viewPgngList');
        Route::get('returnFromList/{url}/{koopId}', 'AdminOrderCooperativeController@returnFromList')->name('koperasi.returnFromList');
        Route::get('/adminHistory', 'AdminOrderCooperativeController@adminHistory')->name('koperasi.adminHistory');
        Route::get('/fetchAdminHistory', 'AdminOrderCooperativeController@fetchAdminHistory')->name('koperasi.fetchAdminHistory');

        Route::get('/exportKoperasiOverview/{id}', 'AdminOrderCooperativeController@exportKoperasiOverview')->name('koperasi.exportKoperasiOverview');

    });
});

                                //// *** Get & Go ***//// 
Route::group(['namespace' => 'Merchant'], function() {
    Route::group(['prefix' => 'getngo', 'namespace' => 'Regular'], function() {
    Route::get('/product', 'ProductController@index')->name('merchant-product.index');
    Route::get('/product/{id}', 'ProductController@show')->name('merchant-product.show');
    }); 
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Merchant'], function() {
    Route::group(['prefix' => 'getngo', 'namespace' => 'Regular'], function() {
        // Index
        Route::get('', 'LandingPageController@index')->name('merchant-reg.index');
        Route::get('fetch-merchant', 'LandingPageController@test_index')->name('merchant.fetch-merchant');
        //Product
        Route::get('/cart','ProductController@showAllCart')->name('merchant.all-cart');//to show all products in cart
        Route::put('update-cart','ProductController@updateCart')->name('merchant.update-cart');//to update  a cart
        Route::get('load-cart-counter','ProductController@loadCartCounter')->name('merchant.load-cart-counter');
        Route::get('get-actual-total-price','ProductController@getTotalPrice')->name('merchant.get-actual-total-price');
        
        // Route::get('/testPayment','ProductController@testPayment')->name('merchant.testPayment');
         
         // Checkout
        Route::get('{id}/checkout', 'ProductController@checkOut')->name('merchant.checkout');
        Route::get('get-checkout-items', 'ProductController@getCheckoutItems')->name('merchant.get-checkout-items');
        // Get all cart
        Route::get('get-all-cart-items', 'OrderController@getAllItemsInCart')->name('merchant-reg.get-all-items');
        // Orders
        Route::get('all-orders', 'HistoryController@index')->name('merchant.all-orders');
        Route::get('get-all-orders', 'HistoryController@getAllOrder')->name('merchant.get-all-orders');
        Route::post('picked-up-order','HistoryController@orderPickedUp')->name('merchant.order-picked-up');
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

    Route::group(['prefix' => 'admin', 'namespace' => 'AdminRegular'], function() {
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
        Route::post('/importMerchantProduct', 'ProductController@importMerchantProduct')->name('importMerchantProduct');

        # Order Dashboard
        Route::get('orders', 'OrderController@index')->name('admin-reg.orders');
        Route::get('all-orders', 'OrderController@getAllOrders')->name('admin-reg.all-orders');
        Route::get('count-orders', 'OrderController@countTotalOrders')->name('admin-reg.count-orders');
        Route::post('picked-up-order','OrderController@orderPickedUp')->name('admin-reg.order-picked-up');
        Route::delete('destroy-order', 'OrderController@destroy')->name('admin-reg.destroy-order');
        Route::get('all-orders/{id}/history', 'OrderController@showHistory')->name('admin-reg.history');
        Route::get('all-histories', 'OrderController@getAllHistories')->name('admin-reg.all-histories');

        Route::put('update-service-charge/{id}','OrderController@updateServiceCharge' )->name('admin-reg.updateServiceCharge');
        # Report Dashboard
        Route::get('/sales-report', 'ReportController@index')->name('admin-reg.report');
        Route::get('get-report', 'ReportController@getReport')->name('admin-reg.get-report');
        Route::get('get-group-table', 'ReportController@getProductGroupTable')->name('admin-reg.group-table');
        
        Route::get('/sales-report/{g_id?}', 'ReportController@showProductItemReport')->name('admin-reg.item-report');
        Route::get('get-item-report', 'ReportController@getProductItemReport')->name('admin-reg.get-item-report');
        Route::get('get-item-table', 'ReportController@getProductItemTable')->name('admin-reg.item-table');

        # Order details
        Route::get('order-details/{order_id}', 'OrderController@showList')->name('admin-reg.order-detail');
    });
});
                                        //// ***End of Get & Go ***//// 


//private school
Route::group(['prefix' => 'private-school'], function () {
    Route::get('teacher', 'TeacherController@indexSwasta')->name('private-school.teacher.index');
    Route::get('teacher/create', 'TeacherController@createSwasta')->name('private-school.teacher.create');
    Route::post('teacher/store', 'TeacherController@storeSwasta')->name('private-school.teacher.store');

    Route::get('class', 'ClassController@indexSwasta')->name('private-school.class.index');
    Route::get('class/create', 'ClassController@createSwasta')->name('private-school.class.create');
    Route::post('class/store', 'ClassController@storeSwasta')->name('private-school.class.store');

    Route::get('student', 'StudentController@indexSwasta')->name('private-school.student.index');
    Route::get('student/create', 'StudentController@createSwasta')->name('private-school.student.create');
    Route::post('student/store', 'StudentController@storeSwasta')->name('private-school.student.store');
    
    Route::get('parent', 'ParentController@index')->name('private-school.parent.index');
    // Route::get('parent/create', 'ParentController@create')->name('private-school.parent.create');
});


Route::group(['middleware' => ['auth']], function () {
    Route::resources([
        'school'             => 'SchoolController',
        'teacher'            => 'TeacherController',
        'class'              => 'ClassController',
        'student'            => 'StudentController', 
        'subject'            => 'SubjectController',
        'schedule'           => 'Schedule\ScheduleController',
        'manage_relief'      => 'Schedule\ScheduleController',
        'relief_report'      => 'Schedule\ScheduleController',
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
        'dorm'               => 'DormController',
        'koperasi'           => 'Cooperative\User\UserCooperativeController',
        'delivery'           => 'DeliveryController',
    ]);
});
Route::get('adminTestFpx/{id}/', 'DirectPayController@adminTestFpx')->name('adminTestFpx');
Route::get('adminHandle', 'DirectPayController@handle');

Route::get('getTransactionInfo/{id}','DirectPayController@getTransactionInfo');
Route::get('paydonate', 'PayController@donateindex')->name('paydonate');
Route::get('donateFromMobile', 'PayController@donateFromMobile');
Route::post('trn', 'PayController@transaction')->name('trn');
Route::post('trn-dev', 'PayController@transactionDev')->name('trn-dev');
Route::post('payment', 'PayController@paymentProcess')->name('payment');

Route::post('fpxIndex', 'PayController@fpxIndex')->name('fpxIndex');
Route::post('directpayIndex', 'DirectPayController@directpayIndex')->name('directpayIndex');

Route::post('paymentStatus', 'PayController@paymentStatus')->name('paymentStatus');
Route::post('transactionReceipt', 'PayController@transactionReceipt')->name('transactionReceipt');
Route::post('directpayReceipt', 'DirectPayController@directpayReceipt')->name('directpayReceipt');
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
Route::post('/exportperanan', 'TeacherController@perananexport')->name('exportperanan');
Route::post('/importteacher', 'TeacherController@teacherimport')->name('importteacher');
Route::post('/importwarden', 'TeacherController@wardenimport')->name('importwarden');
Route::post('/importguard', 'TeacherController@guardimport')->name('importguard');


Route::post('/exportclass', 'ClassController@classexport')->name('exportclass');
Route::post('/importclass', 'ClassController@classimport')->name('importclass');

Route::post('/exportstudent', 'StudentController@studentexport')->name('exportstudent');
Route::post('/importstudent', 'StudentController@studentimport')->name('importstudent');

Route::post('/importparent', 'ParentController@parentImport')->name('importparent');

Route::post('/exportouting', 'DormController@outingexport')->name('exportouting');

Route::post('/exportresident', 'DormController@residentexport')->name('exportresident');

//dorm management import and export
Route::post('/exportdorm', 'DormController@dormexport')->name('exportdorm');
Route::post('/importdorm', 'DormController@dormimport')->name('importdorm');
Route::post('/importresident', 'DormController@residentimport')->name('importresident');

//studentlist export
Route::post('/exportallstudentlist', 'DormController@allstudentlistexport')->name('exportallstudentlist');
Route::post('/exportdormstudentlist', 'DormController@dormstudentlistexport')->name('exportdormstudentlist');

//report export
Route::post('/exportallcategory', 'DormController@allcategoryexport')->name('exportallcategory');
Route::post('/exportcategory', 'DormController@categoryexport')->name('exportcategory');
Route::post('/exportallrequest', 'DormController@allrequestexport')->name('exportallrequest');



Route::get('chat-user', 'MessageController@chatUser')->name('chat-user');
Route::get('chat-page/{friendId}', 'MessageController@chatPage')->name('chat-page');
Route::get('get-file/{filename}', 'MessageController@getFile')->name('get-file');
Route::post('send-message', 'MessageController@sendMessage')->name('send-message');

Route::post('/exportAllYuranStatus', 'FeesController@ExportAllYuranStatus')->name('exportAllYuranStatus');
Route::post('/exportJumlahBayaranIbuBapa', 'FeesController@ExportJumlahBayaranIbuBapa')->name('exportJumlahBayaranIbuBapa');
Route::post('/exportYuranOverview', 'FeesController@exportYuranOverview')->name('exportYuranOverview');



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




Route::resource('dorm', 'DormController');
Route::group(['prefix' => 'sekolah'], function () {
    // application
    Route::get('dorm/superadmin', 'DormController@indexSuperadmin')->name('dorm.superadmin');
    Route::get('dorm/pentadbir', 'DormController@indexPentadbir')->name('dorm.pentadbir');
    Route::get('dorm/penjaga', 'DormController@indexParent')->name('dorm.parent');
    Route::get('dorm/warden', 'DormController@indexWarden')->name('dorm.warden');
    Route::get('dorm/guard', 'DormController@indexGuard')->name('dorm.guard');
    Route::get('dorm/indexRequest/{id}', 'DormController@indexRequest')->name('dorm.indexRequest');
    Route::get('dorm/getStudentOutingDatatable', 'DormController@getStudentOutingDatatable')->name('dorm.getStudentOutingDatatable');
    Route::get('dorm/updateOutTime/{id}', 'DormController@updateOutTime')->name('dorm.updateOutTime');
    Route::get('dorm/updateInTime/{id}', 'DormController@updateInTime')->name('dorm.updateInTime');
    Route::get('dorm/updateArriveTime/{id}', 'DormController@updateArriveTime')->name('dorm.updateArriveTime');
    Route::get('dorm/updateApprove/{id}', 'DormController@updateApprove')->name('dorm.updateApprove');
    Route::get('dorm/updateTolak/{id}', 'DormController@updateTolak')->name('dorm.updateTolak');
    Route::get('dorm/updateCheckIn/{id}', 'DormController@updateCheckIn')->name('dorm.updateCheckIn');
    Route::get('dorm/updateBlacklist/{id}', 'DormController@updateBlacklist')->name('dorm.updateBlacklist');
    Route::get('dorm/fetchCategory', "DormController@fetchCategory")->name('dorm.fetchCategory');
    Route::get('dorm/fetchStudent', "DormController@fetchStudent")->name('dorm.fetchStudent');
    Route::get('dorm/fetchRole', "DormController@fetchRole")->name('dorm.fetchRole');

    // outing
    Route::get('dorm/storeOuting', 'DormController@storeOuting')->name('dorm.storeOuting');
    Route::get('dorm/indexOuting', 'DormController@indexOuting')->name('dorm.indexOuting');
    Route::get('dorm/createOuting', 'DormController@createOuting')->name('dorm.createOuting');
    Route::get('dorm/editOuting/{id}', 'DormController@editOuting')->name('dorm.editOuting');
    Route::post('dorm/updateOuting/{id}', 'DormController@updateOuting')->name('dorm.updateOuting');
    Route::post('dorm/destroyOuting/{id}', 'DormController@destroyOuting')->name('dorm.destroyOuting');
    Route::get('dorm/getOutingsDatatable', 'DormController@getOutingsDatatable')->name('dorm.getOutingsDatatable');

    // student-dorm (resident)
    Route::get('dorm/createResident', 'DormController@createResident')->name('dorm.createResident');
    Route::get('dorm/indexResident/{id}', 'DormController@indexResident')->name('dorm.indexResident');
    Route::get('dorm/storeResident', 'DormController@storeResident')->name('dorm.storeResident');
    Route::get('dorm/editResident/{id}', 'DormController@editResident')->name('dorm.editResident');
    Route::get('dorm/updateResident/{id}', 'DormController@updateResident')->name('dorm.updateResident');
    Route::post('dorm/destroyResident/{id}', 'DormController@destroyResident')->name('dorm.destroyResident');
    Route::post('dorm/fetchDorm', 'DormController@fetchDorm')->name('dorm.fetchDorm');
    Route::post('dorm/schoolStudent', 'DormController@schoolStudent')->name('dorm.schoolStudent');
    Route::get('dorm/getResidentsDatatable', 'DormController@getResidentsDatatable')->name('dorm.getResidentsDatatable');

    //dorm management
    Route::get('dorm/createDorm', 'DormController@createDorm')->name('dorm.createDorm');
    Route::get('dorm/indexDorm', 'DormController@indexDorm')->name('dorm.indexDorm');
    Route::get('dorm/storeDorm', 'DormController@storeDorm')->name('dorm.storeDorm');
    Route::get('dorm/updateDorm/{id}', 'DormController@updateDorm')->name('dorm.updateDorm');
    Route::get('dorm/editDorm/{id}', 'DormController@editDorm')->name('dorm.editDorm');
    Route::get('dorm/destroyDorm/{id}', 'DormController@destroyDorm')->name('dorm.destroyDorm');
    Route::get('dorm/getDormDataTable', 'DormController@getDormDataTable')->name('dorm.getDormDataTable');
    Route::get('dorm/clearDorm/{id}', 'DormController@clearDorm')->name('dorm.clearDorm');
    Route::get('dorm/outDorm/{id}', 'DormController@outDorm')->name('dorm.outDorm');

    //studentlist
    Route::get('dorm/indexStudentlist', 'DormController@indexStudentlist')->name('dorm.indexStudentlist');
    //     for all student
    Route::get('dorm/getAllStudentlistDatatable', 'DormController@getAllStudentlistDatatable')->name('dorm.getAllStudentlistDatatable');
    Route::get('dorm/getBlacklistStudentlistDatatable', 'DormController@getBlacklistStudentlistDatatable')->name('dorm.getBlacklistStudentlistDatatable');
    //     for particular dorm student
    Route::get('dorm/getDormStudentlistDatatable', 'DormController@getDormStudentlistDatatable')->name('dorm.getDormStudentlistDatatable');
    Route::get('dorm/getDormBlacklistStudentlistDatatable', 'DormController@getDormBlacklistStudentlistDatatable')->name('dorm.getDormBlacklistStudentlistDatatable');
    Route::post('dorm/blockStudent/{id}/{blockStatus}', 'DormController@blockStudent')->name('dorm.blockStudent');

    //report
    Route::get('dorm/reportPerStudent/{id}', 'DormController@reportPerStudent')->name('dorm.reportPerStudent');
    Route::get('dorm/getReportDatatable/{id}', 'DormController@getReportDatatable')->name('dorm.getReportDatatable');
    Route::get('dorm/getStudentOutingByCategory', 'DormController@getStudentOutingByCategory')->name('dorm.getStudentOutingByCategory');
    Route::get('dorm/indexReportAll', 'DormController@indexReportAll')->name('dorm.indexReportAll');
    Route::get('dorm/resetOutingLimit', 'DormController@resetOutingLimit')->name('dorm.resetOutingLimit');
    Route::get('dorm/printcategory', 'DormController@printcategory')->name('dorm.printcategory');
    Route::get('dorm/printall', 'DormController@printall')->name('dorm.printall');
    Route::get('dorm/printallrequest', 'DormController@printallrequest')->name('dorm.printallrequest');

    //reason outing
    Route::get('dorm/getReasonOutingDatatable', 'DormController@getReasonOutingDatatable')->name('dorm.getReasonOutingDatatable');
    Route::get('dorm/indexReasonOuting', 'DormController@indexReasonOuting')->name('dorm.indexReasonOuting');
    Route::get('dorm/editOutingReason/{id}', 'DormController@editOutingReason')->name('dorm.editOutingReason');
    Route::get('dorm/destroyReasonOuting/{id}', 'DormController@destroyReasonOuting')->name('dorm.destroyReasonOuting');
    Route::get('dorm/createReasonOuting', 'DormController@createReasonOuting')->name('dorm.createReasonOuting');
    Route::get('dorm/storeReasonOuting', 'DormController@storeReasonOuting')->name('dorm.storeReasonOuting');
    Route::get('dorm/updateReasonOuting/{id}', 'DormController@updateReasonOuting')->name('dorm.updateReasonOuting');
});

Route::group(['prefix' => 'delivery'], function () {
    Route::get('/index', 'DeliveryController@index')->name('delivery.parcelIndex');
   //Route::get('')
});

    //// *** Book & Stay ***//// 
    Route::group(['prefix' => 'booknstay'], function () {
        // Homestay Guest
        Route::get('home', 'HomestayController@homePage')->name('homestay.homePage');
        Route::get('homestay/{id}_{name}', 'HomestayController@showRoom')->name('homestay.showRoom');
        Route::get('fetch-unavailable-dates', 'HomestayController@fetchUnavailableDates')->name('homestay.fetchUnavailableDates');
        Route::get('fetch-discount-increase-dates', 'HomestayController@fetchDiscountIncreaseDates')->name('homestay.fetchDiscountIncreaseDates');
        Route::get('calculate-total-price', 'HomestayController@calculateTotalPrice')->name('homestay.calculateTotalPrice');
        Route::get('autocomplete-search', 'HomestayController@autocompleteSearch')->name('homestay.autocompleteSearch');
        Route::get('search-room','HomestayController@searchRoom')->name('homestay.searchRoom');
        // Route::get('generate-booking-details-pdf/{id}','HomestayController@generateBookingDetailsPdf')->name('homestay.generateBookingDetailsPdf');
        Route::get('get-more-reviews','HomestayController@getMoreReviews')->name('homestay.getMoreReviews');
        Route::group(['middleware' => 'auth'], function(){
            // Homestay Customer
            Route::post('book-room', 'HomestayController@bookRoom')->name('homestay.bookRoom');
            Route::get('tempahananda', 'HomestayController@tempahananda')->name('homestay.tempahananda');
            Route::post('add-review', 'HomestayController@addReview')->name('homestay.addReview');
            Route::get('booking-details/{id}','HomestayController@bookingDetails')->name('homestay.bookingDetails');
            Route::get('test-receipt','HomestayController@testReceipt')->name('homestay.testReceipt');
            // Homestay Management
            Route::get('urusbilik', 'HomestayController@urusbilik')->name('homestay.urusbilik');
            Route::get('gettabledata', 'HomestayController@gettabledata')->name('homestay.gettabledata');
            Route::get('tambahbilik', 'HomestayController@tambahbilik')->name('homestay.tambahbilik');
            Route::post('addroom', 'HomestayController@addroom')->name('homestay.addroom');
            Route::get('edit-room/{id}', 'HomestayController@editRoomPage')->name('homestay.editRoomPage');
            Route::post('update-room', 'HomestayController@updateRoom')->name('homestay.updateRoom');
            Route::post('delete-room', 'HomestayController@deleteRoom')->name('homestay.deleteRoom');
            Route::get('urustempahan', 'HomestayController@urustempahan')->name('homestay.urustempahan');
            Route::get('get-booking-data', 'HomestayController@getBookingData')->name('homestay.getBookingData');
            Route::post('checkout-homestay', 'HomestayController@checkoutHomestay')->name('homestay.checkoutHomestay');
            Route::post('cancel-booking', 'HomestayController@cancelBooking')->name('homestay.cancelBooking');
            Route::get('view-booking-history/{id}','HomestayController@viewBookingHistory')->name('homestay.viewBookingHistory');
            Route::get('get-booking-history-data', 'HomestayController@getBookingHistoryData')->name('homestay.getBookingHistoryData');
            Route::put('update-deposit-charge/{id}','HomestayController@updateDepositCharge')->name('homestay.updateDepositCharge');
            Route::get('view-customers-reviews','HomestayController@viewCustomersReview')->name('homestay.viewCustomersReview');
            Route::get('get-customers-review', 'HomestayController@getCustomersReview')->name('homestay.getCustomersReview');
            Route::get('view-performance-report','HomestayController@viewPerformanceReport')->name('homestay.viewPerformanceReport');
            Route::get('get-report-data', 'HomestayController@getReportData')->name('homestay.getReportData');
            Route::post('send-reminder' ,'HomestayController@sendReminder')->name('homestay.sendReminder');
            // Homestay Dates Management
            Route::get('homestay-date', 'HomestayController@manageDate')->name('homestay.manageDate');
            Route::get('get-organization-homestays', 'HomestayController@getOrganizationHomestays')->name('homestay.getOrganizationHomestays');
            Route::get('get-disabled-dates', 'HomestayController@getDisabledDates')->name('homestay.getDisabledDates');
            Route::post('add-disabled-date', 'HomestayController@addDisableDate')->name('homestay.addDisabledDate');
            Route::put('update-disabled-date', 'HomestayController@updateDisabledDate')->name('homestay.updateDisabledDate');
            Route::delete('delete-disabled-date', 'HomestayController@deleteDisabledDate')->name('homestay.deleteDisabledDate');
            //Homestay Promotion
            Route::get('promotion', 'HomestayController@promotionPage')->name('homestay.promotionPage');
            Route::get('get-promotion-data', 'HomestayController@getPromotionData')->name('homestay.getPromotionData');
            Route::get('setpromotion/{id}', 'HomestayController@setpromotion')->name('homestay.setpromotion');
            Route::get('fetch-unavailable-promotion-dates', 'HomestayController@fetchUnavailablePromotionDates')->name('homestay.fetchUnavailablePromotionDates');
            Route::post('insertpromotion', 'HomestayController@insertpromotion')->name('homestay.insertpromotion');
            Route::get('edit-promotion/{id}','HomestayController@editPromotionPage')->name('homestay.editPromotionPage');
            Route::get('fetch-unavailable-edit-promotion-dates', 'HomestayController@fetchUnavailableEditPromotionDates')->name('homestay.fetchUnavailableEditPromotionDates');
            Route::post('update-promotion', 'HomestayController@updatePromotion')->name('homestay.updatePromotion');
            Route::post('delete-promotion', 'HomestayController@deletePromotion')->name('homestay.deletePromotion');
            Route::get('view-promotion-history/{id}', 'HomestayController@viewPromotionHistory')->name('homestay.viewPromotionHistory');
            Route::get('get-promotion-history', 'HomestayController@getPromotionHistory')->name('homestay.getPromotionHistory');
        });
    });
    Route::post('test-payment', 'HomestayController@testPayment')->name('homestay.testPayment');

   
    //// ***End of Book & Stay ***//// 





    Route::get('disabledatepromo/{id}', 'HomestayController@disabledatepromo');
    Route::post('editpromo/{id}', 'HomestayController@editpromo');

    Route::get('bookinglist', 'HomestayController@bookinglist')->name('homestay.bookinglist');
    Route::get('bookhomestay/{id}', 'HomestayController@bookhomestay')->name('homestay.bookhomestay');
    Route::get('disabledateroom/{id}', 'HomestayController@disabledateroom');
    Route::post('bookhomestay/insertbooking/{id}/{price}', 'HomestayController@insertbooking');
    Route::get('homestayresit/{id}', 'HomestayController@homestayresit')->name('homestay.homestayresit');

    Route::post('tunjukpelanggan', 'HomestayController@tunjukpelanggan');
    Route::post('cancelpelanggan/{id}', 'HomestayController@cancelpelanggan');
    Route::get('userhistory', 'HomestayController@userhistory')->name('homestay.userhistory');
    Route::get('tunjuksales', 'HomestayController@tunjuksales')->name('homestay.tunjuksales');
    Route::get('homestaysales/{id}/{checkin}/{checkout}', 'HomestayController@homestaysales');
    
    //Route::post('test', 'HomestayController@test')->name('homestay.test');

    Route::get('/grab-setcar','GrabStudentController@setcar')->name('grab.setinsert');
    Route::post('/grab-insertcar','GrabStudentController@insertcar')->name('grab.insert');
    Route::get('/grab-check','GrabStudentController@checkcar')->name('grab.check');
    Route::post('/updaterow-grab/{id}','GrabStudentController@updatecar')->name('grab.update/{id}');
    Route::post('/updaterow-destinationgrab/{id}','GrabStudentController@updatedestination')->name('grab.updatedestination/{id}');
    Route::get('/grab-destination','GrabStudentController@setdestination')->name('grab.setdestination');
    Route::post('/grab-insertdestination','GrabStudentController@insertdestination')->name('grab.insertdestination');
    Route::get('/grab-checkpassenger','GrabStudentController@grabcheckpassenger')->name('grab.checkpassenger');
    Route::get('/book-grab','GrabStudentController@bookgrab')->name('book.grab');
    Route::post('/passengerselect-grab/{id}','GrabStudentController@selectbookgrab')->name('passengerselect-grab/{id}');
    Route::post('/passengerpay-grab/{id}','GrabStudentController@paymentgrab')->name('passengerpay-grab/{id}');
    Route::get('/bayar-grab','GrabStudentController@makepaymentgrab')->name('bayar.grab');
    Route::post('/passengernotify-grab/{id}','GrabStudentController@notifygrab')->name('passengernotify-grab/{id}');
    Route::get('/grab-notify','GrabStudentController@grabsendnotify')->name('grab.notifypassenger');
    Route::post('/notifygrab-passenger/{id}','GrabStudentController@updatenotifygrab')->name('notifygrab-passenger/{id}');
    Route::get('/grab-bayartempahan','GrabStudentController@grabbayartempahan')->name('grab.bayartempahan');
    Route::post('/passengerpilihtempahan-grab/{id}','GrabStudentController@passengerpilihtempahan')->name('passengerpilihtempahan-grab/{id}');
    Route::post('/passengerbayartempahan-grab/{id}','GrabStudentController@passengerbayartempahan')->name('passengerbayartempahan-grab/{id}');
    Route::get('/grab-checksales','GrabStudentController@checksales')->name('grab.checksales');
    Route::post('/grab-sales', 'GrabStudentController@grabsales')->name('generate-sales-graph');

    Route::get('/bus-setbus','BusController@setbus')->name('bus.setinsert');
    Route::post('/bus-insertbus','BusController@insertbus')->name('bus.insert');
    Route::get('/bus-managebus','BusController@managebus')->name('bus.manage');
    Route::post('/managebus-bus/{id}','BusController@manageselectedbus')->name('bus.displaymanage/{id}');
    Route::post('/updatemanagebus-bus/{id}','BusController@updatebus')->name('bus.update/{id}');
    Route::get('/book-bus','BusController@bookbus')->name('book.bus');
    Route::post('/passengerselect-bus/{id}','BusController@selectbookbus')->name('passengerselect-bus/{id}');
    Route::post('/passengerpay-bus/{id}','BusController@paymentbus')->name('passengerpay-bus/{id}');
    Route::post('/passengernotify-bus/{id}','BusController@notifybus')->name('passengernotify-bus/{id}');
    Route::get('/bus-notify','BusController@bussendnotify')->name('bus.notifypassenger');
    Route::post('/notifybus-passenger/{id}','BusController@updatenotifybus')->name('notifybus-passenger/{id}');
    Route::get('/bus-bayartempahan','BusController@busbayartempahan')->name('bus.bayartempahan');
    Route::post('/passengerpilihtempahan-bus/{id}','BusController@buspilihtempahan')->name('passengerpilihtempahan-bus/{id}');
    Route::post('/passengerbayartempahan-bus/{id}','BusController@passengerbusbayartempahan')->name('passengerbayartempahan-bus/{id}');
    Route::get('/bayar-bus','BusController@makepaymentbus')->name('bayar.bus');
    Route::get('/bus-listpassenger','BusController@listpassenger')->name('bus.listpassenger');
    Route::post('/passengerlist-bus/{id}','BusController@listpassengerbus')->name('passengerlist-bus/{id}');
    Route::get('/bus-checksales','BusController@checksales')->name('bus.checksales');
    Route::post('/bus-sales', 'BusController@bussales')->name('generate-sales-busgraph');


    

Route::group(['prefix' => 'orders'], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('/managemenu', 'OrderSController@managemenu')->name('orders.managemenu');
        Route::get('/listmenu/{id}', 'OrderSController@listmenu')->name('orders.listmenu');
        Route::get('/addmenu/{id}', 'OrderSController@addmenu')->name('orders.addmenu');
        Route::post('/processaddmenu/{id}', 'OrderSController@processaddmenu')->name('orders.processaddmenu');
        Route::post('/editmenu', 'OrderSController@editmenu')->name('orders.editmenu');
        Route::get('/uruspesanan', 'OrderSController@uruspesanan')->name('orders.uruspesanan');
        Route::get('/listpesanan/{id}', 'OrderSController@listpesanan')->name('orders.listpesanan');
        Route::post('/editpesanan', 'OrderSController@editpesanan')->name('orders.editpesanan');
        Route::get('/laporanjualan', 'OrderSController@laporanjualan')->name('orders.laporanjualan');
        Route::get('/salesreport/{id}/{start}/{end}', 'OrderSController@salesreport')->name('orders.salesreport');
        Route::get('/buatpesanan', 'OrderSController@buatpesanan')->name('orders.buatpesanan');
        Route::get('/pilihlokasi/{id}', 'OrderSController@pilihlokasi')->name('orders.pilihlokasi');
        Route::post('/addorder/{id}', 'OrderSController@addorder')->name('orders.addorder');
        Route::post('/extraorder/{id}/{id2}', 'OrderSController@extraorder')->name('orders.extraorder');
        Route::get('/checkout/{id}', 'OrderSController@checkout')->name('orders.checkout');
        Route::get('/trackorder', 'OrderSController@trackorder')->name('orders.trackorder');
    });
});

    

Route::get('/{name}', 'SchoolController@indexLogin')->name('school.loginindex');

Route::group(['middleware' => ['auth'], 'prefix' => 'lhdn'], function () {
    Route::get('/', 'DonationController@indexLHDN')->name('lhdn.index');
    Route::get('/list/datatable', 'DonationController@getLHDNHistoryDatatable')->name('donate.lhdn_dataTable');
    Route::get('/lhdn-receipt/{id}', 'DonationController@getLHDNReceipt')->name('lhdn-receipt');
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

        Route::get('/konvoChart','PolimasController@konvoChart')->name('polimas.student.konvoChart');
    });
});




