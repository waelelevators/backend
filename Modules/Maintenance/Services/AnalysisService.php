<?php

namespace Modules\Maintenance\Services;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceVisit;
use App\Service\GeneralLogService;
use Modules\Maintenance\Repositories\MaintenanceContractRepository;
use Modules\Maintenance\Repositories\MaintenanceContractDetailRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalysisService
{

    /**
     * Calculate customer retention rate.
     *
     * Customer retention rate is the percentage of customers who remain customers
     * over a certain period of time. This function calculate customer retention
     * rate for new, old and current contracts.
     *
     * @return array
     */
    public function CustomerRetentionRate()
    {



        // الحاليه
        $current =  [
            ['name' => 'free', 'value' => 10],
            ['name' => 'out', 'value' => 10],
            ['name' => 'in', 'value' => 10],
        ];

        return  [
            'new' => $this->newMaintenanceContracts(),
            'old' => $this->oldMaintenanceContracts(),
            'current' => $this->currentMaintenanceContracts(),
        ];
    }



    /**
     * Get new maintenance contracts count.
     *
     * Get new maintenance contracts count grouped by free and paid.
     * The result will be an array of objects with two properties,
     * name and value.
     *
     * @return array
     */
    public function newMaintenanceContracts()
    {

        $free_count =  MaintenanceContractDetail::where('maintenance_type', 'free')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('maintenance_type')
            ->count();

        $pid_count =  MaintenanceContractDetail::where('maintenance_type', 'pid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('maintenance_type')
            ->count();

        $current_contracts = MaintenanceContractDetail::where('status', 'active')->count();

        return [
            ['name' => 'صيانه مجانيه', 'value' => $free_count],
            ['name' => 'صيانه مدفوعه', 'value' => $pid_count],
            ['name' => 'العقود الحاليه', 'value' => $current_contracts],
        ];
    }



    // old maintenace contracts
    function oldMaintenanceContracts()
    {
        return [

            ['name' => 'free', 'value' => 10],
            ['name' => 'out', 'value' => 10],
            ['name' => 'in', 'value' => 10],
        ];
    }


    function currentMaintenanceContracts()
    {
        return [

            ['name' => 'free', 'value' => 10],
            ['name' => 'out', 'value' => 10],
            ['name' => 'in', 'value' => 10],
        ];
    }

    // Customer Lifetime Value (CLTV)
    function CustomerLifetimeValue()
    {


        $contractsWithDuration = MaintenanceContractDetail::select([
            'client_id',
            \DB::raw('ROUND(DATEDIFF(end_date, start_date) / 365.25, 1) as contract_years')
        ])
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        return $contractsWithDuration;

        // تجميع العملاء حسب مدة العقد
        $clientsByDuration = [];
        $totalClients = collect($contractsWithDuration)->unique('client_id')->count();

        foreach ($contractsWithDuration as $contract) {
            $years = ceil($contract->contract_years); // تقريب لأعلى رقم صحيح
            if (!isset($clientsByDuration[$years])) {
                $clientsByDuration[$years] = [
                    'count' => 0,
                    'percentage' => 0
                ];
            }
            // نحسب عدد العملاء الفريدين لكل مدة
            $clientsByDuration[$years]['count'] = $contractsWithDuration
                ->where('contract_years', '>=', $years - 1)
                ->where('contract_years', '<', $years)
                ->unique('client_id')
                ->count();
        }

        // حساب النسب المئوية
        foreach ($clientsByDuration as $years => &$data) {
            $data['percentage'] = round(($data['count'] / $totalClients) * 100, 2);
        }

        // ترتيب النتائج حسب عدد السنوات
        ksort($clientsByDuration);

        return response()->json([
            'total_unique_clients' => $totalClients,
            'distribution' => $clientsByDuration,
            'summary' => [
                'average_contract_years' => $contractsWithDuration->avg('contract_years'),
                'max_contract_years' => $contractsWithDuration->max('contract_years'),
                'min_contract_years' => $contractsWithDuration->min('contract_years')
            ]
        ]);
    }

    public function getCustomersByContractYears()
    {
        try {
            // ROUND(DATEDIFF(MAX(end_date), MIN(start_date)) / 365.25, 1)
            $customerLifetimes = MaintenanceContractDetail::select([
                'client_id',
                DB::raw('TIMESTAMPDIFF(YEAR, MIN(start_date), MAX(end_date)) as years_completed'),
                DB::raw('COUNT(DISTINCT id) as total_contracts'),
                DB::raw('SUM(cost) as total_revenue')
            ])
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->where('start_date', '<=', DB::raw('end_date'))
                ->groupBy('client_id')
                ->get();


            $yearlyDistribution = [];

            foreach ($customerLifetimes as $customer) {
                $years = max(1, $customer->years_completed);

                if (!isset($yearlyDistribution[$years])) {
                    $yearlyDistribution[$years] = [
                        'completed_years' => $years,
                        'customer_count' => 0,
                        'total_revenue' => 0,
                        'average_contracts' => 0,
                        'customers_list' => []
                    ];
                }

                $yearlyDistribution[$years]['customer_count']++;
                $yearlyDistribution[$years]['total_revenue'] += $customer->total_revenue;
                $yearlyDistribution[$years]['customers_list'][] = [
                    'client_id' => $customer->client_id,
                    'contracts' => $customer->total_contracts,
                    'revenue' => $customer->total_revenue
                ];
            }

            // حساب المتوسطات وتنظيف البيانات
            foreach ($yearlyDistribution as $years => &$data) {
                if ($data['customer_count'] > 0) {
                    $data['average_revenue_per_customer'] = round($data['total_revenue'] / $data['customer_count'], 2);
                    $data['average_contracts'] = round(
                        collect($data['customers_list'])->avg('contracts'),
                        2
                    );
                }
                unset($data['customers_list']); // إزالة تفاصيل العملاء من النتيجة النهائية
            }

            // ترتيب النتائج حسب عدد السنوات
            ksort($yearlyDistribution);

            return [
                'status' => true,
                'data' => array_values($yearlyDistribution),
                'summary' => [
                    'total_customers' => $customerLifetimes->count(),
                    'average_lifetime_years' => round($customerLifetimes->avg('years_completed'), 2),
                    'total_revenue' => $customerLifetimes->sum('total_revenue'),
                    'revenue_per_year' => $customerLifetimes->sum('total_revenue') > 0 ?
                        round($customerLifetimes->sum('total_revenue') / max(1, $customerLifetimes->max('years_completed')), 2) : 0
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
