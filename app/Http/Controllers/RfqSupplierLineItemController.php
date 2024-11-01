<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\RfqSupplierLineItem;
use App\Http\Requests\StoreRfqSupplierLineItemRequest;
use App\Http\Requests\UpdateRfqSupplierLineItemRequest;
use App\Models\Notification;
use App\Models\Product;
use App\Models\RFQLineItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RfqSupplierLineItemController extends Controller
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
     * @param  \App\Http\Requests\StoreRfqSupplierLineItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $supplierId = $request->supplier_id; // المورد
        $rfqId = $request->rfq_id;
        $rfqLineItems = collect($request->items)->pluck('id')->toArray();

        // Check if the supplier has already submitted RFQ line items  for any of the products in the current RFQ
        $existingRfqSupplierLineItems = RfqSupplierLineItem::where('supplier_id', $supplierId)
            ->where('status', 'pending')
            ->where('rfq_id', $rfqId)
            ->get();

        $mergedRfqLineItems = [];

        foreach ($existingRfqSupplierLineItems as $item) {
            $mergedRfqLineItems = array_merge($mergedRfqLineItems, $item->rfq_line_items);
        }

        $matchedValues = array_intersect($mergedRfqLineItems, $rfqLineItems);

        $matchedValues =  array_unique($matchedValues);

        // if matchedValues has values get the names of products using this ids and return them
        if (!empty($matchedValues)) {
            $productIds = RFQLineItem::whereIn('id', $matchedValues)->pluck('product_id')->toArray();

            $productNames = Product::whereIn('id', $productIds)->pluck('name')->toArray();
            $productNamesString = implode(', ', $productNames);
            return response(['message' => $productNamesString], 400);
        }

        $user_id =  Auth::guard('sanctum')->user()->id;

        $rfqSupplierLineItem = new RfqSupplierLineItem();
        $rfqSupplierLineItem->rfq_id = $request->rfq_id;
        $rfqSupplierLineItem->supplier_id = $request->supplier_id;
        $rfqSupplierLineItem->rfq_line_items = $rfqLineItems;
        $rfqSupplierLineItem->user_id = $user_id;
        $rfqSupplierLineItem->save();

        $user = User::find($supplierId);

        MyHelper::pushNotification([$user->email], [
            'title' => 'تم ارسال عرض سعر جديد',
            'body' => 'تم ارسال عرض سعر الرجاء الاطلاع عليه'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة عرض السعر بنجاح',

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RfqSupplierLineItem  $rfqSupplierLineItem
     * @return \Illuminate\Http\Response
     */
    public function show(RfqSupplierLineItem $rfqSupplierLineItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RfqSupplierLineItem  $rfqSupplierLineItem
     * @return \Illuminate\Http\Response
     */
    public function edit(RfqSupplierLineItem $rfqSupplierLineItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRfqSupplierLineItemRequest  $request
     * @param  \App\Models\RfqSupplierLineItem  $rfqSupplierLineItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRfqSupplierLineItemRequest $request, RfqSupplierLineItem $rfqSupplierLineItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RfqSupplierLineItem  $rfqSupplierLineItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(RfqSupplierLineItem $rfqSupplierLineItem)
    {
        //
    }
}
