<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceContract;
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
            'visit_ids' => 'required|array',
            'visit_id.*' => 'required|exists:maintenance_visits,id',
            'visit_date' => 'required|date|after_or_equal:today|date_format:"Y-m-d"',
        ]);

        $visit = MaintenanceVisit::whereIn('id', $request->visit_ids)->update(
            [
                'status' => 'scheduled',
                'visit_date' => Carbon::parse($request->visit_date)
            ]
        );

        return response([
            'message' => 'تم تغيير حالة الزيارة بنجاح',
            'status' => 'success',
        ]);
    }


    // filterVisitsByDateRange
    function filterVisitsByDateRange(Request $request)
    {

        $request->validate([
            'start_date' => 'required|date|before_or_equal:end|date_format:"Y-m-d"',
            'end_date' => 'required|date|after_or_equal:start|date_format:"Y-m-d"',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $visits = MaintenanceVisit::with(['maintenanceContract', 'maintenanceContract.client', 'maintenanceContract.city', 'maintenanceContract.neighborhood', 'maintenanceContract.area', 'maintenanceContractDetail', 'technician', 'user', 'logs'])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->where('status', 'scheduled')
            ->orderBy('visit_date', 'asc')
            ->get()
            ->groupBy('visit_date');

        return $visits->map(function ($visits, $key) {
            return [
                'visit_date' => $key,
                'visits' => $visits->map(function ($visit) {
                    return [
                        'visit_id' => $visit->id,
                        'technician' => $visit->technician->name ?? null,
                        'status' => $visit->status,
                        'visit_start_date' => $visit->visit_start_date,
                        'visit_end_date' => $visit->visit_end_date,
                        'notes' => $visit->notes,
                        'visit_contract_id' => $visit->maintenanceContract->id,
                        'contract_number' => $visit->maintenanceContract->contract_number,
                        'client_name' => $visit->maintenanceContract->client->name ?? null,
                        'city' => $visit->maintenanceContract->city->name,
                        'neighborhood' => $visit->maintenanceContract->neighborhood->name ?? null,
                        'area' => $visit->maintenanceContract->area->name,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
}
