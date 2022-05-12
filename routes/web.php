<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HasRole;
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




Route::get('/items', 'ItemsController@index')->name('home');
Route::get('/item-search-employee', 'ItemsController@itemSearchEmployee');
Route::get('/item-search', 'ItemsController@itemSearch');
Route::post('/save-borrow-item', 'ItemsController@saveBorrowItem');
Route::post('/save-return-item', 'ItemsController@saveReturnItem');
Route::post('/save-upload-user-inventories', 'ReportsController@uploadUserInventories');

Route::get('/', function () {
    return redirect('/login');
});




    Auth::routes();

    //Current User
    Route::get('/current-user','HomeController@currentUser');


    // Dashboard
    Route::get('/home', 'HomeController@dashboard')->name('dashboard');
    Route::get('/dashboard-data','HomeController@dashboardData');

    Route::get('/inventories-active-data', 'HomeController@inventoriesActiveData');
    Route::get('/inventories-spare-data', 'HomeController@inventoriesSpareData');
    Route::get('/inventories-loan-items-data', 'HomeController@inventoriesLoanItemsData');
    Route::get('/inventories-maintenance-data', 'HomeController@inventoriesMaintenanceData');

    //Inventories
    Route::get('/inventories', 'InventoryController@index')->name('inventories');
    Route::get('/inventories-data', 'InventoryController@indexData');
    Route::post('/inventories-store', 'InventoryController@store');
    Route::post('/inventories-update', 'InventoryController@update');

    Route::post('/save-upload-inventories', 'InventoryController@uploadInventories');

    //For Disposal Inventories
    Route::get('/for-disposal', 'InventoriesForDisposalController@index')->name('for-disposal');
    Route::get('/for-disposal-data', 'InventoriesForDisposalController@indexData');
    Route::post('/for-disposal-store', 'InventoriesForDisposalController@store');
    Route::post('/for-disposal-update', 'InventoriesForDisposalController@update');
    Route::get('/for-disposal-items', 'InventoriesForDisposalController@forDisposalItems');
    Route::get('/for-disposal-items-data', 'InventoriesForDisposalController@forDisposalItemsData');

    Route::post('/place-request-for-disposal', 'InventoriesForDisposalController@placeRequestForDisposal');
    Route::post('/delete-request-for-disposal', 'InventoriesForDisposalController@deleteRequestForDisposal');

    Route::post('/delete-request-for-disposal-item', 'InventoriesForDisposalController@deleteRequestForDisposalItem');
    Route::post('/approve-request-for-disposal-item', 'InventoriesForDisposalController@approveRequestForDisposalItem');
    Route::post('/disapprove-request-for-disposal-item', 'InventoriesForDisposalController@disapproveRequestForDisposalItem');

    Route::post('/save-rfd-for-disposal', 'InventoriesForDisposalController@saveRdfForDisposal');
    Route::post('/delete-rdf-for-disposal', 'InventoriesForDisposalController@deleteRdfForDisposal');

    Route::post('/save-picture-for-disposal-item', 'InventoriesForDisposalController@savePictureForDisposalItem');
    Route::post('/delete-picture-for-disposal-item', 'InventoriesForDisposalController@deletePictureForDisposalItem');

    //Transfer Inventories
    Route::get('/inventory-transfer', 'InventoryController@transfer')->name('transfer');
    Route::get('/inventory-transfer-data', 'InventoryController@transferData');
    Route::post('/save-inventory-transfer', 'InventoryController@saveTransfer');
    Route::post('/update-inventory-transfer', 'InventoryController@updateTransfer');
    Route::post('/remove-inventory-transfer-item', 'InventoryController@removeTransferItem');

    Route::get('/print-inventory-transfer', 'InventoryController@printTransfer');

    Route::post('/assign-employee-item', 'InventoryController@assignEmployeeItem');

    //Receive Inventories
    Route::get('/inventory-receive', 'InventoryController@receive')->name('receive');
    Route::get('/search-transfer-code', 'InventoryController@searchTransferCode');
    Route::post('/save-receive-item', 'InventoryController@saveReceiveItem');
    Route::get('/inventory-receive-data', 'InventoryController@receiveData');

    //Users
    Route::get('/users', 'UsersController@index')->name('users');
    Route::get('/get-users-data', 'UsersController@getUsersData');
    Route::post('/save-change-user-role', 'UsersController@saveChangeUserRole');

    //Activity Logs
    Route::get('/activity-logs', 'ActivityLogsController@index')->name('activity-logs');
    Route::get('/get-activity-logs', 'ActivityLogsController@activityLogs');

    //Settings - Company
    Route::get('/setting-companies', 'SettingsCompanyController@index')->name('settings-companies');
    Route::get('/setting-companies-data', 'SettingsCompanyController@indexData');
    Route::post('/setting-companies-store', 'SettingsCompanyController@store');
    Route::post('/setting-companies-update', 'SettingsCompanyController@update');

    //Settings - Location
    Route::get('/setting-locations', 'SettingsLocationController@index')->name('settings-locations');
    Route::get('/setting-locations-data', 'SettingsLocationController@indexData');
    Route::post('/setting-locations-store', 'SettingsLocationController@store');
    Route::post('/setting-locations-update', 'SettingsLocationController@update');

    //Settings - Departments
    Route::get('/setting-departments', 'SettingsDepartmentController@index')->name('settings-departments');
    Route::get('/setting-departments-data', 'SettingsDepartmentController@indexData');
    Route::post('/setting-departments-store', 'SettingsDepartmentController@store');
    Route::post('/setting-departments-update', 'SettingsDepartmentController@update');

    //Settings - Locations
    Route::get('/setting-buildings', 'SettingsBuildingController@index')->name('settings-buildings');
    Route::get('/setting-buildings-data', 'SettingsBuildingController@indexData');
    Route::post('/setting-buildings-store', 'SettingsBuildingController@store');
    Route::post('/setting-buildings-update', 'SettingsBuildingController@update');

    //Settings - Types
    Route::get('/setting-types', 'SettingsTypeController@index')->name('settings-types');
    Route::get('/setting-types-data', 'SettingsTypeController@indexData');
    Route::post('/setting-types-store', 'SettingsTypeController@store');
    Route::post('/setting-types-update', 'SettingsTypeController@update');

    //Settings - Categories
    Route::get('/setting-categories', 'SettingsCategoryController@index')->name('settings-categories');
    Route::get('/setting-categories-data', 'SettingsCategoryController@indexData');
    Route::post('/setting-categories-store', 'SettingsCategoryController@store');
    Route::post('/setting-categories-update', 'SettingsCategoryController@update');

    //Reports
    Route::get('/reports-borrow-logs', 'ReportsController@borrowLogs')->name('borrow-logs');
    Route::get('/reports-borrow-logs-data', 'ReportsController@borrowLogData');
    Route::get('/reports-return-logs', 'ReportsController@returnLogs')->name('return-logs');
    Route::get('/reports-return-logs-data', 'ReportsController@returnLogData');

    Route::get('/reports-asset-logs', 'ReportsController@assetLogs')->name('asset-logs');
    Route::get('/reports-asset-logs-data', 'ReportsController@assetLogData');

    Route::get('/reports-disposed-logs', 'ReportsController@disposedLogs')->name('disposed-logs');
    Route::get('/reports-disposed-logs-data', 'ReportsController@disposedLogData');

    Route::get('/reports-asset-handover-forms', 'ReportsController@assetHandoverForms')->name('asset-handover-forms');
    Route::get('/reports-asset-handover-forms-data', 'ReportsController@assetHandoverFormsData');

    Route::get('/print-asset-handover-form', 'ReportsController@printAssetHandoverForm');

    //RFID Registration Device
    Route::get('/rfid-registration-devices', 'RfidRegistrationDeviceController@index')->name('rfid-registration-devices');
    Route::get('/rfid-registration-devices-data', 'RfidRegistrationDeviceController@indexData');
    Route::post('/rfid-registration-devices-store', 'RfidRegistrationDeviceController@store');
    Route::post('/rfid-registration-devices-update', 'RfidRegistrationDeviceController@update');

    Route::post('/clear-rfid-log-registration-device', 'RfidRegistrationDeviceController@clearRfidLogRegistrationDevice');

    //Impinj
    Route::get('/rfid-registration-impinj-devices-activated-data', 'RfidRegistrationDeviceController@activatedImpinjReaderData');
    Route::post('/rfid-registration-impinj-devices-activate', 'RfidRegistrationDeviceController@activateImpinjReader');
    Route::post('/rfid-registration-geovision-devices-activate', 'RfidRegistrationDeviceController@activatedGeovisionReaderData');

    //System Approver
    Route::get('/system-approver-it-data', 'SystemApproverController@systemApproverITData');
    Route::get('/system-approver-finance-data', 'SystemApproverController@systemApproverFinanceData');

    //Employees
    Route::get('/all-employees', 'EmployeeController@allEmployees');

    //Generate QR
    Route::get('/generate-qr-code', 'InventoryController@generateQrCode');

    //Generate LOU
    Route::get('/generate-letter-of-undertaking', 'LetterOfUndertakingController@generateLetterOfUndertaking');
    Route::get('/get-lou-user-inventory', 'LetterOfUndertakingController@getLouUserInventory');
    Route::post('/save-generate-letter-of-undertaking', 'LetterOfUndertakingController@saveGenerateLetterOfUndertaking');
    Route::get('/print-generate-letter-of-undertaking-laptop', 'LetterOfUndertakingController@printGenerateLetterOfUndertakingLaptop');

    




    

