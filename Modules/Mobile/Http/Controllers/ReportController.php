<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return [
            'data' => [
                'total' => 10,
                'items' => [
                    'total' => 10,
                    'items' => []
                ]
            ]

        ];
    }

    // store
    public function store(Request $request)
    {
        return $request->all();
    }

    // technicianReports
    public function technicianReports(Request $request)
    {

        $user_id = 1;
        MaintenanceReport::create([
            'technician_id' => $user_id,
            'status' => 'open',
            'technician_id' => $user_id,
            'notes' => $request->notes,
            'maintenance_contract_id' => $request->maintenance_contract_id,
        ]);
        return response()->json(['message' => 'Report created successfully']);
    }
}
