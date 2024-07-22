<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\RFQ;
use App\Models\RfqSupplierLineItem;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RFQController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $user =  Auth::guard('sanctum')->user();

        if ($user->level == 'supplier') {

            $ids =  RfqSupplierLineItem::where('supplier_id', $user->supplier->id)
                ->where('status', 'pending')
                ->pluck('rfq_id');

            return RFQ::with('user')
                ->whereIn('id', $ids)
                ->get();
                
        } else {
            return RFQ::with('user')->orderByDesc('created_at')->get();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('purchase::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function pdf(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $rFQ = RFQ::where('id', $id)->first(); // Apply condition to fetch only the RFQ with id = 1

        //  return $rFQ;
        return $rFQ->load([
            'lineItems',
            'lineItems.product',
            'responses',
            // 'products',
            'lineItems.responses',
        ]);
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
