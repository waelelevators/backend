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
use Modules\Maintenance\Http\Controllers\MaintenanceVisitController;
use Modules\Maintenance\Http\Controllers\LoginController;

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

    // login
    Route::post('/login', [LoginController::class, 'login']);
    // logout
    Route::post('/logout', [LoginController::class, 'logout']);

    // otp
    Route::post('/otp', [LoginController::class, 'otp']);
    // verify otp
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);

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

    Route::get('/contracts/{type?}', [MaintenanceContractController::class, 'index']);
    Route::post('/contracts', [MaintenanceContractController::class, 'store']);
    // convert draft to contract
    Route::post('/contracts/convert-to-contract', [MaintenanceContractController::class, 'convertDraftToContract']);
    // contracts/:id
    Route::get('/contracts/{id}', [MaintenanceContractController::class, 'show']);


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

    // upgrades
    Route::get('/upgrades', [UpgradeElevatorController::class, 'index']);
    Route::post('/upgrades', [UpgradeElevatorController::class, 'store']);
    Route::post('/upgrades/upload-attachment', [UpgradeElevatorController::class, 'uploadAttachment']);

    Route::get('/upgrades/{id}', [UpgradeElevatorController::class, 'show']);

    // rejectUpgrade
    Route::post('/upgrades/reject', [UpgradeElevatorController::class, 'rejectUpgrade']);
    // acceptUpgrade
    Route::post('/upgrades/accept', [UpgradeElevatorController::class, 'acceptUpgrade']);
    Route::post('/upgrades/add-required-products', [UpgradeElevatorController::class, 'addRequiredProducts']);


    // MaintenanceVisit
    Route::get('/visits', [MaintenanceVisitController::class, 'index']);
    Route::get('/visits/{id}', [MaintenanceVisitController::class, 'show']);
    Route::post('/visits', [MaintenanceVisitController::class, 'store']);


    // البحث عن عميل من جدول العملاء باستخدم
    Route::get('/clients/search', [MaintenanceContractController::class, 'searchClients']);
    // clients/:id
});