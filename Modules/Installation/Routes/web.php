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
*/;

use Illuminate\Support\Facades\Route;

use Modules\Installation\Http\Controllers\ContractPdfController;
use Modules\Installation\Http\Controllers\LocationDetectionController;
use Modules\Installation\Http\Controllers\QuotationController;


Route::prefix('installation')->group(function () {

    Route::get('/', 'InstallationController@index');


});
