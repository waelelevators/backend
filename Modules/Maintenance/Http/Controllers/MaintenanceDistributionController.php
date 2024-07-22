<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\MaintenanceLog;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Maintenance\Resources\MaintenanceLogResource;

class MaintenanceDistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id)
    {

        //     if ($id === 'all')
        //     $maintenances =  MaintenanceLog::get();
        // else $maintenances = MaintenanceLog::join('maintenances', 'maintenances.id', '=', 'maintenance_logs.m_id')
        //     ->where('maintenances.m_status_id', $id)
        //     ->get();
        // return  MaintenanceLogResource::collection($maintenances);


        if ($id == 'all') $models =  MaintenanceLog::get();

        else $models = MaintenanceLog::where('area_id', $id)->get();

        return  MaintenanceLogResource::collection($models);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // $models = MaintenanceLog::
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'id' => 'nullable|integer|exists:areas,id',
            ],
            [
                'id.exists' => 'الرقم غير موجود في جدول مناطق الصيانة',
            ]
        );
        $contracts = $request['m_id'];

        foreach ($contracts as  $value) {

            $model = MaintenanceLog::findOrFail($value);
            $model->area_id = $id;
            $model->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم العملية بنجاح',
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
