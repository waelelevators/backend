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

    public function index()
    {
        $contracts = MaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')->get();
        return MaintenanceContractResource::collection($contracts);
    }

    public function store(MaintenanceContractStoreRequest $request)
    {
        // return $request;
        $contract = $this->maintenanceContractService->createContract($request->all());

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

        $clients = Client::query();

        foreach ($request->all() as $field => $value) {
            if ($value) {
                $clients->where($field, 'like', "%{$value}%");
            }
        }

        return $clients->first();
    }
}
