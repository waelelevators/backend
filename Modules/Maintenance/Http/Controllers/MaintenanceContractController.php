<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Client;
use App\Models\MaintenanceContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Maintenance\Http\Requests\MaintenanceContractStoreRequest;
use Modules\Maintenance\Services\MaintenanceContractService;
use Modules\Maintenance\Transformers\MaintenanceContractResource;

class MaintenanceContractController extends Controller
{
    protected $maintenanceContractService;

    public function __construct(MaintenanceContractService $maintenanceContractService)
    {
        $this->maintenanceContractService = $maintenanceContractService;
    }

    public function index($type = null)
    {
        if ($type == 'draft') {
            $contracts = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'draft')->paginate(10);
        } else {
            $contracts = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'contract')->paginate(10);
        }
        return MaintenanceContractResource::collection($contracts);
    }

    public function store(MaintenanceContractStoreRequest $request)
    {
        if ($request->has('contract_id') && $request->contract_id > 0) {
            $contract = $this->maintenanceContractService->convertDraftToContract($request->all());
        } else {
            $contract = $this->maintenanceContractService->createContract($request->all());
        }

        return new MaintenanceContractResource($contract);
    }

    public function show($id)
    {
        $contract = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType', 'contractDetails', 'activeContract', 'client', 'logs')->findOrFail($id);
        // return $contract;
        return new MaintenanceContractResource($contract);
    }


    public function searchClients(Request $request)
    {

        $request->validate([
            'elevator_type_id' => 'nullable',
            'phone' => 'nullable',
        ]);
        $client_id = Client::where('phone', $request->phone)->first()->id;
        $maintenance_contracts = MaintenanceContract::where('elevator_type_id', $request->elevator_type_id)
            ->where('client_id', $client_id)
            ->where('contract_type', 'draft')
            ->with('area', 'city', 'neighborhood', 'elevatorType')
            ->get();

        // ahmed hmed

        return MaintenanceContractResource::collection($maintenance_contracts);

        $data = [
            'maintenance_contracts' => MaintenanceContractResource::collection($maintenance_contracts),
            'client' => $client,
        ];
    }


    public function convertDraftToContract(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'start_date' => 'required',
            'visits_count' => 'required',
            'end_date' => 'required',
        ]);

        $contract = $this->maintenanceContractService->convertDraftToContract($request->all());
        return new MaintenanceContractResource($contract);
    }

    // convertDraftToContract
}