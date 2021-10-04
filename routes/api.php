<?php

use Illuminate\Http\Request;
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

//Impinj Registration Devices
Route::post('impinj-rfid-registration-logs', 'RfidController@storeImpinjRfid');
Route::get('impinj-rfid-log-registration-details', 'RfidController@impinj_rfid_log_registration_details');

//Impinj Items Devices
Route::post('impinj-rfid-item-logs', 'RfidController@storeImpinjRfidItem');
Route::get('impinj-rfid-log-item-details', 'RfidController@impinj_rfid_log_item_details');

//Geovision Device

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
