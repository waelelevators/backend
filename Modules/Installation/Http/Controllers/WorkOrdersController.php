<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\MyHelper;
use App\Models\Contract;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Employee;
use App\Models\ExternalDoorManufacturer;
use App\Models\LocationAssignment;
use App\Models\LocationStatus;
use App\Models\Notification;
use App\Models\Status;
use App\Models\TechniciansWorkOrder;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\WorkOrdersProduct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Installation\Http\Resources\WorkOrderResource;

class WorkOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($activeStatus)
    {

        if ($activeStatus == 'all') {
            // العمليات الجارية
            $workOrders =  WorkOrder::with('technicians.employee')
                ->orderByDesc('created_at')->get();
        } else if ($activeStatus == 'mechanical') {

            $workOrders = WorkOrder::whereIn('status_id', ['pending', 'in progress', 'rejected', 'ready for delivery'])
                ->whereIn('stage_id', [1, 2])
                ->with('technicians.employee')->orderByDesc('created_at')->get();
        } else if ($activeStatus == 'electrical') {

            $workOrders = WorkOrder::whereIn('status_id', ['pending', 'in progress', 'rejected', 'ready for delivery'])
                ->where('stage_id', 3)
                ->with('technicians.employee')->orderByDesc('created_at')->get();
        } else { // جاهز للموافقة (كبير الفنيين) 

            $status = Status::find($activeStatus);

            $workOrders = WorkOrder::where('status_id', $status->value)
                ->with('technicians.employee')->orderByDesc('created_at')->get();
        }
        // return $workOrders;
        return WorkOrderResource::Collection($workOrders);
    }

    public function handOver($id)
    {

        $externalDoor = ExternalDoorManufacturer::with('externalDoorSpecification', 'orderResponse')
            ->findOrFail($id);

        $workOrders =  WorkOrder::with('technicians.employee')
            ->where([
                ['contract_id', $externalDoor->contract_id],
                ['stage_id', 1]
            ])
            ->orderByDesc('created_at')
            ->first();

        if ($externalDoor) {


            $resource =  new WorkOrderResource($workOrders);

            $transformedData = $resource->transformData();
            return response()->json(
                [
                    'workOrders' => $transformedData,
                    'externalDoors' => $externalDoor
                ]
            );
        }
        return response()->json([
            'message' =>
            'We Dont WorkOrder With this'
        ]);
    }

    public function remaining($id)
    {

        return WorkOrdersProduct::where('work_order_id', $id)
            ->whereColumn('received', '<', 'qty')
            ->with('product')
            ->get();
    }

    public function dispatch($id)
    {
        $dispatchs = Dispatch::where('work_order_id', $id)->pluck('id');

        return DispatchItem::whereIn('dispatch_id', $dispatchs)
            ->with('product', 'dispatch.employee', 'contractProductItems')->get();
    }

    public function resume(Request $request)
    {

        $workOrder = WorkOrder::find($request->work_order_id);

        $workOrder->status_id = 'pending';
        $workOrder->save();

        $emails = $workOrder->technicians->pluck('employee.user.email')->toArray();

        MyHelper::pushNotification($emails, [
            'title' => 'تم استئناف المهمه' . $workOrder->id,
            'body' => 'الفني ' . auth('sanctum')->user()->name . ' تم استئناف المهمه 🥷🏽👍',
        ]);


        return WorkOrder::with('locationStatus')->get();
    }

    public function unfreezeOrder(Request $request)
    {

        $workOrder = WorkOrder::find($request->work_order_id);
        $workOrder->freeze = !$workOrder->freeze;

        $workOrder->save();

        $emails = $workOrder->technicians
            ->pluck('employee.user.email')->toArray();

        MyHelper::pushNotification($emails, [
            'title' => 'تعديل تجميد المهمه' . $workOrder->id,
            'body' => 'تم تعديل حاله التجميد',
        ]);


        return WorkOrder::with('locationStatus')->get();
    }

    public function operationStatus(Request $request)
    {
        // بدء العملية او عدم البدء

        $workOrder = WorkOrder::find($request->id);

        if ($request->status == 'starting') {

            if (
                $workOrder->status_id == 'pending' ||
                $workOrder->status_id == 'not started'
            ) {
                $workOrder->start_at = now();
                $workOrder->status_id = 'in progress';
                $workOrder->save();

                MyHelper::pushNotification([$workOrder->user->email], [
                    'title' => 'تم بدء المهمه' . $workOrder->id,
                    'body' => 'الفني ' . auth('sanctum')->user()->name . ' بداء المهمه 🥷🏽👍',
                    'deep_link' => 'http://localhost:3000/installations/work-orders/' . $workOrder->id,
                ], 'work_order');

                WorkOrderLog::create([
                    'work_order_id' => $workOrder->id,
                    'status' => 'in progress',
                    'user_id' => auth('sanctum')->user()->id,
                    'comment' => $request->comment,
                ]);

                return response([
                    'status' => 'success',
                    'data' => WorkOrder::all(),
                    'message' => 'بداء المهمه' . $request->comment
                ], 200);
            } else {
                return response([
                    'status' => 'error',
                    'data' => WorkOrder::all(),
                    'message' => 'Work order already in process'
                ], 200);
            }
        } else {

            $workOrder->status_id = 'not started';
            $workOrder->save();

            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'status' => 'not started',
                'user_id' =>  auth('sanctum')->user()->id,
                'comment' => $request->comment,
            ]);

            MyHelper::pushNotification([$workOrder->user->email], [
                'title' => 'عدم بدء المهمه' . $workOrder->id,
                'body' => 'الفني ' . auth('sanctum')->user()->name . ' عدم بدء المهمه 🥷🏽👍' . $request->comment,
                'deep_link' => 'http://localhost:3000/installations/work-orders/' . $workOrder->id,
            ]);

            return response([
                'status' => 'success',
                'data' => WorkOrder::all(),
                'message' => 'عدم بدء المهمه'
            ], 200);
        }
    }

    function show(WorkOrder $workOrder)
    {
        // return WorkOrder::find($workOrder->id)->stage_id;
        // return $workOrder;
        // return WorkOrder::with('locationStatus')->find($workOrder->id)->location_status->assignment->contract_id;

        return $workOrder->load(
            'locationStatus',
            'locationStatus.assignment',
            'locationStatus.assignment.contract.locationDetection.client',

            'comments',
            'comments.user',
            'technicians.employee'
        );
    }



    function store(Request $request)
    {
        // اسناد  العملية للفنيين

        $request->validate([
            'assignment_id' => 'required|exists:location_statuses,id', // التاكد من وجود العقد اولا
            'contract_id' => 'required|exists:contracts,id', //
            'employees'     => 'required|array', // Ensure 'employees' is an array and is required.
            'employees.*' => 'exists:users,id|distinct',
        ]);
        DB::beginTransaction();

        try {

            $LocationAssignmentModel = LocationStatus::find($request->assignment_id);

            // Check if assignment exists
            $assignment = $LocationAssignmentModel->assignment;
            if (!$assignment) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Assignment not found.'
                ], 404);
            }

            // Get stage_id from assignment
            $stage_id = $assignment->stage_id;

            $existingWorkOrder = WorkOrder::where(
                [
                    ['assignment_id', '=', $request->assignment_id],
                    ['stage_id', '=', $stage_id],
                    ['contract_id', '=', $request->contract_id],
                ]
            )->first();

            if ($existingWorkOrder) {

                return response()->json([
                    'status' => 'failed',
                    'message' => 'تم انشاء امر العمل مسبقا'
                ], 422);
            }

            $workOrder = new WorkOrder;
            $workOrder->stage_id = $stage_id;
            $workOrder->contract_id = $request->contract_id;
            $workOrder->assignment_id = $request->assignment_id;
            $workOrder->user_id = auth('sanctum')->user()->id;
            $workOrder->save();

            $employees = User::whereIn('id', $request->employees)->get();


            foreach ($employees as $employee) {
                $technicianWorkOrder = new TechniciansWorkOrder();
                $technicianWorkOrder->assignment_id = $workOrder->assignment_id;
                $technicianWorkOrder->work_order_id = $workOrder->id;
                $technicianWorkOrder->technician_id = $employee->id;
                $technicianWorkOrder->stage_id = $stage_id;
                $technicianWorkOrder->save();


                Notification::Create([
                    'user_id' => $employee->id,
                    'data' => [
                        'title' => 'تم اليك اسناد مهمه رقم #' . $workOrder->id,
                        'body' => 'الرجاء البداء فى المهمه فورا 🥷🏽👍',
                        'url' => 'http://localhost:30000/installations/my-work-orders',
                    ],
                    'type' => 'work_order',
                ]);
            }
            DB::commit();

            // $emails = $employees->map->user->pluck('email')->filter()->values();

            // return MyHelper::pushNotification($emails, [
            //     'title' => 'تم اليك اسناد مهمه رقم #' . $workOrder->id,
            //     'body' => 'الرجاء البداء فى المهمه فورا 🥷🏽👍',
            //     'deep_link' => 'http://localhost:30000/installations/my-work-orders',
            // ]);

            return response()->json([
                'status' => 'success',
                'id' => $workOrder->id,
                'work_order' => $workOrder,
                'contract_id' => $workOrder->assignment_id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'حدث خطأ أثناء إنشاء أمر العمل. الرجاء المحاولة مرة أخرى.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    function approval(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:work_orders,id',
            'status' => 'required|in:approved,rejected,conditionally',
            'approval' => 'required',
            //  'comment' => 'required_if:status,rejected,conditionally',
        ], [
            'status.required' => 'الحاله المختاره غير صحيحه',
            'status.in' => 'الحاله المختاره غير صحيحه',
            // 'comment.required_if' => 'يجب كتابه التعليق',
        ]);

        if ($request->approval == 'chief_approval') {

            // return response($this->chiefApproval($request), 200);
            return  $this->chiefApproval($request);
        } else {
            return  $this->mangerApproval($request);
        }
    }

    function chiefApproval($request) // اعتماد كبير الفنيين
    {

        $workOrder = WorkOrder::find($request->id);

        if ($request->status == 'rejected') {
            $workOrder->status_id = $request->status;
            $workOrder->save();
        } else {
            $workOrder->status_id = $request->status;
            $workOrder->manager_approval = 'chief';
            $workOrder->save();
        }

        $this->sendPushNotification($request);

        $log = new WorkOrderLog();
        $log->work_order_id = $workOrder->id;
        $log->status = $request->status;
        $log->user_id = auth('sanctum')->user()->id;
        $log->comment = $request->comment;
        $log->save();


        return WorkOrder::where('status_id', 'ready for delivery')
            ->with('contract', 'stage')->get();
    }

    function mangerApproval($request) // اعتماد مدير العمليات
    {

        $workOrder = WorkOrder::find($request->id);

        $locationStatusModel = LocationStatus::find($workOrder->assignment_id);
        $contract = Contract::find($locationStatusModel->assignment->contract_id);

        $workOrder->manager_approval = $request->status;

        if ($request->status !== 'rejected') {

            $now = now();
            $statrting =  $workOrder->start_at;
            $duration  = $now->diffInDays($statrting);

            $workOrder->end_at =   now();
            $workOrder->duration = $duration;

            // if ($workOrder->status_id == 'ready for delivery') {
            $workOrder->status_id = 'approved';
            //  }

            $workOrder->save();

            if ($contract->stage_id !== 3) {

                $contract->stage_id = $contract->stage_id + 1;
                ApiHelper::LocationAssignment($contract, $contract->id);

                // return $contract->stage_id;
            } else {
                $contract->stage_id = 3;
                $contract->contract_status = 'Completed';
            }

            $contract->save();
        } else {

            $workOrder->status_id = 'in progress';
            $workOrder->end_at =   null;
            $workOrder->save();
        }


        $log = new WorkOrderLog();
        $log->work_order_id = $workOrder->id;
        $log->status = $request->status;
        $log->user_id = auth('sanctum')->user()->id;
        $log->comment = $request->comment;
        $log->save();


        $this->sendPushNotification($request);

        return WorkOrder::all();
    }

    function sendPushNotification($request)
    {

        $workOrder = WorkOrder::find($request->id);

        $workOrder->load('technicians.employee');
        $emails = $workOrder->technicians->pluck('employee.email')->toArray();


        MyHelper::pushNotification($emails, [
            'title' => 'تم تعديل حالة امر العمل رقم #' . $request->id,
            'body' => 'تم تعديل حالة امر العمل رقم #' . $request->id,
        ]);
    }
}
