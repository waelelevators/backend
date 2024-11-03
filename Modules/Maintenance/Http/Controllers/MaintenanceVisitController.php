<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceUpgrade;
use App\Models\MaintenanceVisit;
use App\Service\GeneralLogService;
use Carbon\Carbon;
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

    // getVisitsWithRange

    public function getVisitsWithRange(Request $request)
    {

        $request->validate([
            'start' => 'required|date|before_or_equal:end|date_format:"Y-m-d"',
            'end' => 'required|date|after_or_equal:start|date_format:"Y-m-d"',
        ]);

        $startDate = Carbon::parse($request->start)->startOfDay();
        $endDate = Carbon::parse($request->end)->endOfDay();

        $visits = MaintenanceVisit::with([
            'maintenanceContract',
            'maintenanceContractDetail',
            'technician',
            'user',
            'logs'
        ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->where('status', 'scheduled')
            ->paginate();
        return MaintenanceVisitResource::collection($visits);
    }

    // reschedule

    public function reschedule(Request $request)
    {
        $request->validate([
            'visit_id' => 'required',
            'visit_date' => 'required|date|after_or_equal:today|date_format:"Y-m-d"',
        ]);

        $visit = MaintenanceVisit::findOrFail($request->visit_id);
        $visit->visit_date = Carbon::parse($request->visit_date);
        $visit->save();
        $visit = MaintenanceVisit::with('maintenanceContractDetail', 'technician', 'user', 'logs')->findOrFail($visit->id);
        return new MaintenanceVisitResource($visit);
    }
}
