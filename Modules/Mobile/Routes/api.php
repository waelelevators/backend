<?php

use App\Models\Fault;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Maintenance\Http\Controllers\LoginController;
use Modules\Mobile\Http\Controllers\AuthController;
use Modules\Mobile\Http\Controllers\VisitController;
use Modules\Mobile\Http\Controllers\ReportController;

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

Route::prefix('mobile')->group(function () {
    // Route::middleware('auth:api')->get('/mobile', function (Request $request) {
    //     return $request->user();
    // });

    // auth group
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('visits/remove-image', [VisitController::class, 'removeImage']);

        // products


        Route::get('visits', [VisitController::class, 'index']);
        Route::get('visits/{id}', [VisitController::class, 'show']);
        Route::post('visits/{id}', [VisitController::class, 'update']);

        Route::post('/upload-image', [VisitController::class, 'uploadImage']);

        // update location
        Route::post('visits/{id}/location-update', [VisitController::class, 'updateLocation']);

        Route::get('maintenance-items', function () {
            return ["data" =>  [
                [
                    'id' => 1,
                    'label' => 'تشحيم سكك التقل',
                    'icon' => 'exit-outline',
                    'name' => 'p1',
                ],
                [
                    'id' => 2,
                    'label' => 'تنظيف التابلوه من الغبار',
                    'icon' => 'cog',
                    'name' => 'p2',

                ],
                [
                    'id' => 3,
                    'label' => 'فحص كومينات الموتور',
                    'icon' => 'git-network',
                    'name' => 'p3',
                ],
                // تنظيف الكابينات
                [
                    'id' => 4,
                    'label' => 'تنظيف الكابينات',
                    'icon' => 'color-fill',
                    'name' => 'p4',
                ],
                // تشحيم السكك
                [
                    'id' => 5,
                    'label' => 'تشحيم السكك',
                    'icon' => 'exit-outline',
                    'name' => 'p5',
                ],
                // تنظيف المكنه
                [
                    'id' => 6,
                    'label' => 'تنظيف المكنه والموتور ',
                    'icon' => 'exit-outline',
                    'name' => 'p6',
                ],
                // تنظيف المكنه

            ]];
        });
        // products
        Route::get('products', function () {
            return ["data" =>  Product::all()];
        });
        Route::get('faults', function () {
            return ["data" =>  Fault::all()];
        });


        // report
        Route::get('technician/reports', [ReportController::class, 'index']);
        Route::get('technician/reports/{id}', [ReportController::class, 'show']);

        // get contractors to report
        Route::get('technician/contractors', [ReportController::class, 'contractors']);
        // reports/add-products
        Route::post('technician/reports/{id}/update-products', [ReportController::class, 'updateProducts']);
        // upate faults
        Route::post('technician/reports/{id}/faults', [ReportController::class, 'updateFaults']);
        // technician/reports/update-status
        Route::post('technician/reports/update-status', [ReportController::class, 'updateStatus']);
        Route::post('technician/reports/{id}/update-products', [ReportController::class, 'updateProducts']);

        // reports add
        Route::post('technician/reports', [ReportController::class, 'technicianReports']);

        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        // resend otp
        Route::post('resend-otp', [LoginController::class, 'otp']);
        // verify otp
        Route::post('verify-otp', [LoginController::class, 'verifyOtp']);
    });


    Route::post('login', [LoginController::class, 'login'])->name('login');
});