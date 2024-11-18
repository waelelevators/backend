<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceReport;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Maintenance\Transformers\MaintenanceContractDetailResource;

class CustomerController extends Controller
{

    // العقودات
    function index()
    {

        $client_id = 5720;
        $contracts = MaintenanceContractDetail::where('client_id', $client_id)
            ->selectRaw('maintenance_contract_details.*,
                (SELECT COUNT(*) FROM maintenance_visits WHERE maintenance_visits.maintenance_contract_detail_id = maintenance_contract_details.id AND maintenance_visits.status = "completed") AS completed_visits_count')
            ->with('contract', 'contract.city', 'contract.neighborhood', 'contract.area')

            ->get();


        return ['data' => $contracts];
        return  MaintenanceContractDetailResource::collection($contracts);
    }

    // show contract details
    function show($id)
    {
        $contract = MaintenanceContractDetail::with('contract', 'contract.city', 'contract.neighborhood', 'contract.area', 'visits', 'visits.technician', 'lastReport')->find($id);

        return MaintenanceContractDetailResource::make($contract);
        return ['data' => $contract];
    }

    // reports
    function reports()
    {
        $user = auth('sanctum')->user();
        $reports =  MaintenanceReport::with(
            'maintenanceContract',
            'technician',
            'maintenanceContract.activeContract',
            'maintenanceContract.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'requiredProducts',
            'logs'
        )
            ->orderBy('id', 'desc')
            // ->where('maintenanceContract', function ($query) {
            //     // $query->where('client_id', auth()->user()->client_id);
            // })
            // ->where('status', 'assigned')
            // ->take(10)
            ->get();


        $reports = $reports->map(function ($report) {
            $logs = $report->logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'comment' => $log->comment,
                    'action' => $log->action,
                    'date' => $log->created_at ? $log->created_at : null,
                    'user' => $log->user->name ?? null,
                ];
            });
            return [
                'id' => $report->id,
                'notes' => $report->notes,
                'status' => $report->status,
                'created_at' => $report->created_at,
                'products' => $report->requiredProducts,
                'city' => $report->maintenanceContract->city->name ?? null,
                'neighborhood' => $report->maintenanceContract->neighborhood->name ?? null,
                'logs' => $logs ?? [],
                'technician' => $report->technician,
                'start_date' => $report->maintenanceContract->activeContract->start_date ?? null,
                'end_date' => $report->maintenanceContract->activeContract->end_date ?? null,
            ];
        });
        return ['data' => $reports];
    }

    function storeReport($contractId)
    {

        $maintenance_contract_id = MaintenanceContractDetail::where('id', $contractId)->first()->maintenance_contract_id;
        $report = new MaintenanceReport();
        $report->maintenance_contract_details_id = $contractId;
        $report->maintenance_contract_id = $maintenance_contract_id;
        $report->status = 'open';
        $report->notes = request()->notes;
        $report->save();
        return $report;
    }
}