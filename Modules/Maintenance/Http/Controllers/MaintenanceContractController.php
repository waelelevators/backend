<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\MaintenanceContract;
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

        $contract = $this->maintenanceContractService->createContract($request->validated());
        return new MaintenanceContractResource($contract);
    }

    public function show($id)
    {
        $contract = MaintenanceContract::findOrFail($id);
        return new MaintenanceContractResource($contract);
    }
}