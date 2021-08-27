<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@dashboard')->name('dashboard');

//Inventories
Route::get('/inventories', 'InventoryController@index')->name('inventories');
Route::get('/inventories-data', 'InventoryController@indexData');
Route::post('/inventories-store', 'InventoryController@store');
Route::post('/inventories-update', 'InventoryController@update');

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

