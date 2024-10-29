<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Area;
use App\Models\BuildingType;
use App\Models\City;
use App\Models\Client;
use App\Models\ControlCard;
use App\Models\DoorSize;
use App\Models\ElevatorType;
use App\Models\MachineSpeed;
use App\Models\MachineType;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\Neighborhood;
use App\Models\OuterDoorDirection;
use App\Models\StopNumber;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Services\AnalysisService;
use Modules\Maintenance\Services\ContractRenewalAnalysisService;

class AnalysisController extends Controller
{

    private $AnalysisService;
    private $renewalAnalysisService;

    // constructor
    public function __construct(AnalysisService $AnalysisService, ContractRenewalAnalysisService $renewalAnalysisService)
    {

        $this->AnalysisService = $AnalysisService;
        $this->renewalAnalysisService = $renewalAnalysisService;
    }
    public function index($param, $year = 2024)
    {

        $analysisType = $param;
        $year = intval($year);


        // how can U when ;lkdalkf


        // Determine the analysis type based on the $param value
        switch ($analysisType) {
            case 'area':
                $types = Area::all()->pluck('name');
                $countField = 'area_id';
                break;
            case 'elevator_type':
                $types = ElevatorType::pluck('name');
                $countField = 'elevator_type_id';
                $typeIds = ElevatorType::pluck('id');
                break;
            case 'has_stairs':
                $types = ['نعم', 'لا'];
                $countField = 'has_stairs';
                $typeIds = [0, 1];
                break;
            case 'stops_count':
                $types = StopNumber::pluck('name');
                $countField = 'stops_count';
                $typeIds = StopNumber::pluck('id');
                break;
            case 'machine_type':
                $types = MachineType::pluck('name');
                $countField = 'machine_type_id';
                $typeIds = MachineType::pluck('id');
                break;
            case 'machine_speed':
                $types = MachineSpeed::pluck('name');
                $countField = 'machine_speed_id';
                $typeIds = MachineType::pluck('id');
                break;
            case "door_direction":
                $types = OuterDoorDirection::pluck("name");
                $countField = "door_direction_id";
                $typeIds = OuterDoorDirection::pluck("id");
                break;

            case "control_type":
                $types = ControlCard::pluck("name");
                $countField = "control_type_id";
                $typeIds = ControlCard::pluck("id");
                break;
            case 'door_size':
                $types = DoorSize::all()->pluck('name');
                $countField = 'door_size_id';
                $typeIds = DoorSize::pluck('id');
                break;
            case 'neighborhood':
                $types = Neighborhood::all()->pluck('name');
                $countField = 'neighborhood_id';
                break;
            case 'client':
                $types = Client::all()->pluck('name');
                $countField = 'client_id';
                break;

            case 'building_type':
                $types = BuildingType::all()->pluck('name');
                $countField = 'building_type_id';
                break;
            case 'has_window':
                $types = ['نعم', 'لا'];
                $countField = 'has_window';
                $typeIds = [0, 1];
                break;

            default:
                return response()->json(['error' => 'Invalid analysis type'], 400);
        }


        // Initialize an array to store the analysis data
        $analysisData = [];

        // Iterate over the months of the specified year
        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
            $analysisData[$monthName] = [];

            // Iterate over the types
            for ($i = 0; $i < sizeof($typeIds); $i++) {
                // Count the number of maintenance contracts for the current type and month
                $count = MaintenanceContract::where($countField, $typeIds[$i])
                    ->whereYear('created_at', $year)->whereMonth('created_at', $month)
                    ->count();


                if ($count == 0) {
                    continue;
                }

                $analysisData[$monthName][$types[$i]] = $count;
            }
        }

