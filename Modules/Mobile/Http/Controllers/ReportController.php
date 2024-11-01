<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\Fault;
use App\Models\MaintenanceContract;
use App\Models\Product;
use App\Models\RequiredProduct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
            'requiredProducts'
        )
            ->orderBy('id', 'desc')
            ->where('technician_id', auth()->user()->id)
            ->where('status', '!=', 'completed')
            ->get();

        $reports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'notes' => $report->notes,
                'status' => $report->status,
                'created_at' => $report->created_at,
                'products' => $report->requiredProducts,
                'city' => $report->maintenanceContract->city->name ?? null,
                'neighborhood' => $report->maintenanceContract->neighborhood->name ?? null,
                'client' => [
                    'name' => $report->maintenanceContract->client->name ?? null,
                    'phone' => $report->maintenanceContract->client->phone ?? null,
                ],
                'start_date' => $report->maintenanceContract->activeContract->start_date ?? null,
                'end_date' => $report->maintenanceContract->activeContract->end_date ?? null,
            ];
        });
        return ['data' => $reports];


        return $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'status' => $report->status,
                'client' => [
                    'id' => $report->maintenanceContract->client->id,
                    'name' => $report->maintenanceContract->client->name,
                    'phone' => $report->maintenanceContract->client->phone,
                ],
                'faults' => $report->faults->map(function ($fault) {
                    return [
                        'id' => $fault->id,
                        'name' => $fault->name,
                        'description' => $fault->description,
                    ];
                }),
                'active_contract' => [
                    'start_date' => $report->maintenanceContract->activeContract->start_date,
                    'end_date' => $report->maintenanceContract->activeContract->end_date,
                ],
            ];
        });
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
            'requiredProducts'
        )
            ->find($id);


        $data =  [
            'id' => $report->id,
            'notes' => $report->notes,
            'faults' => $report->faults,
            'status' => $report->status,
            'created_at' => $report->created_at,
            'products' => $report->requiredProducts,
            'city' => $report->maintenanceContract->city->name ?? null,
            'neighborhood' => $report->maintenanceContract->neighborhood->name ?? null,
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
                'address' => $contractor->city->name . ',
                 ' . $contractor->neighborhood->name ?? 'غير معروف',
                'phone' => $contractor->client->phone ?? 'غير معروف',
                'contractType' => $contractor->activeContract->type ?? null,
                'startDate' => $contractor->activeContract->start_date ?? null,
                'endDate' => $contractor->activeContract->end_date ?? null,
            ];
        });
        return ['data' => $contractors];
    }
    // technicianReports
    public function technicianReports(Request $request)
    {

        $user_id = auth('sanctum')->user()->id;
        MaintenanceReport::create([
            'technician_id' => $user_id,
            'status' => 'open',
            'technician_id' => $user_id,
            'notes' => $request->notes,
            'maintenance_contract_id' => $request->maintenance_contract_id,
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
}
