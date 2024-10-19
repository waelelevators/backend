<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    Route::post('visits/remove-image', [VisitController::class, 'removeImage']);

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
    // remove-image


    // report
    Route::get('reports', [ReportController::class, 'index']);


    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});