        return response()->json([
            'types' => $types,
            'analysis' => $analysisData,
        ]);
    }




    // داله تقوم بارجاع معدل الاحفتاظ بالمعملاء

    public function CustomerRetentionRate()
    {
        return  $this->AnalysisService->CustomerRetentionRate();
    }


    public function CustomerLifetimeValue()
    {
        // return $this->run();
        // return $this->analyzeRenewalPatterns();
        // return $this->renewalAnalysisService->analyzeRenewalPeriods();
        // return $this->AnalysisService->getCustomersByContractYears();
        return $this->getDetailedClientAnalysis();
        return $this->AnalysisService->CustomerLifetimeValue();
    }

    public function getDetailedClientAnalysis()
    {
        try {
            $analysis = MaintenanceContractDetail::select([
                'client_id',
                DB::raw('COUNT(*) as total_contracts'),
                DB::raw('COALESCE(SUM(cost), 0) as total_revenue'),
                DB::raw('MIN(start_date) as first_contract_date'),
                DB::raw('MAX(end_date) as latest_contract_end'),
                DB::raw('CASE
                    WHEN DATEDIFF(MAX(end_date), MIN(start_date)) <= 0 THEN 1
                    ELSE ROUND(DATEDIFF(MAX(end_date), MIN(start_date)) / 365.25, 1)
                END as customer_lifetime_years')
            ])
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->where('start_date', '<=', DB::raw('end_date'))
                ->groupBy('client_id')
                ->having('total_contracts', '>', 0)
                ->orderBy('total_revenue', 'desc')
                ->with('client:id,name')
                ->get()
                ->map(function ($item) {
                    $lifetimeYears = max(1, $item->customer_lifetime_years); // استخدام حد أدنى سنة واحدة

                    return [
                        'client_id' => $item->client_id,
                        'client_name' => $item->client->name ?? 'غير معروف',
                        'total_contracts' => $item->total_contracts,
                        'total_revenue' => $item->total_revenue,
                        'lifetime_years' => $lifetimeYears,
                        'average_yearly_revenue' => round($item->total_revenue / $lifetimeYears, 2),
                        'first_contract' => Carbon::parse($item->first_contract_date)->format('Y-m-d'),
                        'latest_contract_end' => Carbon::parse($item->latest_contract_end)->format('Y-m-d')
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $analysis,
                'summary' => [
                    'total_clients' => $analysis->count(),
                    'total_revenue' => $analysis->sum('total_revenue'),
                    'average_lifetime_years' => round($analysis->avg('lifetime_years'), 2),
                    'average_contracts_per_client' => round($analysis->avg('total_contracts'), 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ أثناء تحليل البيانات',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function analyzeRenewalPatterns()
    {


        $result = $this->renewalAnalysisService->analyzeRenewalPatterns();

        if (!$result['status']) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحليل البيانات',
                'error' => $result['message']
            ], 500);
        }

        return response()->json([
            'نمط_تجديد_العقود' => [
                'ملخص_العملاء' => [
                    'إجمالي_العملاء' => $result['data']['customer_renewal_summary']['total_customers'],
                    'نمط_التعاقد' => [
                        'عقد_واحد' => $result['data']['customer_renewal_summary']['renewal_patterns']['single_contract'],
                        'عقود_متعددة' => $result['data']['customer_renewal_summary']['renewal_patterns']['multiple_contracts']
                    ],
                    'متوسط_العقود_للعميل' => $result['data']['customer_renewal_summary']['average_contracts_per_customer'],
                    'تصنيف_العملاء' => [
                        'عملاء_دائمون' => [
                            'عدد' => $result['data']['customer_renewal_summary']['customer_segments']['loyal_customers'],
                            'نسبة' => $result['data']['customer_renewal_summary']['customer_segments_percentage']['loyal_customers'] . '%'
                        ],
                        'عملاء_منتظمون' => [
                            'عدد' => $result['data']['customer_renewal_summary']['customer_segments']['regular_customers'],
                            'نسبة' => $result['data']['customer_renewal_summary']['customer_segments_percentage']['regular_customers'] . '%'
                        ],
                        'عملاء_جدد' => [
                            'عدد' => $result['data']['customer_renewal_summary']['customer_segments']['new_customers'],
                            'نسبة' => $result['data']['customer_renewal_summary']['customer_segments_percentage']['new_customers'] . '%'
                        ]
                    ]
                ],
                'فترات_التجديد' => [
                    'تجديد_فوري' => count($result['data']['renewal_gaps']['immediate_renewal'] ?? []),
                    'تجديد_عادي' => count($result['data']['renewal_gaps']['normal_renewal'] ?? []),
                    'تجديد_متأخر' => count($result['data']['renewal_gaps']['delayed_renewal'] ?? []),
                    'تجديد_متأخر_جداً' => count($result['data']['renewal_gaps']['late_renewal'] ?? [])
                ],
                'معدلات_التجديد_السنوية' => $result['data']['yearly_renewal_rates'],
                'تأثير_التكلفة' => $result['data']['cost_impact']
            ]
        ]);
    }
}