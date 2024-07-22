<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractProductQuantity;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationD;
use App\Models\Stage;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class QuotationController extends Controller
{

    public function index($contract_id, $stage_id)
    {
        //     // طلبات البضاعة 
        //     // عرض البضاعة المراد طلبها حسب المرحلة والعقد 

        $contract = Contract::where('id', $contract_id)->first();
        // $stage = $contract->stage_id;
        $stage = $stage_id;

        $productsQyt = ContractProductQuantity::where('stage', $stage)
            ->where('elevator_type_id', $contract->elevator_type_id)
            ->where('floor', $contract->stop_number_id)
            ->get();

        $quotation =  Quotation::where('contract_id', $contract_id)
            ->where('stage', $stage)
            ->with(['quotation_d'])
            ->get();


        if ($quotation->count() > 0) { // تم طلب بضاعة المرحلة المعينة مسبقا
            return [
                'quotation' => $quotation,
                'contract' => $contract,
                'products_qyt' => $productsQyt
            ];
        }

        $products =  Product::where('stage', $stage)->get(); // في حالة الرغبة في اضافة منتج اضافي
        return [
            'contract' => $contract,
            'products' => $products,
            'products_qyt' => $productsQyt
        ];
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $contract_id)
    {
        $contract = Contract::find($contract_id);
        // $stage = $contract->stage_id;
        $stage = $request->stage_id;
        $products =  $request->data;

        // check if stage has quotation already
        if ($stage == 1 && $contract->stage_one()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        } elseif ($stage == 2 && $contract->stage_two()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        } elseif ($stage == 3 && $contract->stage_three()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        }

        $payed = $contract->payments->sum('amount'); //اجمالي المبلغ المدفوع 
        $total = $contract->total;
        $required_percentage = Stage::find($stage)->required_percentage;

        if ($payed >= ($total * $required_percentage / 100)) {
        } else {
            return response([
                'message' => 'يجب عليك دفع المرحله لتتمكن من انشاء عرض سعر',
            ], 400);
        }

        $quotation = new Quotation();
        $quotation->contract_id = $contract_id;
        $quotation->stage = $stage;
        $quotation->save();

        foreach ($products as $product) {
            $quotation_product = new QuotationD();
            $quotation_product->quotation_id = $quotation->id;
            $quotation_product->product_id = $product['product_id'];
            $quotation_product->quantity = $product['qty'];
            $quotation_product->save();
        }
        return response()->json([
            'error' => false,
            'message' => 'تم اضافة العرض بنجاح',
            'data' => $quotation
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('purchase::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('purchase::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
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
