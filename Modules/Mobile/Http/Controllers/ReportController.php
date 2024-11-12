<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\Fault;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\Product;
use App\Models\RequiredProduct;
use App\Service\GeneralLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Transformers\ReportResource;

class ReportController extends Controller
{
    public function index()
    {
        $reports =  MaintenanceReport::with(
            'maintenanceContract',
            'maintenanceContract.activeContract',
            'maintenanceContract.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'requiredProducts',
            'logs'
        )
            ->orderBy('id', 'desc')
            ->where('technician_id', auth()->user()->id)
            ->where('status', 'assigned')
            ->get();


        $reports = $reports->map(function ($report) {
            $logs = $report->logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'comment' => $log->comment,
                    'action' => $log->action,
                    'date' => $log->created_at ? $log->created_at : null,
                    'user' => $log->user->name ?? null,
                ];
            });
            return [
                'id' => $report->id,
                'notes' => $report->notes,
                'status' => $report->status,
                'created_at' => $report->created_at,
                'products' => $report->requiredProducts,
                'city' => $report->maintenanceContract->city->name ?? null,
                'neighborhood' => $report->maintenanceContract->neighborhood->name ?? null,
                'logs' => $logs ?? [],
                'client' => [
                    'name' => $report->maintenanceContract->client->name ?? null,
                    'phone' => $report->maintenanceContract->client->phone ?? null,
                ],
                'start_date' => $report->maintenanceContract->activeContract->start_date ?? null,
                'end_date' => $report->maintenanceContract->activeContract->end_date ?? null,
            ];
        });
        return ['data' => $reports];
    }

    // store
    public function store(Request $request)
    {
        return $request->all();
    }


    function show($id)
    {
        $report =  MaintenanceReport::with(
            'maintenanceContract',
            'maintenanceContract.activeContract',
            'maintenanceContract.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'requiredProducts',
            'logs'
        )
            ->find($id);

        $logs = $report->logs->map(function ($log) {
            return [
                'id' => $log->id,
                'comment' => $log->comment,
                'action' => $log->action,
                'date' => $log->created_at ? $log->created_at : null,
                'user' => $log->user->name ?? null,
            ];
        });
        $data =  [
            'id' => $report->id,
            'notes' => $report->notes,
            'faults' => $report->faults,
            'status' => $report->status,
            'images' => $report->images,
            'created_at' => $report->created_at,
            'products' => $report->requiredProducts,
            'city' => $report->maintenanceContract->city->name ?? null,
            'neighborhood' => $report->maintenanceContract->neighborhood->name ?? null,
            'logs' => $logs,
            'client' => [
                'name' => $report->maintenanceContract->client->name ?? null,
                'phone' => $report->maintenanceContract->client->phone ?? null,
            ],
            'start_date' => $report->maintenanceContract->activeContract->start_date ?? null,
            'end_date' => $report->maintenanceContract->activeContract->end_date ?? null,
        ];

        return ['data' => $data];
    }

    // contractors
    public function contractors()
    {
        $contractors = MaintenanceContract::with('client', 'city', 'neighborhood', 'activeContract')
            ->where('contract_type', '!=', 'draft')
            ->take(10)->get();

        $contractors = $contractors->map(function ($contractor) {
            return [
                'id' => $contractor->id,
                'customerName' => $contractor->client->name ?? 'غير معروف',
                'address' => $contractor->city->name . ',' . $contractor->neighborhood->name ?? 'غير معروف',
                'phone' => $contractor->client->phone ?? 'غير معروف',
                'contractType' => $contractor->activeContract->type ?? null,
                'startDate' => $contractor->activeContract->start_date ?? null,
                'endDate' => $contractor->activeContract->end_date ?? null,
            ];
        });
        return ['data' => $contractors];
    }


    public function technicianReports(Request $request)
    {

        $user_id = auth('sanctum')->user()->id;

        $maintenance_contract_detail_id = MaintenanceContractDetail::where('maintenance_contract_id', $request->maintenance_contract_id)
            ->latest('created_at')
            ->first()
            ?->id;


        MaintenanceReport::create([
            'technician_id' => $user_id,
            'status' => 'assigned',
            'technician_id' => $user_id,
            'notes' => $request->notes,
            'maintenance_contract_id' => $request->maintenance_contract_id,
            'maintenance_contract_detail_id' => $maintenance_contract_detail_id,
        ]);
        return response()->json(['message' => 'Report created successfully']);
    }

    // updateFaults
    public function updateFaults(Request $request, $id)
    {
        $report = MaintenanceReport::findOrFail($id);
        $fault_id = $request->fault_id;

        $currentFaults = $report->problems ?? [];

        // if fault_id exists in currentFaults remove it else add it
        if (in_array($fault_id, $currentFaults)) {
            $currentFaults = array_diff($currentFaults, [$fault_id]);
        } else {
            $currentFaults[] = $fault_id;
        }

        $report->problems = $currentFaults;
        $report->save();

        $faults = Fault::find($fault_id);

        GeneralLogService::log($report, 'faults_updated', "تمت اضافة مشكله جديده " .
            $faults->name ?? null . ' ' . $faults->description ?? null . "
        ", [
            'faults' => $currentFaults,

        ]);
        return response()->json([
            'message' => 'Faults updated successfully',
            'faults' => Fault::whereIn('id', $currentFaults)->get()
        ]);
    }

    // addProducts
    public function UpdateProducts(Request $request, $id)
    {
        $report = MaintenanceReport::findOrFail($id);

        foreach ($request->products as $product) {

            $product_id =  $product['id'] ?? null;
            if ($product_id) {
                $requiredProduct = RequiredProduct::find($product_id);

                $requiredProduct->quantity = $product['quantity'];
                $requiredProduct->subtotal = $product['subtotal'];
                $requiredProduct->save();
                if ($product['quantity'] == 0) {
                    $requiredProduct->delete();
                }
            } else {

                $price = $product['product']['sale_price'];
                $subtotal = $price * $product['quantity'];
                $tax = $subtotal * 0.15;


                $report->requiredProducts()->create([
                    'product_id' => $product['product']['id'],
                    'quantity' => $product['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                ]);
            }
        }


        GeneralLogService::log($report,  'report_products_added', 'تم اضافة المنتجات للبلاغ', [
            'report_id' => $report->id,
        ]);

        return response()->json([
            'message' => 'Products updated successfully',
            'products' => $report->required_products
        ]);
    }

    // updateStatus
    public function updateStatus(Request $request)
    {
        $report = MaintenanceReport::findOrFail($request->id);
        $report->status = 'waiting_approval';
        $report->save();

        return $this->show($request->id);
    }


    public function removeImage(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string',
            'report_id' => 'required|integer',
        ]);

        $report = MaintenanceReport::findOrFail($request->report_id);
        $report->images = array_filter($report->images, function ($image) use ($request) {
            return $image !== $request->image_url;
        });
        $report->save();

        if ($request->image_url && Storage::exists($request->image_url)) {
            try {
                Storage::delete($request->image_url);
            } catch (\Exception $e) {
                Log::error('Failed to delete file: ' . $e->getMessage());
                // Handle the error as needed, e.g., return an error response
            }
        }

        return response()->json([
            'message' => 'Image removed successfully',
            'images' => $report->images,
        ], 200);
    }
}
