<?php

namespace App\Http\Controllers;

use App\Models\RFQ;
use App\Models\RfqSupplierLineItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class RFQController extends Controller
{

    function rfq_products(RFQ $rFQ, $supplier_id = null)
    {
        $user =  Auth::guard('sanctum')->user();

        if ($user->level == 'supplier') {
            // rfq_line_items
            $ids =  RfqSupplierLineItem::where('supplier_id', $user->supplier->id)
                ->where('rfq_id', $rFQ->id)
                ->where('status', 'pending')
                ->first()->rfq_line_items;

            return $rFQ
                ->load([
                    'lineItems' => function ($query) use ($ids) {
                        $query->whereIn('id', $ids)->with('product');
                    },
                    'user'
                ]);
        } else {

            $items =  RfqSupplierLineItem::where('supplier_id', $supplier_id)
                ->where('rfq_id', $rFQ->id)
                ->where('status', 'pending')
                ->first();
            if ($items) {
                $ids = $items->rfq_line_items;

                return $rFQ
                    ->load([
                        'lineItems' => function ($query) use ($ids) {
                            $query->whereIn('id', $ids)->with('product');
                        },
                        'user',
                    ]);
            } else {
                return response([
                    'isError' => true,
                    'error' => 'there is no data',
                    'message' => 'لاتوجد فواتير'
                ]);
            }
        }
    }
}
