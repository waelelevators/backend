<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\RFQ;
use App\Models\Supplier;
use App\Helpers\PdfHelper;
use App\Models\RFQLineItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\RfqSupplierLineItem;
use Illuminate\Contracts\Support\Renderable;

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function invoice($rfq_id, $supplier_id)
    {

        $items =  RfqSupplierLineItem::with('rfq','user')
            ->where('supplier_id', $supplier_id)
            ->where('rfq_id', $rfq_id)
            ->where('status', 'pending')
            ->first();

        if ($items) {

            $ids = $items->rfq_line_items;
            $supplierName = Supplier::findOrFail($supplier_id)->name; // اسم المورد
            $products = RFQLineItem::whereIn('id', $ids)->get();  // المنتجات المراد عرض سعر لها

            $data = [
                'SupplierName' => $supplierName,
                'Products' => $products,
                'RfqNumber' => $items->rfq->rfq_number,
                'Date' => date('Y-m-d', strtotime($items->created_at))
            ];

            // $name = optional($contract?->createdBy?->employee)->name; // محتاجة الي تغيير 

            $name = 'hani';

            //$doneby = 'hani';

            $doneby = __('pdf.Export By') . ($name ?? '');

            $mpdf = PdfHelper::generateContractPDF($name, $doneby);

            $mpdf->WriteHTML(view(
                'purchase::pdf.invoice',
                compact('data', 'products')
            )->render());

            return $mpdf->Output();
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
    public function store(Request $request)
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
