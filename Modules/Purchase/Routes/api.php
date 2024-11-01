<?php

use App\Models\RFQ;
use Illuminate\Http\Request;
use Modules\Purchase\Http\Controllers\ContractProductQuantityController;
use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PdfController;
use Modules\Purchase\Http\Controllers\QuotationController;
use Modules\Purchase\Http\Controllers\RFQController;

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

//Route::middleware('auth:sanctum')->get('/purchase', function (Request $request) {

Route::middleware('auth:sanctum')->prefix('purchase')->group(function () {


    Route::post('product_quantities', [ContractProductQuantityController::class, 'index']);
    Route::post('product_quantities/create', [ContractProductQuantityController::class, 'store']);
    Route::put('product_quantities/{id?}', [ContractProductQuantityController::class, 'update']);
    Route::get('product_quantities/{id?}', [ContractProductQuantityController::class, 'show']);
    Route::post('product_quantities/delete/{id}', [ContractProductQuantityController::class, 'destroy']);


    // اضافة منتج جديد في مايخض البضاعة

    // عرض البضاعة المفترض طلبها لعقد معين في مرحلة معينة
    Route::get('contracts/products/{contract_id}/{stage_id}', [QuotationController::class, 'index']);

    // بحث عن منتج معين 
    Route::post('serach_products', function (Request $request) {

        return response(\App\Models\Product::where('name', 'like', '%' . $request->name . '%')->get());
    });

    // انشاء عرض سعر ( طلب بضاعة)
    Route::post('contracts/products/{contract_id}', [QuotationController::class, 'store']);


    Route::get('rfqs', [RFQController::class, 'index']);
    Route::get('rfqs/{rFQ}', [RFQController::class, 'show']);
});
