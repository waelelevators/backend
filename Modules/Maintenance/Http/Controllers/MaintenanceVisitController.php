<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceUpgrade;
use App\Models\MaintenanceVisit;
use App\Service\GeneralLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Http\Resources\MaintenanceUpgradeResource;
use Modules\Maintenance\Services\UpgradeElevatorService;
use Modules\Maintenance\Http\Requests\UpgradeElevatorStoreRequest;
use Modules\Maintenance\Enums\MaintenanceUpgradeStatus;
use Modules\Maintenance\Services\MaintenanceVisitService;
use Modules\Maintenance\Http\Resources\MaintenanceVisitResource;

class MaintenanceVisitController extends Controller
{
    protected $maintenanceVisitService;

    public function __construct(MaintenanceVisitService $maintenanceVisitService)
    {
        $this->maintenanceVisitService = $maintenanceVisitService;
    }

    public function index()
    {
        $visits = MaintenanceVisit::with('maintenanceContractDetail', 'technician', 'user', 'logs')->get();
        return MaintenanceVisitResource::collection($visits);
    }

    public function show($id)
    {
        $visit = MaintenanceVisit::with('maintenanceContractDetail', 'technician', 'user', 'logs')->findOrFail($id);
        return new MaintenanceVisitResource($visit);
    }

    public function store(Request $request)
    {
        $request->validate([
            'maintenance_contract_detail_id' => 'required',
            'technician_id' => 'required',
        ]);

        try {
            $visit = $this->maintenanceVisitService->createVisit($request->all());
            if (is_array($visit)) {
                $visit = $visit[0]; // Take the first visit if an array is returned
            }
            $visit = MaintenanceVisit::with('maintenanceContractDetail', 'technician', 'user', 'logs')->findOrFail($visit->id);
            return new MaintenanceVisitResource($visit);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الزيارة.'], 500);
        }
    }
}