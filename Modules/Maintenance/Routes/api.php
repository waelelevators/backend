<?php



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

Route::middleware('auth:sanctum')->prefix('maintenance')->group(function () {

    // ..
    Route::get('location_data', [MaintenanceController::class, 'location']);
    Route::get('malfunction_data', [MaintenanceController::class, 'malfunction']);

    //   كشف الموقع
    Route::post('location_detections', [LocationDetectionController::class, 'store']);
    Route::get('location_detections', [LocationDetectionController::class, 'index']);
    Route::get('location_detections/{id?}', [LocationDetectionController::class, 'show']);
    Route::put('location_detections/{id}', [LocationDetectionController::class, 'update']);
    //  Route::get('location_detections/change_status', [LocationDetectionController::class, 'changeStatus']);
    Route::delete('location_detections/{id?}', [LocationDetectionController::class, 'destroy']);

    //    تحديث مصعد
    Route::post('upgrade_elevators', [UpgradeElevatorController::class, 'store']);
    Route::get('upgrade_elevators/parts/{id?}', [UpgradeElevatorController::class, 'index']);
    Route::get('upgrade_elevators/{id?}', [UpgradeElevatorController::class, 'show']);
    Route::put('upgrade_elevators/{id}', [UpgradeElevatorController::class, 'update']);
    Route::delete('upgrade_elevators/{id?}', [UpgradeElevatorController::class, 'destroy']);

    //  عروض الاسعار
    Route::post('quotations', [QuotationController::class, 'store']);
    Route::get('quotations', [QuotationController::class, 'index']);
    Route::get('quotations/{id?}', [QuotationController::class, 'show']);
    Route::put('quotations/{id}', [QuotationController::class, 'update']);
    Route::delete('quotations/{id?}', [QuotationController::class, 'destroy']);

    Route::get('quotations/search/{idNumber?}', [QuotationController::class, 'search']);

    // حالة عقد الصيانة
    Route::get('m_status', [MaintenanceStatusController::class, 'index']);

    // المناطق
    Route::get('area', [AreaController::class, 'index']);
    Route::post('area', [AreaController::class, 'store']);
    Route::put('area/{id}', [AreaController::class, 'update']);
    Route::get('area/{id?}', [AreaController::class, 'show']);
    Route::delete('area/{id?}', [AreaController::class, 'destroy']);

    // عقودات الصيانة
    Route::post('m_info', [MaintenanceInfoController::class, 'store']);
    Route::get('m_info/m_status={id?}', [MaintenanceInfoController::class, 'index']);
    Route::get('m_info/{id?}', [MaintenanceInfoController::class, 'show']);
    Route::put('m_info/{id}', [MaintenanceInfoController::class, 'update']);


    // العقودات الحالية
    Route::get('current_contracts/m_status={id?}', [CurrentMaintenanceController::class, 'index']);
    Route::get('current_contracts/{id?}', [CurrentMaintenanceController::class, 'show']);


    // توزيع العقودات 
    Route::put('m_dis/{id?}', [MaintenanceDistributionController::class, 'update']);
    Route::get('m_dis/area_id={id?}', [MaintenanceDistributionController::class, 'index']);

    // دفعيات الصيانة
    Route::post('payments', [MaintenancePaymentController::class, 'store']);
    Route::get('payments', [MaintenancePaymentController::class, 'index']);


    //  الصيانات الشهرية او الزيارات الشهرية
    Route::get('monthly', [MonthlyMaintenanceController::class, 'index']);
    Route::post('monthly', [MonthlyMaintenanceController::class, 'store']);
    Route::get('monthly/{id?}', [MonthlyMaintenanceController::class, 'show']);
    Route::put('monthly/{id}', [MonthlyMaintenanceController::class, 'update']);

    // زيارات الفنيين الشهرية
    Route::post('monthly_tech', [MonthlyMaintenanceTechnicantController::class, 'store']);
    Route::get('monthly_tech', [MonthlyMaintenanceTechnicantController::class, 'index']);
    Route::get('monthly_tech/{id?}', [MonthlyMaintenanceTechnicantController::class, 'show']);
    Route::put('monthly_tech/{id}', [MonthlyMaintenanceTechnicantController::class, 'update']);


    // الاعطال  
    Route::post('malfunctions', [MalfunctionController::class, 'store']);
    Route::get('malfunctions', [MalfunctionController::class, 'index']);
    Route::get('malfunctions/{id?}', [MalfunctionController::class, 'show']);
    Route::put('malfunctions/{id}', [MalfunctionController::class, 'update']);


    Route::get('maintenance_data', function () {
        $data = [];

        $tables = [
            "elevator_types", "machine_types", "machine_speeds", "door_sizes",
            "stops_numbers", "control_cards",
            "drive_types", "maintenance_types", "building_types", "maintenance_statuses"
        ];

        $regionsWithCity =   Region::whereHas('cities.neighborhoods')->with('cities.neighborhoods')->get();

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get();
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    });

    //   نوع العطل
    Route::get('{type}', function ($type) {
        return DB::table($type)->get(['id', 'name']);
    });
});
