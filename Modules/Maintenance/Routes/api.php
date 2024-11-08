<?php

use App\Models\Employee;
use App\Models\Fault;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Http\Controllers\UpgradesAnalyticsController;
use Modules\Maintenance\Http\Controllers\UpgradeElevatorController;
use Modules\Maintenance\Http\Controllers\ReportController;
use Modules\Maintenance\Http\Controllers\MaintenanceContractController;
use Modules\Maintenance\Http\Controllers\MaintenanceVisitController;
use Modules\Maintenance\Http\Controllers\LoginController;
use Modules\Maintenance\Http\Controllers\AnalysisController;
use Modules\Maintenance\Http\Controllers\AreaController;
use Modules\Maintenance\Http\Controllers\VisitisAnalysisController;

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
Route::middleware('auth:sanctum')->prefix('maintenance')->group(function () {



    Route::get('/reports', [ReportController::class, 'index']); // جلب كل البلاغات
    Route::get('/reports/{id}', [ReportController::class, 'show']); // جلب بلاغ معين
    Route::post('/reports/initial', [ReportController::class, 'createInitialReport']); // المرحلة الأولى
    // الرحله الثانيه اضافة فني الصيانه
    Route::post('/reports/assign-technician', [ReportController::class, 'assignTechnicianToReport']);
    Route::post('/reports/problems', [ReportController::class, 'addProblemsToReport']);
    // approve report
    Route::post('/reports/approve', [ReportController::class, 'approveReport']);
    // convert report to upgrade
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
    // convert report to upgrade'
    Route::post('/reports/convert-to-upgrade/{reportId}', [ReportController::class, 'convertReportToUpgrade']);
    // renew contract
    Route::post('/contracts/renew-contract/{id}', [MaintenanceContractController::class, 'renewContract']);
    // getExpiredContracts
    Route::post('/contracts/upload-files', [MaintenanceContractController::class, 'uploadFiles']);
    Route::get('/contracts/get-expired-contracts', [MaintenanceContractController::class, 'getExpiredContracts']);
    Route::get('/contracts/unpaid-contracts', [MaintenanceContractController::class, 'getUnpaidContracts']);
    Route::post('/contracts/end-contract', [MaintenanceContractController::class, 'endContract']);
    // البحث فى العقد
    Route::get('/contracts/search-contract', [MaintenanceContractController::class, 'searchContract']);


    Route::get('/contracts/show/{id}', [MaintenanceContractController::class, 'show']);
    Route::get('/contracts/{type?}', [MaintenanceContractController::class, 'index']);
    Route::post('/contracts', [MaintenanceContractController::class, 'store']);
    Route::put('/contracts', [MaintenanceContractController::class, 'update']);
    // end contract

    // contract update

    // convert draft to contract
    Route::get('/area', [AreaController::class, 'index']);
    Route::post('/area/change-contract-area', [AreaController::class, 'changeContractArea']);

    Route::post('/contracts/convert-to-contract', [MaintenanceContractController::class, 'convertDraftToContract']);
    // contracts/:id

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
            'branches',
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

    Route::get('maintenance-data', function () {
        $data = [];

        $tables = [
            "elevator_types",
            "branches",
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


    Route::get('/visits/filter', [MaintenanceVisitController::class, 'filterVisitsByDateRange']);
    // MaintenanceVisit
    Route::get('/visits', [MaintenanceVisitController::class, 'index']);
    Route::get('/visits/{id}', [MaintenanceVisitController::class, 'show']);
    Route::post('/visits', [MaintenanceVisitController::class, 'store']);

    // get visits with reange
    Route::post('/visits/range', [MaintenanceVisitController::class, 'getVisitsWithRange']);

    // rescheduled visits
    Route::post('/visits/reschedule', [MaintenanceVisitController::class, 'reschedule']);
    // filiter visits by date range


    // البحث عن عميل من جدول العملاء باستخدم
    Route::post('/clients/search', [MaintenanceContractController::class, 'searchClients']);
    // clients/:id

    // CustomerRetentionRate
    Route::get('/analysis/customer-retention-rate', [AnalysisController::class, 'CustomerRetentionRate']);
    // deep anlysis
    Route::get('/analysis/deep-analysis/{analysisType}', [AnalysisController::class, 'deepAnalysis']);

    // CustomerLifetimeValue
    Route::get('/analysis/customer-lifetime-value', [AnalysisController::class, 'CustomerLifetimeValue']);
    // analysis
    Route::get('/analysis/{param}/{year?}', [AnalysisController::class, 'index']);

    // VisitisAnalysis
    Route::get('VisitisAnalysis', [VisitisAnalysisController::class, 'index']);



    Route::prefix('analytics/upgrades')->group(function () {
        Route::get('overview', [UpgradesAnalyticsController::class, 'getUpgradesOverview']);
        Route::get('neighborhood/{id}', [UpgradesAnalyticsController::class, 'getNeighborhoodUpgradesAnalysis']);
        Route::get('financial-efficiency', [UpgradesAnalyticsController::class, 'getFinancialEfficiencyAnalysis']);
        Route::get('trends', [UpgradesAnalyticsController::class, 'getUpgradeTrendsAnalysis']);
    });
});