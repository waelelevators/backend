<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Contract;
use App\Models\TechniciansWorkOrder;
use App\Models\WorkOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Installation\Http\Resources\WorkOrderResource;

class MyWorkOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $model = WorkOrder::with('locationStatus', 'stage')
            ->orderByDesc('created_at')
            ->whereNotIn('manager_approval', ['conditionally', 'approved'])
            ->get();

        return WorkOrderResource::collection($model);
    }
    public function myWorkOrder()
    {
        $employeeId = auth('sanctum')->user()->id; // عمليات الفني (الموبايل)

        $model = WorkOrder::with('locationStatus', 'stage')
            ->whereNotIn('manager_approval', ['conditionally', 'approved'])
            ->whereHas('technicians', function ($q) use ($employeeId) {
                $q->where('technician_id', $employeeId);
            })->orderByDesc('created_at')->get();

        return WorkOrderResource::collection($model);
    }

    function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'employees' => 'required|array',
        ]);

        $Contract =  Contract::find($request->contract_id);
        $stage_id =  $Contract->stage_id;

        $work_order = new WorkOrder;
        $work_order->stage_id = $stage_id;
        $work_order->contract_id = $request->contract_id;
        $work_order->save();

        return $request;
    }
}
