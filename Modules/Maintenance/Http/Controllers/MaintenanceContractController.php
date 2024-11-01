<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Client;
<<<<<<< HEAD
use App\Models\MaintenanceContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
=======
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\MaintenanceContract;
>>>>>>> 1ebb111 (Maintenance Part)
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
<<<<<<< HEAD
            $contracts = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'draft')->paginate(10);
        } else {
            $contracts = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'contract')->paginate(10);
=======
            $contracts = MaintenanceContract::with('region', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'draft')
                ->orderByDesc('id')
                ->paginate(10);
        } else {
            $contracts = MaintenanceContract::with('region', 'city', 'neighborhood', 'elevatorType')->where('contract_type', 'contract')
                ->orderByDesc('id')
                ->paginate(10);
>>>>>>> 1ebb111 (Maintenance Part)
        }
        return MaintenanceContractResource::collection($contracts);
    }

    public function store(MaintenanceContractStoreRequest $request)
    {
        if ($request->has('contract_id') && $request->contract_id > 0) {
<<<<<<< HEAD
            $contract = $this->maintenanceContractService->convertDraftToContract($request->all());
        } else {
            $contract = $this->maintenanceContractService->createContract($request->all());
        }

        return new MaintenanceContractResource($contract);
=======
            $this->maintenanceContractService->convertDraftToContract($request->all());
        } else {
            $this->maintenanceContractService->createContract($request->all());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم الاضافة بنجاح',
        ]);

        //  return new MaintenanceContractResource($contract);
>>>>>>> 1ebb111 (Maintenance Part)
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
<<<<<<< HEAD
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
=======
        $client = Client::where('phone', $request->phone)->firstOrFail();

        if ($client) {
            $clientId = $client->id;
            $maintenance_contracts = MaintenanceContract::where(
                'elevator_type_id',
                $request->elevator_type_id
            )
                ->where('client_id', $clientId)
                ->where('contract_type', 'draft')
                ->with('area', 'city', 'neighborhood', 'elevatorType')
                ->get();

            return MaintenanceContractResource::collection($maintenance_contracts);
        } else {
            // Handle the case when no client is found
            // For example, return a response or log a message
            return response()->json(['error' => 'client not found'], 404);
        }
>>>>>>> 1ebb111 (Maintenance Part)
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
<<<<<<< HEAD
}
=======
}
>>>>>>> 1ebb111 (Maintenance Part)
