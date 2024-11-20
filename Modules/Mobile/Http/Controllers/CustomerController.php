<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceContractDetail;
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
        $contract = MaintenanceContractDetail::with('contract', 'contract.city', 'contract.neighborhood', 'contract.area', 'visits', 'visits.technician')->find($id);

        return MaintenanceContractDetailResource::make($contract);
        return ['data' => $contract];
    }
}
