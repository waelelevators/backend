<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\MaintenanceReport;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
}
