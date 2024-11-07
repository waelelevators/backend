<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceUpgrade;
use App\Models\RequiredProduct;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpgradesAnalyticsController extends Controller
{
    /**
     * تحليل إحصائيات التحديثات الشاملة
     */
    public function getUpgradesOverview(Request $request)
    {
        return response()->json([
            'summary' => $this->getUpgradesSummary($request),
            'parts_analysis' => $this->getPartsAnalysis($request),
            'geographical_analysis' => $this->getGeographicalAnalysis($request),
            'timeline_analysis' => $this->getTimelineAnalysis($request)
        ]);
    }

    /**
     * ملخص التحديثات الإجمالي
     */
    private function getUpgradesSummary(Request $request)
    {
        return MaintenanceUpgrade::query()
            ->select(
                DB::raw('COUNT(*) as total_upgrades'),
                DB::raw('SUM(total) as total_cost'),
                DB::raw('AVG(total) as average_cost'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('COUNT(DISTINCT city_id) as cities_count'),
                DB::raw('COUNT(DISTINCT neighborhood_id) as neighborhoods_count')
            )
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->first();
    }

    /**
     * تحليل قطع الغيار في التحديثات
     */
    private function getPartsAnalysis(Request $request)
    {
        return DB::table('required_products as rp')
            ->join('maintenance_upgrades as mu', function ($join) {
                $join->on('mu.id', '=', 'rp.productable_id')
                    ->where('rp.productable_type', '=', MaintenanceUpgrade::class);
            })
            ->join('products as p', 'p.id', '=', 'rp.product_id')
            ->select(
                'p.id',
                'p.name',
                DB::raw('SUM(rp.quantity) as total_quantity'),
                DB::raw('SUM(rp.subtotal) as total_cost'),
                DB::raw('AVG(rp.price) as average_price'),
                DB::raw('COUNT(DISTINCT mu.id) as upgrades_count')
            )
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('mu.city_id', $request->city_id);
            })
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('total_quantity')
            ->get();
    }

    /**
     * التحليل الجغرافي للتحديثات
     */
    private function getGeographicalAnalysis(Request $request)
    {
        // تحليل حسب المدن
        $cityAnalysis = MaintenanceUpgrade::query()
            ->select(
                'city_id',
                DB::raw('COUNT(*) as upgrades_count'),
                DB::raw('SUM(total) as total_cost'),
                DB::raw('AVG(total) as average_cost')
            )
            ->with('city:id,name')
            ->groupBy('city_id')
            ->get();

        // تحليل حسب الأحياء
        $neighborhoodAnalysis = MaintenanceUpgrade::query()
            ->select(
                'neighborhood_id',
                'city_id',
                DB::raw('COUNT(*) as upgrades_count'),
                DB::raw('SUM(total) as total_cost'),
                DB::raw('AVG(total) as average_cost')
            )
            ->with(['neighborhood:id,name', 'city:id,name'])
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->groupBy('neighborhood_id', 'city_id')
            ->get();

        return [
            'cities' => $cityAnalysis,
            'neighborhoods' => $neighborhoodAnalysis
        ];
    }

    /**
     * التحليل الزمني للتحديثات
     */
    private function getTimelineAnalysis(Request $request)
    {
        return MaintenanceUpgrade::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as upgrades_count'),
                DB::raw('SUM(total) as total_cost'),
                DB::raw('AVG(total) as average_cost'),
                DB::raw('COUNT(DISTINCT neighborhood_id) as neighborhoods_count')
            )
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * تحليل تفصيلي للحي
     */
    public function getNeighborhoodUpgradesAnalysis(Request $request, $neighborhoodId)
    {
        // تحليل التحديثات في الحي
        $upgradesAnalysis = MaintenanceUpgrade::query()
            ->where('neighborhood_id', $neighborhoodId)
            ->select(
                DB::raw('COUNT(*) as total_upgrades'),
                DB::raw('SUM(total) as total_cost'),
                DB::raw('AVG(total) as average_cost'),
                'elevator_type_id',
                'building_type_id'
            )
            ->with(['elevatorType:id,name', 'buildingType:id,name'])
            ->groupBy('elevator_type_id', 'building_type_id')
            ->get();

        // تحليل قطع الغيار في الحي
        $partsAnalysis = DB::table('maintenance_upgrades as mu')
            ->join('required_products as rp', function ($join) {
                $join->on('mu.id', '=', 'rp.productable_id')
                    ->where('rp.productable_type', '=', MaintenanceUpgrade::class);
            })
            ->join('products as p', 'p.id', '=', 'rp.product_id')
            ->where('mu.neighborhood_id', $neighborhoodId)
            ->select(
                'p.name',
                DB::raw('SUM(rp.quantity) as total_quantity'),
                DB::raw('SUM(rp.subtotal) as total_cost'),
                DB::raw('AVG(rp.price) as average_price')
            )
            ->groupBy('p.id', 'p.name')
            ->get();

        return response()->json([
            'upgrades_analysis' => $upgradesAnalysis,
            'parts_analysis' => $partsAnalysis
        ]);
    }

    /**
     * تحليل الكفاءة المالية للتحديثات
     */
    public function getFinancialEfficiencyAnalysis(Request $request)
    {
        $analysis = MaintenanceUpgrade::query()
            ->select(
                'city_id',
                DB::raw('COUNT(*) as upgrades_count'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('AVG(total) as average_revenue'),
                DB::raw('MIN(total) as min_revenue'),
                DB::raw('MAX(total) as max_revenue')
            )
            ->with('city:id,name')
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->groupBy('city_id')
            ->get()
            ->map(function ($item) {
                $item->efficiency_score = ($item->total_revenue - $item->total_discount) / $item->upgrades_count;
                return $item;
            });

        return response()->json($analysis);
    }

    /**
     * تحليل اتجاهات التحديثات
     */
    public function getUpgradeTrendsAnalysis(Request $request)
    {
        // تحليل الاتجاهات الشهرية
        $monthlyTrends = MaintenanceUpgrade::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'),
                DB::raw('AVG(total) as average')
            )
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // تحليل أنواع المصاعد
        $elevatorTypeTrends = MaintenanceUpgrade::query()
            ->select(
                'elevator_type_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'),
                DB::raw('AVG(total) as average')
            )
            ->with('elevatorType:id,name')
            ->groupBy('elevator_type_id')
            ->get();

        return response()->json([
            'monthly_trends' => $monthlyTrends,
            'elevator_type_trends' => $elevatorTypeTrends
        ]);
    }
}
