<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\MaintenanceUpgrade;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Http\Requests\CreateReportRequest;
use Modules\Maintenance\Services\ReportService;
use Modules\Maintenance\Transformers\ReportResource;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $reports = $this->reportService->getAllReports();
        return ReportResource::collection($reports);
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $report = MaintenanceReport::with('requiredProducts', 'requiredProducts.product', 'technician', 'user')->findOrFail($id);
        return new ReportResource($report);
    }

    /**
     * إنشاء بلاغ جديد (المرحلة الأولى)
     */
    // public function createInitialReport(CreateReportRequest $request)
    public function createInitialReport(Request $request)
    {
        $request->validate([
            'maintenance_contract_id' => 'required',
            'notes' => 'required|string',
            'technician_id' => 'exists:employees,id',
        ]);
        $report = $this->reportService->createInitialReport($request->all());
        return response()->json([
            'message' => 'Report created successfully',
            'status' => 'success',
        ]);
    }

    /**
     * إنشاء بلاغ جديد (المرحلة الثانية)
     */
    public function assignTechnicianToReport(Request $request)
    {
        $validatedData = $request->validate([
            'maintenance_reports_id' => 'required|exists:maintenance_reports,id',
            'technician_id' => 'required|exists:employees,id',
        ]);

        $report = $this->reportService->assignTechnicianToReport($validatedData);
        return new ReportResource($report);
    }


    /**
     * اضافة الاسبيرات او المنتجات المستخدمه في الصيانه
     */
    public function addRequiredProductsToReport(Request $request)
    {
        $validatedData = $request->validate([
            'maintenance_reports_id' => 'required|exists:maintenance_reports,id',
            'required_products' => 'required|array',
            'required_products.*.product_id' => 'required|exists:products,id',
            'required_products.*.quantity' => 'integer',
            'required_products.*.price' => 'numeric',
        ]);

        $report = $this->reportService->addRequiredProductsToReport($validatedData);
        return new ReportResource($report);
    }

    /**
     * اضافة المشاكل التي واجهتها الصيانه
     */
    public function addProblemsToReport(Request $request)
    {
        $validatedData = $request->validate([
            'maintenance_reports_id' => 'required|exists:maintenance_reports,id',
            'problems' => 'required',
            // 'problems' => 'required|array',
            // 'problems.*' => 'required|string',
        ]);

        $report = $this->reportService->addProblemsToReport($validatedData);
        return response()->json(['message' => 'Problems added successfully', 'data' => $report]);
        return new ReportResource($report);
    }


    /**
     * تاكيد البلاغ
     */
    public function approveReport(Request $request)
    {
        $validatedData = $request->validate([
            'maintenance_reports_id' => 'required|exists:maintenance_reports,id',
        ]);
        $report = $this->reportService->approveReport($validatedData);
        return new ReportResource($report);
    }

    public function addProductsToReport(Request $request)
    {
        $validatedData = $request->validate([
            'maintenance_reports_id' => 'required|exists:maintenance_reports,id',
            'products' => 'required|array',
        ]);

        $report = $this->reportService->addProductsToReport($validatedData);
        return new ReportResource($report);
    }

    public function convertReportToUpgrade(Request $request, $reportId)
    {




        $report = MaintenanceReport::findOrFail($reportId);
        $maintenanceContract = $report->maintenanceContract;


        try {
            DB::beginTransaction();

            $upgrade = new MaintenanceUpgrade();
            $upgrade->maintenance_contract_id = $report->maintenance_contract_id;
            $upgrade->status = 'pending';
            $upgrade->template_id = $request->template_id;
            $upgrade->city_id = $report->maintenanceContract->city_id;
            $upgrade->user_id = auth()->id();
            $upgrade->neighborhood_id = $report->maintenanceContract->neighborhood_id;
            $upgrade->latitude = $maintenanceContract->latitude ?? $report->maintenanceContract->latitude;
            $upgrade->longitude = $maintenanceContract->longitude ?? $report->maintenanceContract->longitude;
            $upgrade->client_id = $report->maintenanceContract->client_id;
            $upgrade->elevator_type_id = $maintenanceContract->elevator_type_id;
            $upgrade->building_type_id = $maintenanceContract->building_type_id;
            $upgrade->stops_count = $maintenanceContract->stops_count;
            $upgrade->has_window = $maintenanceContract->has_window;
            $upgrade->has_stairs = $maintenanceContract->has_stairs;
            $upgrade->speed_id = $maintenanceContract->machine_speed_id;
            $upgrade->total = 0;
            $upgrade->tax = $report->tax ?? 0;
            $upgrade->net_price = $report->price_without_tax;
            $upgrade->total = $report->final_price ?? 0;
            $upgrade->user_id = auth('sanctum')->user()->id;
            $upgrade->save();

            if ($report->requiredProducts->count() > 0) {
                foreach ($report->requiredProducts as $product) {
                    $upgrade->requiredProducts()->create([
                        'product_id' => $product->product_id,
                        'quantity' => $product->quantity,
                        'price' => $product->price,
                        'tax' => $product->tax,
                    ]);
                }
            }



            $report->status = 'converted_to_upgrade';
            $report->save();

            $upgrade->total = $report->requiredProducts->sum('subtotal') + $report->tax ?? 0;
            $upgrade->save();



            $report->logs()->create([
                'action' => 'converted_to_upgrade',
                'description' => 'تم تحويل البلاغ إلى مقايسة',
            ]);

            $upgrade->logs()->create([
                'action' => 'created_from_report',
                'description' => 'تم إنشاء المقايسة من البلاغ رقم ' . $report->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحويل البلاغ إلى مقايسة بنجاح',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحويل البلاغ',
                'error' => $e->getMessage(),
                // 'error' => $e->getTraceAsString()
            ], 500);
        }
    }


    /**
     * Calculate the estimated price for a given upgrade
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calculateEstimatedPrice(Request $request)
    {
        $validator = $request->validate([
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'stops_count' => 'required|integer|min:2',
            'required_products' => 'required|array',
            'required_products.*.product_id' => 'required|exists:products,id',
            'required_products.*.quantity' => 'required|integer|min:1',
        ]);



        try {
            $basePrice = 0;
            $productsPrice = 0;

            $total = $basePrice + $productsPrice;
            $tax = $total * 0.15;
            $netPrice = $total + $tax;

            return response()->json([
                'status' => true,
                'data' => [
                    'base_price' => $basePrice,
                    'products_price' => $productsPrice,
                    'total' => $total,
                    'tax' => $tax,
                    'net_price' => $netPrice
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حساب السعر التقديري',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}