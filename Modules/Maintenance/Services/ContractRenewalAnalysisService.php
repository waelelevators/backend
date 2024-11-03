<?php

namespace Modules\Maintenance\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Maintenance\Entities\MaintenanceContractDetail;

class ContractRenewalAnalysisService
{
    /**
     * تحليل تفصيلي لفترات التجديد
     */
    public function analyzeRenewalPeriods()
    {
        try {
            // استخراج جميع فترات التجديد
            $renewalPeriods = DB::table('maintenance_contract_details as current')
                ->join('maintenance_contract_details as next', function ($join) {
                    $join->on('current.client_id', '=', 'next.client_id')
                        ->whereRaw('next.start_date > current.end_date');
                })
                ->select([
                    'current.client_id',
                    'current.id as current_contract_id',
                    'next.id as next_contract_id',
                    'current.end_date',
                    'next.start_date',
                    'current.cost as old_cost',
                    'next.cost as new_cost',
                    DB::raw('DATEDIFF(next.start_date, current.end_date) as gap_days')
                ])
                ->whereRaw('next.start_date = (
                    SELECT MIN(start_date)
                    FROM maintenance_contract_details
                    WHERE client_id = current.client_id
                    AND start_date > current.end_date
                )')
                ->get();

            // تصنيف فترات التجديد
            $categorizedRenewals = [
                'تجديد_فوري' => [
                    'وصف' => 'تجديد خلال 30 يوم من انتهاء العقد',
                    'عقود' => [],
                    'إحصائيات' => []
                ],
                'تجديد_عادي' => [
                    'وصف' => 'تجديد خلال 31-90 يوم من انتهاء العقد',
                    'عقود' => [],
                    'إحصائيات' => []
                ],
                'تجديد_متأخر' => [
                    'وصف' => 'تجديد خلال 91-180 يوم من انتهاء العقد',
                    'عقود' => [],
                    'إحصائيات' => []
                ],
                'تجديد_متأخر_جداً' => [
                    'وصف' => 'تجديد بعد أكثر من 180 يوم من انتهاء العقد',
                    'عقود' => [],
                    'إحصائيات' => []
                ]
            ];

            foreach ($renewalPeriods as $renewal) {
                $category = $this->getRenewalCategory($renewal->gap_days);

                // حساب نسبة التغيير في السعر
                $priceChangePercentage = (($renewal->new_cost - $renewal->old_cost) / $renewal->old_cost) * 100;

                $renewalData = [
                    'client_id' => $renewal->client_id,
                    'فترة_التجديد_بالأيام' => $renewal->gap_days,
                    'تاريخ_نهاية_العقد_السابق' => $renewal->end_date,
                    'تاريخ_بداية_العقد_الجديد' => $renewal->start_date,
                    'تكلفة_العقد_السابق' => $renewal->old_cost,
                    'تكلفة_العقد_الجديد' => $renewal->new_cost,
                    'نسبة_تغير_السعر' => round($priceChangePercentage, 2) . '%'
                ];

                $categorizedRenewals[$category]['عقود'][] = $renewalData;
            }

            // حساب الإحصائيات لكل فئة
            foreach ($categorizedRenewals as $category => &$data) {
                $contracts = collect($data['عقود']);

                if ($contracts->isNotEmpty()) {
                    $data['إحصائيات'] = [
                        'عدد_العقود' => $contracts->count(),
                        'متوسط_فترة_التجديد' => round($contracts->avg('فترة_التجديد_بالأيام'), 1),
                        'أقل_فترة_تجديد' => $contracts->min('فترة_التجديد_بالأيام'),
                        'أقصى_فترة_تجديد' => $contracts->max('فترة_التجديد_بالأيام'),
                        'متوسط_تغير_السعر' => round($contracts->avg(function ($contract) {
                            return (($contract['تكلفة_العقد_الجديد'] - $contract['تكلفة_العقد_السابق'])
                                / $contract['تكلفة_العقد_السابق']) * 100;
                        }), 2) . '%'
                    ];
                }
            }

            // حساب الإحصائيات العامة
            $totalRenewals = $renewalPeriods->count();
            $generalStats = [
                'إجمالي_التجديدات' => $totalRenewals,
                'توزيع_التجديدات' => array_map(function ($category) use ($totalRenewals) {
                    return [
                        'عدد' => count($category['عقود']),
                        'نسبة' => $totalRenewals > 0 ?
                            round((count($category['عقود']) / $totalRenewals) * 100, 2) . '%' : '0%'
                    ];
                }, $categorizedRenewals),
                'متوسط_فترة_التجديد_العامة' => round($renewalPeriods->avg('gap_days'), 1) . ' يوم'
            ];

            return [
                'status' => true,
                'data' => [
                    'تصنيف_فترات_التجديد' => $categorizedRenewals,
                    'إحصائيات_عامة' => $generalStats
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * تحديد فئة التجديد بناءً على عدد الأيام
     */
    private function getRenewalCategory($gapDays)
    {
        if ($gapDays <= 30) return 'تجديد_فوري';
        if ($gapDays <= 90) return 'تجديد_عادي';
        if ($gapDays <= 180) return 'تجديد_متأخر';
        return 'تجديد_متأخر_جداً';
    }

    public function analyzeRenewalPatterns()
    {
        
        try {
            // تحليل التجديدات لكل عميل
            $customerRenewals = MaintenanceContractDetail::select(
                'client_id',
                DB::raw('COUNT(*) as total_contracts'),
                DB::raw('MIN(start_date) as first_contract_date'),
                DB::raw('MAX(end_date) as latest_contract_end'),
                DB::raw('COUNT(DISTINCT YEAR(start_date)) as active_years'),
                DB::raw('SUM(cost) as total_revenue'),
                DB::raw('GROUP_CONCAT(DISTINCT YEAR(start_date) ORDER BY start_date) as contract_years')
            )
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->groupBy('client_id')
                ->having('total_contracts', '>', 0)
                ->get();

            // تحليل فجوات التجديد
            $renewalGaps = $this->analyzeRenewalGaps();

            // تحليل معدلات التجديد السنوية
            $yearlyRenewalRates = $this->calculateYearlyRenewalRates();

            // تحليل تأثير التكلفة على التجديد
            $costImpact = $this->analyzeCostImpactOnRenewal();

            return [
                'status' => true,
                'data' => [
                    'customer_renewal_summary' => $this->formatCustomerRenewalData($customerRenewals),
                    'renewal_gaps' => $renewalGaps,
                    'yearly_renewal_rates' => $yearlyRenewalRates,
                    'cost_impact' => $costImpact
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function analyzeRenewalGaps()
    {
        // تحليل الفجوات بين العقود
        return DB::table('maintenance_contract_details as current')
            ->join('maintenance_contract_details as next', function ($join) {
                $join->on('current.client_id', '=', 'next.client_id')
                    ->whereRaw('next.start_date > current.end_date');
            })
            ->select(
                'current.client_id',
                DB::raw('DATEDIFF(next.start_date, current.end_date) as gap_days'),
                DB::raw('current.end_date as contract_end'),
                DB::raw('next.start_date as next_contract_start'),
                'current.cost as previous_cost',
                'next.cost as new_cost'
            )
            ->whereRaw('next.start_date = (
                SELECT MIN(start_date)
                FROM maintenance_contract_details
                WHERE client_id = current.client_id
                AND start_date > current.end_date
            )')
            ->get()
            ->groupBy(function ($item) {
                // تصنيف الفجوات
                if ($item->gap_days <= 30) return 'immediate_renewal';
                if ($item->gap_days <= 90) return 'normal_renewal';
                if ($item->gap_days <= 180) return 'delayed_renewal';
                return 'late_renewal';
            });
    }

    private function calculateYearlyRenewalRates()
    {
        $years = MaintenanceContractDetail::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        $renewalRates = [];
        foreach ($years as $year) {
            $previousYearClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
                ->whereYear('end_date', $year - 1)
                ->first()
                ->total;

            $renewedClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
                ->whereYear('start_date', $year)
                ->whereIn('client_id', function ($query) use ($year) {
                    $query->select('client_id')
                        ->from('maintenance_contract_details')
                        ->whereYear('end_date', $year - 1);
                })
                ->first()
                ->total;

            if ($previousYearClients > 0) {
                $renewalRates[$year] = [
                    'year' => $year,
                    'previous_year_clients' => $previousYearClients,
                    'renewed_clients' => $renewedClients,
                    'renewal_rate' => round(($renewedClients / $previousYearClients) * 100, 2)
                ];
            }
        }
        $data = [];
        foreach ($renewalRates as $key => $value) {
            array_push($data, $value);
        }
        return $data;
    }

    private function analyzeCostImpactOnRenewal()
    {


        $impact =  DB::table('maintenance_contract_details as current')
            ->join('maintenance_contract_details as next', function ($join) {
                $join->on('current.client_id', '=', 'next.client_id')
                    ->whereRaw('next.start_date >= current.end_date');
            })
            ->select(
                DB::raw('
                    CASE
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 0 THEN "decrease"
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 10 THEN "slight_increase"
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 25 THEN "moderate_increase"
                        ELSE "significant_increase"
                    END as price_change_category
                '),
                DB::raw('COUNT(*) as total_renewals'),
                DB::raw('AVG(DATEDIFF(next.start_date, current.end_date)) as avg_renewal_gap_days'),
                DB::raw('AVG((next.cost - current.cost) / current.cost * 100) as avg_price_change_percentage')
            )
            ->whereRaw('next.start_date = (
                SELECT MIN(start_date)
                FROM maintenance_contract_details
                WHERE client_id = current.client_id
                AND start_date >= current.end_date
                AND id != current.id
            )')
            ->groupBy('price_change_category')
            ->get();

        $categoryTranslations = [
            'decrease' => 'انخفاض',
            'moderate_increase' => 'زيادة متوسطة',
            'significant_increase' => 'زيادة كبيرة',
            'slight_increase' => 'زيادة طفيفة'
        ];

        return $impact->map(function ($item) use ($categoryTranslations) {
            return [
                'category' => $categoryTranslations[$item->price_change_category],
                'renewals' => $item->total_renewals,
                'avg_gap' => round((float)$item->avg_renewal_gap_days, 2),
                'avg_change' => round((float)$item->avg_price_change_percentage, 1)
            ];
        });
    }

    private function formatCustomerRenewalData($customerRenewals)
    {
        $summary = [
            'total_customers' => $customerRenewals->count(),
            'renewal_patterns' => [
                'single_contract' => $customerRenewals->where('total_contracts', 1)->count(),
                'multiple_contracts' => $customerRenewals->where('total_contracts', '>', 1)->count(),
            ],
            'average_contracts_per_customer' => round($customerRenewals->avg('total_contracts'), 2),
            'customer_segments' => [
                'loyal_customers' => $customerRenewals->where('active_years', '>=', 3)->count(),
                'regular_customers' => $customerRenewals->whereBetween('active_years', [2, 2])->count(),
                'new_customers' => $customerRenewals->where('active_years', 1)->count(),
            ]
        ];

        // إضافة النسب المئوية
        $summary['customer_segments_percentage'] = [
            'loyal_customers' => round(($summary['customer_segments']['loyal_customers'] / $summary['total_customers']) * 100, 2),
            'regular_customers' => round(($summary['customer_segments']['regular_customers'] / $summary['total_customers']) * 100, 2),
            'new_customers' => round(($summary['customer_segments']['new_customers'] / $summary['total_customers']) * 100, 2)
        ];

        return $summary;
    }
}
