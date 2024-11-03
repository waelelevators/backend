<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Client;
use App\Models\MaintenanceContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Maintenance\Entities\MaintenanceContract as EntitiesMaintenanceContract;
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
            $contracts = EntitiesMaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'draft')
                ->latest()
                ->paginate(10);
        } else {
            $contracts = EntitiesMaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'contract')->latest()->paginate(10);
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
        return response([
            'message' => 'Contract created successfully',
            'status' => 'success',
        ], 201);
        return new MaintenanceContractResource($contract);
    }

    public function show($id)
    {
        $contract = EntitiesMaintenanceContract::findOrFail($id);
        // return $contract;
        return new MaintenanceContractResource($contract);
    }


    public function searchClients(Request $request)
    {

        $request->validate([
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'phone' => 'required|exists:clients,phone',
        ], [
            'phone.exists' => 'العميل غير موجود',
            'elevator_type_id.exists' => 'نوع المصعد غير موجود',
        ]);
        // $client_id = Client::where('phone', $request->phone)->findOrFail()->id;

        $client_id = Client::where('phone', $request->phone)->first()->id;

        $maintenance_contracts = EntitiesMaintenanceContract::where('elevator_type_id', $request->elevator_type_id)
            ->where('client_id', $client_id)
            ->where('contract_type', 'draft')
            ->with('area', 'city', 'neighborhood', 'elevatorType')
            ->get();



        $clients =  MaintenanceContractResource::collection($maintenance_contracts);

        $data = [
            'status' => 'success',
            'data' => MaintenanceContractResource::collection($maintenance_contracts)
        ];
        return response($data);
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
