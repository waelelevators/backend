<?php

use App\Models\Employee;
use App\Models\Fault;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Http\Controllers\AreaController;
use Modules\Maintenance\Http\Controllers\CurrentMaintenanceController;
use Modules\Maintenance\Http\Controllers\LocationDetectionController;
use Modules\Maintenance\Http\Controllers\MaintenanceController;
use Modules\Maintenance\Http\Controllers\MaintenanceDistributionController;
use Modules\Maintenance\Http\Controllers\MaintenanceInfoController;
use Modules\Maintenance\Http\Controllers\MaintenancePaymentController;
use Modules\Maintenance\Http\Controllers\MaintenanceStatusController;
use Modules\Maintenance\Http\Controllers\MalfunctionController;
use Modules\Maintenance\Http\Controllers\MonthlyMaintenanceController;
use Modules\Maintenance\Http\Controllers\MonthlyMaintenanceTechnicantController;
use Modules\Maintenance\Http\Controllers\QuotationController;
use Modules\Maintenance\Http\Controllers\UpgradeElevatorController;
use Modules\Maintenance\Http\Controllers\ReportController;
use Modules\Maintenance\Http\Controllers\MaintenanceContractController;

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

// Route::middleware('auth:sanctum')->prefix('maintenance')->group(function () {

// });

// prefix maintenance
Route::prefix('maintenance')->group(function () {

    Route::get('/reports', [ReportController::class, 'index']); // جلب كل البلاغات
    Route::get('/reports/{id}', [ReportController::class, 'show']); // جلب بلاغ معين
    Route::post('/reports/initial', [ReportController::class, 'createInitialReport']); // المرحلة الأولى
    // الرحله الثانيه اضافة فني الصيانه
    Route::post('/reports/assign-technician', [ReportController::class, 'assignTechnicianToReport']);
    Route::post('/reports/problems', [ReportController::class, 'addProblemsToReport']);
    // approve report
    Route::post('/reports/approve', [ReportController::class, 'approveReport']);

    // اضافة الاسبيرات او المنتجات المستخدمه في الصيانه
    // Route::post('/reports/add-required-products', [ReportController::class, 'addRequiredProductsToReport']);
    // products
    Route::get('/products', function () {
        $products = Product::all();
        return [
            'data' => $products
        ];
    });

    // add products to report
    Route::post('/reports/add-required-products', [ReportController::class, 'addProductsToReport']);

    Route::get('/contracts', [MaintenanceContractController::class, 'index']);
    // contracts/:id
    Route::get('/contracts/{id}', [MaintenanceContractController::class, 'show']);

    Route::post('/contracts', [MaintenanceContractController::class, 'store']);

    // technicians
    Route::get('/technicians', function () {
        $technicians = Employee::all();
        return [
            'data' => $technicians
        ];
    });


    // faults
    Route::get('/faults', function () {
        $faults = Fault::all();
        return [
            'data' => $faults
        ];
    });


    Route::get('maintenance_data', function () {
        $data = [];

        $tables = [
            "elevator_types",
            "machine_types",
            "machine_speeds",
            "door_sizes",
            "stops_numbers",
            "control_cards",
            "drive_types",
            "maintenance_types",
            "building_types"
        ];

        $regionsWithCity =  Region::whereHas('cities')->with('cities')->get();

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get();
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    });
});
