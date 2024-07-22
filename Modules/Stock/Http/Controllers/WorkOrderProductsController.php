<?php

namespace Modules\Stock\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\ContractProductQuantity;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Employee;
use App\Models\LocationStatus;
use App\Models\TechniciansWorkOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrdersProduct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Stock\Http\Resources\WorkOrderResource;

class WorkOrderProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $model = WorkOrder::with('locationStatus')->get();

        //  return $model;

        return WorkOrderResource::collection($model);
    }
    public function employee($id)
    {

        $employees =  TechniciansWorkOrder::where('work_order_id', $id)
            ->with('employee')
            ->get();

        return $employees->map(function ($employee) {
            return $employee->employee;
        });
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        // Get the current products for the given work order ID
        $currentProducts = WorkOrdersProduct::where('work_order_id', $id)->get()->keyBy('product_id');

        // Extract the product IDs from the incoming products array
        // $incomingProductIds = collect($request['products'])->pluck('product_id')->toArray();

        // Handle updates and creates
        foreach ($request['products'] as $productData) {
            $productId = $productData['product_id'];
            $quantity = $productData['quantity'];

            if (isset($currentProducts[$productId])) {
                $currentProduct = $currentProducts[$productId];
                // Update the product quantity if it's different
                if ($currentProduct->qty != $quantity) {
                    $currentProduct->update(['qty' => $quantity]);
                }
                // Remove the product from the current products list (we have processed it)
                unset($currentProducts[$productId]);
            } else {
                // Create new product if it doesn't exist in the database
                WorkOrdersProduct::create([
                    'work_order_id' => $id,
                    'product_id' => $productId,
                    'qty' => $quantity,
                    'received' => $productData['received'] ?? 0,
                ]);
            }
        }

        // Delete products that are in the database but not in the incoming products array
        foreach ($currentProducts as $productToDelete) {
            $productToDelete->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم التعديل بنجاح',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $hasProductWithQtyGreaterThanZero =
            collect($request->products)->contains(function ($product) {
                return $product['qty'] > 0;
            });

        if (!$hasProductWithQtyGreaterThanZero) {
            return response()->json(['message' => 'يجب عليك صرف منتج واحد على الاقل'], 422);
        }

        // DB::beginTransaction();

        //   try {
        $workOrder = WorkOrder::find($request->id);

        $contract_id = $workOrder->contract_id;
        $stage_id = $workOrder->stage_id;

        $dispatch = new Dispatch();
        $dispatch->work_order_id = $request->id;
        $dispatch->stage_id = $stage_id;
        $dispatch->contract_id = $contract_id;
        $dispatch->user_id = auth('sanctum')->user()->id;
        $dispatch->employee_id = $request->employee_id;
        $dispatch->save();

        foreach ($request->products as $product) {

            if ($product['qty'] != 0) {

                $dispatchItem = new DispatchItem();
                $dispatchItem->dispatch_id = $dispatch->id;
                $dispatchItem->product_id = $product['product_id'];
                $dispatchItem->qty = $product['qty'];
                $dispatchItem->dispatch_sheet_id = $product['id'];
                $dispatchItem->save();

                $workOrdersProduct = WorkOrdersProduct::find($product['id']);
                $workOrdersProduct->received = $workOrdersProduct->received + $product['qty'];
                $workOrdersProduct->save();
            }
        }
        $workOrdersProducts = WorkOrdersProduct::where('work_order_id', $request->id)->get();

        $data = [];

        foreach ($workOrdersProducts as $product) {

            $data[] = [
                'id' => $product->id,
                'name' => $product->product->name,
                'product_id' => $product->product->id,
                'quantity' => $product->qty,
                'received' => $product->received,
                'qty' => 0,
            ];
        }


        $email = $workOrder->user->email;
        MyHelper::pushNotification([$email], [
            'title' => 'تم تسليم المنتجات',
            'body' => 'تم تسليم المنتجات' . $workOrder->id

        ]);

        $employeeMail = Employee::find($request->employee_id)->user->email;
        MyHelper::pushNotification([$employeeMail], [
            'title' => ' تسليم المنتجات',
            'body' => 'تم تسليم منتجات عليك الموافقه ' . $workOrder->id
        ]);

        //  DB::commit();

        return response($data);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json(['message' => 'حدث خطأ ما. الرجاء المحاولة لاحقًا.'], 500);
        // }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request)
    {
        $workOrder = WorkOrder::find($request->id);

        $contract = LocationStatus::findOrFail($workOrder->assignment_id);

        $contractData = [
            'client' => $contract->assignment->contract->locationDetection->client, // بيانات العميل
            'stopNumbers' => $contract->assignment->contract->stopsNumbers->name, // عدد الوقفات
            'elevatorTrip' => $contract->assignment->contract->elevatorTrip->name, // مشوار المصعد
            'elevatorType' => $contract->assignment->contract->elevatorType->name,  // نوع المصعد
            'doorSize' => $contract->assignment->contract->doorSize->name,  // مقاس فتحة الباب 
            'machineType' => $contract->assignment->contract->machineType->name,  //   نوع الماكينة 
            'machineSpeed' => $contract->assignment->contract->machineSpeed->name,  //   سرعة الماكينة 
            'controlCard' => $contract->assignment->contract->controlCard->name,  //   نوع الكنترول 
            'doorOpenDirection' => $contract->assignment->contract->outerDoorDirections->name,  //    اتجاه فتح الباب 
            'entranceNumber' => $contract->assignment->contract->entrancesNumber->name,  //   عدد المداخل  
            'stage' => $contract->assignment->stage,  //   المرحلة  
        ];

        $stage = $contract->assignment->stage_id;
        $elevator_type_id =  $contract->assignment->contract->elevator_type_id;
        $floor = $contract->assignment->contract->stop_number_id;

        if ($workOrder->products == 0) {

            $contractProductQuantity =  ContractProductQuantity::where([
                'elevator_type_id' => $elevator_type_id,
                'floor_id' => $floor,
                'stage_id' => $stage,
            ])
                ->with('product')
                ->get();


            foreach ($contractProductQuantity as $product) {

                $workOrderProduct = new WorkOrdersProduct();
                $workOrderProduct->work_order_id = $workOrder->id;
                $workOrderProduct->product_id = $product->product_id;
                $workOrderProduct->qty = $product->qty;
                $workOrderProduct->save();
            }
            $workOrder->products = 1;
            $workOrder->save();
        }

        $workOrdersProducts = WorkOrdersProduct::where('work_order_id', $workOrder->id)->get();

        $data = [];

        if ($workOrdersProducts) {
            foreach ($workOrdersProducts as $product) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->product->name ?? '',
                    'product_id' => $product->product->id ?? '',
                    'quantity' => $product->qty ?? '',
                    'received' => $product->received ?? '',
                    'qty' => 0,
                ];
            }
        }
        //return response($data);

        return response()->json([
            'contractData' => $contractData,
            'contractProducts' => $data
        ]);

        // $quotation = Quotation::with('quotation_d')
        //     ->where([
        //         'contract_id' => $workOrder->contract_id,
        //         'stage' => $workOrder->stage_id
        //     ])->first();

        // // return response($quotation, 500);

        // foreach ($quotation->quotation_d as $q) {
        //     $data[] = [
        //         'name' => $q->product->name,
        //         'quotation_d_id' => $q->id,
        //         'product_id' => $q->product_id,
        //         'quantity' => $q->quantity,
        //         'received' => $q->received,
        //         'qty' => 0,
        //     ];
        // }

        // return response($data);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
