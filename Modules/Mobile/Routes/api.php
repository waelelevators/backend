<?php

use App\Http\Controllers\CustomerController;
use App\Models\Fault;
use App\Models\MaintenanceUpgrade;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Maintenance\Http\Controllers\LoginController;
use Modules\Mobile\Http\Controllers\AuthController;
use Modules\Mobile\Http\Controllers\CustomerController as ControllersCustomerController;
use Modules\Mobile\Http\Controllers\PaymentController;
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
        // /reports/remove-image
        Route::post('reports/remove-image', [ReportController::class, 'removeImage']);

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


        // customers group
        Route::group(['prefix' => 'customer'], function () {

            Route::get('contracts', [ControllersCustomerController::class, 'index']);
            Route::get('contracts', [ControllersCustomerController::class, 'index']);
            Route::get('contracts/{id}', [ControllersCustomerController::class, 'show']);
            // reports
            Route::get('reports', [ControllersCustomerController::class, 'reports']);
            // customer/contracts/${contractId}/report

            Route::post('contracts/{contractId}/report', [ControllersCustomerController::class, 'storeReport']);

            Route::get('upgrades', function () {
                $upgrades = MaintenanceUpgrade::with('requiredProducts.product', 'city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client', 'logs')
                    ->take(10)->get();

                $upgrades = $upgrades->map(function ($upgrade) {
                    return [
                        'id' => $upgrade->id,
                        'contract_number' => $upgrade->contract_number,
                        'status' => $upgrade->status,
                        'created_at' => $upgrade->created_at->format('Y-m-d'),
                        'total' => $upgrade->total,
                        'tax' => $upgrade->tax,
                        'discount' => $upgrade->discount,
                        'site_images' => $upgrade->site_images ?? [],
                        'attachment_contract' => $upgrade->attachment_contract,
                        'attachment_receipt' => $upgrade->attachment_receipt,
                        'city' => $upgrade->city->name ?? '',
                        'neighborhood' => $upgrade->neighborhood->name ?? '',

                    ];
                });

                return [
                    'data' => $upgrades
                ];
            });


            Route::get('upgrades/{id}', function ($id) {
                $upgrade = MaintenanceUpgrade::with('requiredProducts.product', 'city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client', 'logs', 'technician')->find($id);
                // return $upgra    des;
                // return $upgrade;

                $products =  $upgrade->requiredProducts->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_id' => $product->product_id,
                        'name' => $product->product->name ?? '',
                        'price' => $product->price,
                        'quantity' => $product->quantity,
                        'subtotal' => $product->subtotal,
                    ];
                });

                $upgrades =   [
                    'id' => $upgrade->id,
                    'contract_number' => $upgrade->contract_number,
                    'status' => $upgrade->status,
                    'created_at' => $upgrade->created_at->format('Y-m-d'),
                    'total' => $upgrade->total,
                    'tax' => $upgrade->tax,
                    'discount' => $upgrade->discount,
                    'site_images' => $upgrade->site_images ?? [],
                    'attachment_contract' => $upgrade->attachment_contract,
                    'attachment_receipt' => $upgrade->attachment_receipt,
                    'city' => $upgrade->city->name ?? '',
                    'neighborhood' => $upgrade->neighborhood->name ?? '',
                    'technician' => $upgrade->technician ?? '',
                    'products' => $products

                ];


                return [
                    'data' => $upgrades
                ];
            });
        });



        // include crm routes
        include 'crm_api.php';
    });


    Route::post('/payments/initiate', [PaymentController::class, 'initiatePayment']);
    Route::get('/payments/verify/{id}', [PaymentController::class, 'verifyPayment']);
    Route::get('/payments/upgrades/payment-callback', [PaymentController::class, 'paymentCallback']);
    Route::post('/payments/webhook', [PaymentController::class, 'handleWebhook']);


    Route::post('login', [LoginController::class, 'login'])->name('login');
});