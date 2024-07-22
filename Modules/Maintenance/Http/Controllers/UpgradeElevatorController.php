<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceUpgradeElevator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Maintenance\Http\Requests\UpgradeElevatorStoreRequest;
use Modules\Maintenance\Http\Resources\MaintenanceUpgradeResource;

class UpgradeElevatorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id)
    {
        $upgrade = MaintenanceUpgradeElevator::where("m_location_id", $id)->orderByDesc('created_at')->get();
        return MaintenanceUpgradeResource::collection($upgrade);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(UpgradeElevatorStoreRequest $request)
    {

        $upgrade = new MaintenanceUpgradeElevator();

        $elevatorsParts = is_array($request['elevatorsParts']) ?
            $request['elevatorsParts'] :
            array($request['elevatorsParts']);

        $upgrade->m_location_id = $request['m_location_id'];
        $upgrade->elevators_parts = json_encode($elevatorsParts);
        $upgrade->notes = $request['notes'] ?? '';
        $upgrade->total_cost = $request['total_cost'];
        $upgrade->tax = $request['tax'];
        $upgrade->done_by = $request['done_by'];
        $upgrade->user_id = Auth::guard('sanctum')->user()->id;
        $upgrade->save();


        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة القطع  بنجاح',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $model = MaintenanceUpgradeElevator::findOrFail($id);

        return $model;
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
        $upgrade = MaintenanceUpgradeElevator::findOrFail($id);

        $elevatorsParts = is_array($request['elevatorsParts']) ?
            $request['elevatorsParts'] :
            array($request['elevatorsParts']);

        $upgrade->elevators_parts = json_encode($elevatorsParts);
        $upgrade->notes = $request['notes'] ?? '';
        $upgrade->total_cost = $request['total_cost'];
        $upgrade->tax = $request['tax'];
        $upgrade->done_by = $request['done_by'];
        $upgrade->user_id = Auth::guard('sanctum')->user()->id;
        $upgrade->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل القطع بنجاح',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
