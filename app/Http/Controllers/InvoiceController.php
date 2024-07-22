<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\InvoiceDetail;
use App\Models\Quotation;
use App\Models\QuotationD;
use App\Models\RFQ;
use App\Models\RFQLineItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($stage = 1)
    {

        $quotation_ids =  QuotationD::where('rfq_id', 0)
            ->whereHas('quotation', function ($query) use ($stage) {
                $query->where('stage', $stage);
            })
            ->pluck('id');

        $quotationData = QuotationD::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('quotation', function ($query) use ($stage) {
                $query->where('stage', $stage);
            })
            ->where('rfq_id', 0)
            ->groupBy('product_id')
            ->with('product')
            ->get();


        return response()->json([
            'quotationData' => $quotationData,
            'quotation_ids' => $quotation_ids
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return Invoice::with('invoice_details.product', 'invoice_details')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validate products not empty and quotation_ids and stage {"products":[],"stage":"1","quotation_ids":[]}
        $request->validate([
            'products' => 'required|array|min:1',
            'stage' => 'required|integer',
            'quotation_ids' => 'required|array|min:1',
        ]);



        $user = Auth::guard('sanctum')->user();

        $rfq = new RFQ;
        $rfq->user_id = $user->id;
        $rfq->rfq_number = 'RFQ-' . time();
        $rfq->rfq_status = 'pending';
        $rfq->save();

        foreach ($request->products as $product) {
            $rfq_line_item = new RFQLineItem;
            $rfq_line_item->rfq_id = $rfq->id;
            $rfq_line_item->product_id = $product['product_id'];
            $rfq_line_item->quantity = $product['total_quantity'];
            $rfq_line_item->save();
        }

        QuotationD::whereIn('id', $request->quotation_ids)
            //  update rfq_id
            ->update(['rfq_id' => $rfq->id]);

        return $rfq->load('lineItems');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceRequest  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    function create_invoice(Request $req)
    {
        $user_ids = collect($req)->pluck('supplier.user_id')->toArray();

        $user_ids = array_unique($user_ids);

        $emails = User::whereIn('id', $user_ids)->pluck('email')->toArray();
        $man_rfq = 0;

        // TODO : valoadtion


        // $req->validate([
        //     'rfq_id' => 'required',
        //     'product_id' => 'required',
        //     'supplier_id' => 'required',
        //     'price' => 'required',
        // ], [
        //     'rfq_id.required' => 'يجب اختيار منتج واحد على الاقل',
        //     'product_id.required' => 'يجب اختيار منتج واحد على الاقل',
        //     'supplier_id.required' => 'يجب اختيار منتج واحد على الاقل',
        //     'price.required' => 'يجب اختيار منتج واحد على الاقل',
        // ]);


        foreach ($req->all() as $request) {



            $rfq_id = $request['rfq_id'];
            $man_rfq = $request['rfq_id'];
            $product_id = $request['product_id'];
            $price = $request['price'];
            $supplier_id = $request['supplier_id'];


            $qty = RFQLineItem::where('id', $rfq_id)->first()->quantity;

            $invoice = Invoice::where(['rfq_id' => $rfq_id])->first();

            if (!$invoice) {
                $invoice = new Invoice;
                $invoice->rfq_id = $rfq_id;
                $invoice->supplier_id = 0;
                $invoice->save();
            }


            $existingProduct = $invoice->invoice_details()
                ->where('product_id', $product_id)
                ->where('supplier_id', $supplier_id)
                ->first();

            if (!$existingProduct) {
                $invoice_details = new InvoiceDetail();

                $invoice_details->product_id = $product_id;
                $invoice_details->price = $price;
                $invoice_details->qty = $qty;
                $invoice_details->supplier_id = $supplier_id;
                $invoice_details->invoice_id = $invoice->id;
                $invoice_details->save();
            }
        }

        $rdq = RFQ::where('id', $man_rfq)->update(['rfq_status' => 'createInvoice']);

        MyHelper::pushNotification($emails, [
            'title' => 'فاتورة  جديدة',
            'body' => 'تم اعتماد فاتورة جديدة',
        ]);

        return response([
            'message' => 'تمت العملية بنجاح',
        ]);
    }
}
