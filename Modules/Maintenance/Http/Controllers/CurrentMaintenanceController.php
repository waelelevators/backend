<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\MaintenanceLog;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Resources\MaintenanceLogResource;

class CurrentMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id)
    {

        //     if ($id === 'all')
        //     $maintenances =  MaintenanceLog::with('mInfo.contracts', 'mInfo.representatives')
        //         ->orderByDesc('created_at')->get();

        // else $maintenances = MaintenanceLog::join('maintenances', 'maintenances.id', '=', 'maintenance_logs.m_id')
        //     ->select('maintenances.*', 'maintenance_logs.*')
        //     ->with('mInfo.contracts', 'mInfo.representatives')
        //     ->orderByDesc('maintenance_logs.created_at')
        //     ->where('maintenances.m_status_id', $id)
        //     ->get();

        // return  MaintenanceLogResource::collection($maintenances);

        if ($id === 'all') $maintenances = MaintenanceLog::join('maintenances', 'maintenances.id', '=', 'maintenance_logs.m_id')
            ->select('maintenances.*', 'maintenance_logs.*')
            ->orderByDesc('maintenance_logs.created_at')
            ->get();

        else

            $maintenances = MaintenanceLog::join('maintenances', 'maintenances.id', '=', 'maintenance_logs.m_id')
                ->select('maintenances.*', 'maintenance_logs.*')
                ->orderByDesc('maintenance_logs.created_at')
                ->where('maintenances.m_status_id', $id)
                ->get();

        return  MaintenanceLogResource::collection($maintenances);
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MaintenanceLog::findOrFail($id);
    }
}
