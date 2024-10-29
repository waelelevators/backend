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

        $report = MaintenanceReport::with('requiredProducts', 'requiredProducts.product', 'technician', 'user')->find($id);
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
            'technician_id' => 'required|exists:employees,id',
        ]);
        $report = $this->reportService->createInitialReport($request->all());
        return new ReportResource($report);
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
        // التحقق من صحة البيانات المدخلة
        $validator = $request->validate([
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'building_type_id' => 'required|exists:building_types,id',
            'stops_count' => 'required|integer|min:2',
            'has_window' => 'required|boolean',
            'has_stairs' => 'required|boolean',
            'site_images' => 'nullable|array',
            'site_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'speed_id' => 'required|exists:machine_speeds,id',
        ]);

        try {
            DB::beginTransaction();

            $report = MaintenanceReport::findOrFail($reportId);



            $upgrade = new MaintenanceUpgrade();
            $upgrade->maintenance_contract_id = $report->maintenance_contract_id;
            $upgrade->status = 'pending';
            $upgrade->city_id = $report->maintenanceContract->city_id;
            $upgrade->user_id = auth()->id();
            $upgrade->neighborhood_id = $report->maintenanceContract->neighborhood_id;
            $upgrade->latitude = $request->latitude ?? $report->maintenanceContract->latitude;
            $upgrade->longitude = $request->longitude ?? $report->maintenanceContract->longitude;
            $upgrade->client_id = $report->maintenanceContract->client_id;
            $upgrade->elevator_type_id = $request->elevator_type_id;
            $upgrade->building_type_id = $request->building_type_id;
            $upgrade->stops_count = $request->stops_count;
            $upgrade->has_window = $request->has_window;
            $upgrade->has_stairs = $request->has_stairs;
            $upgrade->speed_id = $request->speed_id;
            $upgrade->total = 0;
            $upgrade->tax = $report->tax ?? 0;
            $upgrade->net_price = $report->price_without_tax;
            $upgrade->total = $report->final_price;
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
                'status' => true,
                'message' => 'تم تحويل البلاغ إلى مقايسة بنجاح',
                'data' => [
                    'upgrade_id' => $upgrade->id,
                    'report_id' => $report->id
                ]
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