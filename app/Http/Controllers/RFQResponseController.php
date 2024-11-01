<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\RFQResponse;
use App\Http\Requests\StoreRFQResponseRequest;
use App\Http\Requests\UpdateRFQResponseRequest;
use App\Models\RfqSupplierLineItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RFQResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRFQResponseRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(StoreRFQResponseRequest $request)
    public function store(Request $request)
    {

        $request->validate([
            'invoice.*.prices.*.price' => 'required|numeric|min:0', // Add any other validation rules as needed
        ], [
            'invoice.*.prices.*.price.required' => 'يجب عليك ادخال السعر',
        ]);
        $user = Auth::guard('sanctum')->user();
        $supplier_id = $user->level === 'supplier' ? $user->supplier->id ?? 1 : $request->supplierId;

        foreach ($request->invoice as $data) {
            if (isset($data['prices']) && is_array($data['prices'])) {
                foreach ($data['prices'] as $priceData) {
                    if ($priceData['price'] !== null) {
                        RFQResponse::create([
                            'rfq_id' => $data['rfq_id'],
                            'supplier_id' => $supplier_id,
                            'rfq_line_item_id' => $data['id'],
                            'product_id' => $data['product_id'],
                            'price' => $priceData['price'],
                            'note' => $priceData['note'],
                        ]);
                    }
                }
            }
        }

        $lineItem = RfqSupplierLineItem::where(['rfq_id' => $request->id, 'supplier_id' => $supplier_id])->first();
        $lineItem->status = 'completed';
        $lineItem->save();

        // $rfq = FRQ

        // $supplierEmail =  User::where('level', 'purchases')->pluck('email')->toArray();
        // if ($supplierEmail) {
        //     MyHelper::pushNotification($supplierEmail, [
        //         'title' => 'تم ادخال اسعار لفاتورة',
        //         'body' => 'تم ادخال اسعار لفاتورة' . $request->id . ' المورد ' . $user->supplier->name,
        //     ]);
        // }

        return response()->json(['message' => 'Data saved successfully']);
    }
}
