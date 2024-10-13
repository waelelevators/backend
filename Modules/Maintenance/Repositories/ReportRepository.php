<?php

namespace Modules\Maintenance\Repositories;

use App\Models\Employee;
use App\Models\MaintenanceReport;
use App\Models\RequiredProduct;
use App\Service\GeneralLogService;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportRepository
{
    public function getAllReports()
    {
        return MaintenanceReport::all();
    }

    public function getReportById($id)
    {
        return MaintenanceReport::with('faults')->findOrFail($id);
    }

    public function create(array $data)
    {
        return MaintenanceReport::create($data);
    }

    public function assignTechnicianToReport(array $data)
    {

        MaintenanceReport::where('id', $data['maintenance_reports_id'])->update([
            'technician_id' => $data['technician_id'],
            'status' => 'assigned',

        ]);

        $report = MaintenanceReport::where('id', $data['maintenance_reports_id'])->first();

        $employee = Employee::find($data['technician_id']);

        $user_id = auth('sanctum')->user()->id;
        // make log for logService
        GeneralLogService::log($report, 'report_assigned', 'تم اسناد البلاغ إلى الفني ' . $employee->name, [
            'report_id' => $report->id,
            'technician_id' => $data['technician_id']
        ]);



        return $report;
    }

    public function addRequiredProductsToReport(array $data)
    {
        $requiredProduct = RequiredProduct::create($data);

        return MaintenanceReport::where('id', $data['maintenance_reports_id'])->update($data);
    }

    // public function getAllPaginated($perPage = 15)
    // {
    //     // return MaintenanceReport::paginate($perPage);
    // }

    public function addProblemsToReport(array $data)
    {
        $report = MaintenanceReport::findOrFail($data['maintenance_reports_id']);
        $report->problems = $data['problems'];
        $report->save();

        // لا حاجة لتحديث العلاقة هنا لأننا نستخدم حقل 'problems' مباشرة

        return $report;
    }

    public function approveReport(array $data)
    {
        $report = MaintenanceReport::where('id', $data['maintenance_reports_id'])->first();


        $report->status = 'approved';
        $report->save();
        //make log for logService
        GeneralLogService::log($report, 'report_approved', 'Report approved by admin', ['report_id' => $report->id]);

        return $report->load('logs');
    }
    public function addProductsToReport(array $data)
    {
        $report = MaintenanceReport::findOrFail($data['maintenance_reports_id']);

        $total = 0;
        $reportTax = 0;
        foreach ($data['products'] as $product) {

            $subtotal = $product['quantity'] * $product['price'];
            $tax = $subtotal * 0.15;
            $total += $subtotal;
            $reportTax += $tax;
            $report->requiredProducts()->create([
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'tax' => $tax,
                'subtotal' => $subtotal
            ]);
        }
        $report->price_without_tax = $total;
        $report->tax = $reportTax;
        $report->final_price = $total + $reportTax;
        $report->save();

        // make log for logService
        GeneralLogService::log($report,  'report_products_added', 'تم اضافة المنتجات إلى البلاغ', [
            'report_id' => $report->id,
        ]);

        return $report;
    }
}
