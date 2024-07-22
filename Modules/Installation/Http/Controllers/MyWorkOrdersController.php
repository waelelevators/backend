<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Contract;
use App\Models\TechniciansWorkOrder;
use App\Models\WorkOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MyWorkOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        return WorkOrder::with('locationStatus', 'stage')
            ->orderByDesc('created_at')->get();
    }
    public function myWorkOrder()
    {
        $employeeId = auth('sanctum')->user()->employee->id;

        return WorkOrder::with('locationStatus', 'stage')
            ->whereHas('technicians', function ($q) use ($employeeId) {
                $q->where('technician_id', $employeeId);
            })->orderByDesc('created_at')->get();
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
