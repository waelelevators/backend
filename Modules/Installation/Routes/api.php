<?php

use App\Helpers\MyHelper;

use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Employee;
use App\Models\Status;
use App\Models\TechniciansWorkOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\WorkOrdersProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Installation\Http\Controllers\CabinManufactureController;
use Modules\Installation\Http\Controllers\ContractController;
use Modules\Installation\Http\Controllers\ExternalDoorManufactureController;
use Modules\Installation\Http\Controllers\InstallationController;
use Modules\Installation\Http\Controllers\InternalDoorManufactureController;
use Modules\Installation\Http\Controllers\LocationAssignmentController;
use Modules\Installation\Http\Controllers\LocationDetectionController;
use Modules\Installation\Http\Controllers\LocationDetectionTechController;
use Modules\Installation\Http\Controllers\LocationStatusController;
use Modules\Installation\Http\Controllers\MyWorkOrdersController;
use Modules\Installation\Http\Controllers\QuotationController;
use Modules\Installation\Http\Controllers\ReportController;
use Modules\Installation\Http\Controllers\TemplateController;
use Modules\Installation\Http\Controllers\WorkOrderLogController;
use Modules\Installation\Http\Controllers\WorkOrdersController;

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


Route::middleware('auth:sanctum')->prefix('installation')->group(function () {

    // .....
    Route::get('location_data', [InstallationController::class, 'location']);
    Route::get('quotation_data', [InstallationController::class, 'quotation']);
    Route::get('door_data', [InstallationController::class, 'doorManufacture']);
    Route::get('cabin_data', [InstallationController::class, 'cabinManufacture']);
    Route::get('elevator_data', [InstallationController::class, 'contract']);
    Route::get('product_data', [InstallationController::class, 'products']);


    // كشف موقع
    Route::get('location_detections', [LocationDetectionController::class, 'index']);
    Route::post('location_detections', [LocationDetectionController::class, 'store']);
    Route::put('location_detections/{id}', [LocationDetectionController::class, 'update']);
    Route::get('location_detections/{id?}', [LocationDetectionController::class, 'show']);
    Route::delete('location_detections/{id?}', [LocationDetectionController::class, 'destroy']);

    // كشف موقع خاص بالفنيين
    Route::get('ld_tech/{id?}', [LocationDetectionTechController::class, 'show']);
    Route::delete('ld_tech/{id?}', [LocationDetectionController::class, 'destroy']);

    // اسناد الموقع للمناديب لتحديد جاهزي الموقع
    Route::get('location_assignment', [LocationAssignmentController::class, 'index']);
    Route::get(
        'location_assignment/representative/{id?}',
        [LocationAssignmentController::class, 'representative']
    );
    Route::get('location_assignment/{id?}', [LocationAssignmentController::class, 'show']);
    Route::put('location_assignment/{id?}', [LocationAssignmentController::class, 'update']);

    // جاهزية الموقع
    Route::post('location_statuses', [LocationStatusController::class, 'store']);
    Route::put('location_statuses/{id}', [LocationStatusController::class, 'update']);

    Route::get('location_statuses', [LocationStatusController::class, 'index']);
    Route::get('location_statuses/no-work-orders', [LocationStatusController::class, 'index'])->defaults('filterNoWorkOrder', true);
    Route::get('location_statuses/{id?}', [LocationStatusController::class, 'show']);
    Route::delete('location_statuses', [LocationStatusController::class, 'destroy']);


    // عرض سعر
    Route::get('quotations', [QuotationController::class, 'index']);
    Route::post('quotations', [QuotationController::class, 'store']);
    Route::put('quotations/{id}', [QuotationController::class, 'update']);
    Route::get('quotations/{id?}', [QuotationController::class, 'show']);
    Route::delete('quotations/{id?}', [QuotationController::class, 'destroy']);


    // توزيع العمليات على الفنيين
    Route::post('payment', [ContractController::class, 'payment']);
    Route::get('contract', [ContractController::class, 'index']);
    Route::post('contracts', [ContractController::class, 'store']);
    Route::put('contracts/{id}', [ContractController::class, 'update']);
    Route::get('contracts/{id?}', [ContractController::class, 'show']);
    Route::delete('contracts/{id?}', [ContractController::class, 'destroy']);
    Route::get('contracts/status/{status?}', [ContractController::class, 'status']);
    Route::get('contracts/to/cover', [ContractController::class, 'toCover']);
    Route::get('contracts/reports/monthly', [ContractController::class, 'monthlyReport']);
    Route::get('contracts/reports/{type?}', [ContractController::class, 'typeReport']);
    Route::get('contract-representatives', [ContractController::class, 'representatives']);

    // طلب تلبيس الباب الداخلي
    Route::get('i_door_manufacturers', [InternalDoorManufactureController::class, 'index']);
    Route::post('i_door_manufacturers', [InternalDoorManufactureController::class, 'store']);
    Route::post('i_door_manufacturers/change_status', [InternalDoorManufactureController::class, 'changeStatus']);
    Route::put('i_door_manufacturers/{id}', [InternalDoorManufactureController::class, 'update']);
    Route::get('i_door_manufacturers/{id?}', [InternalDoorManufactureController::class, 'show']);
    Route::delete('i_door_manufacturers/{id?}', [InternalDoorManufactureController::class, 'destroy']);

    // طلب تلبيس الباب الخارجي
    Route::get('e_door_manufacturers', [ExternalDoorManufactureController::class, 'index']);
    Route::post('e_door_manufacturers', [ExternalDoorManufactureController::class, 'store']);
    Route::post('e_door_manufacturers/change_status', [ExternalDoorManufactureController::class, 'changeStatus']);
    Route::put('e_door_manufacturers/{id}', [ExternalDoorManufactureController::class, 'update']);
    Route::get('e_door_manufacturers/{id?}', [ExternalDoorManufactureController::class, 'show']);
    Route::delete('e_door_manufacturers/{id?}', [ExternalDoorManufactureController::class, 'destroy']);

    // طلب تصنيع كبينة
    Route::get('cabin_manufacturers', [CabinManufactureController::class, 'index']);
    Route::post('cabin_manufacturers', [CabinManufactureController::class, 'store']);
    Route::post('cabin_manufacturers/change_status', [CabinManufactureController::class, 'changeStatus']);
    Route::put('cabin_manufacturers/{id}', [CabinManufactureController::class, 'update']);
    Route::get('cabin_manufacturers/{id?}', [CabinManufactureController::class, 'show']);
    Route::delete('cabin_manufacturers/{id?}', [CabinManufactureController::class, 'destroy']);

    Route::post('contracts/{contract_id}/attachment', [ContractController::class, 'attachment']);
    Route::get('contracts/{contract_id}/installments', [ContractController::class, 'installments']);

    Route::get('templates', [TemplateController::class, 'index']);
    Route::post('templates', [TemplateController::class, 'store']);
    Route::put('templates/{type?}', [TemplateController::class, 'update']);
    Route::get('templates/{type?}', [TemplateController::class, 'show']);

    Route::get('count-contracts', [ReportController::class, 'countContracts']);


    // Route::apiResource('contract', ContractController::class);
    // Route::post('contract/{contract}/assign', [ContractController::class, 'assign']);
    // Route::get('contract_quotations', [contractQuotationsController::class, 'index']);
    // Route::get('contract_quotations/{id?}', [contractQuotationsController::class, 'show']);
    // Route::put('contract_quotations/{id}', [contractQuotationsController::class, 'update']);
    // Route::delete('contract_quotations/{id}', [contractQuotationsController::class, 'destroy']);

    Route::post('accept-product', function (Request $request) {

        $dispatchItem = DispatchItem::find($request->id);
        if (auth('sanctum')->user()->id != $dispatchItem->dispatch->employee->user_id) {
            return response([
                'message' => 'عفوا لايمكن تاكيد صرف المنتج من قبل شخص اخر'
            ], 400);
        }


        $dispatchItem->status = 1;
        $dispatchItem->save();

        $workOrderProduct = WorkOrdersProduct::find($dispatchItem->dispatch_sheet_id);
        $workOrderProduct->received = $workOrderProduct->received + $dispatchItem->qty;
        $workOrderProduct->save();

        return response([], 200);
    });

    Route::get('work-orders-logs/{work_order_id}', function ($work_order_id) {

        return WorkOrderLog::where('work_order_id', $work_order_id)->get();
    });

    Route::post('work-orders/approval', [WorkOrdersController::class, 'approval']); // اعتماد العملية

    Route::get('work-orders/{activeStatus}', [WorkOrdersController::class, 'index']);

    Route::get('my-work-orders', [MyWorkOrdersController::class, 'myWorkOrder']);

    Route::post('work-orders', [WorkOrdersController::class, 'store']); // اسناد العملية للفنيين

    Route::post('start-work-status', [WorkOrdersController::class, 'operationStatus']); // بدء العملية او عدم البدء

    Route::get('work-orders/dispatched-products/{id}', [WorkOrdersController::class, 'dispatch']);

    Route::get('work-orders-remaining/{id}', [WorkOrdersController::class, 'remaining']);


    Route::get('has-work-order/{id}', function ($id) {
        $contract =  Contract::find($id);
        return $contract;
    });

    Route::get('work-orders/show/{workOrder}', [WorkOrdersController::class, 'show']);

    Route::post('work-orders/resume', [WorkOrdersController::class, 'resume']);
    Route::post('work-orders/unfreezeOrder', [WorkOrdersController::class, 'unfreezeOrder']);


    Route::post('work-order-log', [WorkOrderLogController::class, 'store']);

    Route::get('work-orders-status', function () {
        return Status::all();
    });

    Route::post('work-orders/manager-approval', function (Request $request) {

        $workOrder = WorkOrder::find($request->id);
        if ($request->has('status')) {

            $workOrder->status_id = 'pending';
            $workOrder->save();
        } else {
            $workOrder->manager_approval = 1;
            $workOrder->status_id = 'approved';
            $workOrder->end_at = now();
            $workOrder->save();

            $contract = Contract::find($workOrder->contract_id);
            $contract->saget_id = 2;
            $contract->save();
        }


        return WorkOrder::all();
    });


    Route::get('employees', function () {

        $workOrderIds =  WorkOrder::whereNull('end_at')->where('freeze', 0)->pluck('id')->toArray();
        $technicianIds = TechniciansWorkOrder::whereIn('work_order_id', $workOrderIds)->pluck('technician_id')->toArray();


        $employees =  Employee::whereNotIn('id', $technicianIds)->get();

        return $employees;


        $technicianIds = TechniciansWorkOrder::whereHas('workOrder', function ($query) {
            return $query->whereNull('end_at')->where('freeze', 0);
        })
            ->pluck('technician_id')
            ->toArray();


        $employees =  Employee::whereNotIn('id', $technicianIds)->get();

        return $employees;
    });

    Route::get('contracts', function () {

        // $contracts = Contract::readyToStart()->get();
        $contracts = Contract::all();


        $filteredContracts = $contracts->filter(function ($contract) {
            return $contract->is_ready_to_start && !$contract->has_work_order;
        });

        $filteredContracts->each(function ($contract) {
            $contract->append('is_ready_to_start');
        });

        return  ContractResource::collection($filteredContracts);
    });
});
