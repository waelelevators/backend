<?php

use Modules\Maintenance\Http\Controllers\PdfContractController;
use Modules\Maintenance\Http\Controllers\PdfQuotationController;

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


Route::prefix('maintenance')->group(function () {
    Route::get('/', 'MaintenanceController@index');

    Route::get('quotation-pdf/{id}', [PdfQuotationController::class, 'pdf']); // عرض السعر
    Route::get('contract-pdf/{id}', [PdfContractController::class, 'pdf']); // عرض السعر

});
