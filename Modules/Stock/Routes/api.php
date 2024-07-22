<?php

use App\Helpers\MyHelper;
use App\Models\Contract;
use App\Models\ContractProductQuantity;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\LocationStatus;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationD;
use App\Models\Representative;
use App\Models\RFQ;
use App\Models\RFQLineItem;
use App\Models\TechniciansWorkOrder;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrdersProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\WorkOrderProductsController;

use function PHPSTORM_META\map;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// prefix stock with sanctum middleware
Route::middleware('auth:sanctum')->prefix('stock')->group(function () {

    Route::post('/update_qty_invoice/{invoice}', function (Request $request, Invoice $invoice) {

        foreach ($request->invoice_details as $details) {


            $invoice->invoice_details()->where('id', $details['id'])->update([
                'stock_qty' => $details['stock_qty']
            ]);
        }
        $invoice->status = 'partial';
        $invoice->save();

        $not_complete = $invoice->invoice_details->filter(function ($details) {
            return $details->stock_qty < $details->qty;
        })->isNotEmpty();

        if ($not_complete) {
            // get only emails of user where level purchases
            $emails = User::where('level', 'purchase')->get()->pluck('email');
            MyHelper::pushNotification([$emails], [
                'title' => 'تحديث الكميات',
                'body' => 'تحتاج ان تكمل الفاتورة',
            ], 'stock');
        } else {
            $invoice->status = 'complete';
            $invoice->save();
        }
        return $invoice;
    });

    // صرف المنتجات
    Route::get('work_orders_products', [WorkOrderProductsController::class, 'index']);
    Route::get('work_order_employees/{id}', [WorkOrderProductsController::class, 'employee']);
    Route::get('work_orders_products/{id}', [WorkOrderProductsController::class, 'show']);
    Route::post('work_orders_products/{id}/dispatch', [WorkOrderProductsController::class, 'store']);
    Route::put('work_orders_products/{id}', [WorkOrderProductsController::class, 'update']); //تحديث البيانات  


    Route::get('products', function () {
        return Product::with(['thsStage', 'elevatorType'])->get();
    });

    Route::post('rfq', function (Request $request) {
        // return $request;

        // validate products not empty and quotation_ids and stage {"products":[],"stage":"1","quotation_ids":[]}
        $request->validate([
            'products' => 'required|array|min:1',
        ]);

        $user = Auth::guard('sanctum')->user();

        $rfq = new RFQ();
        $rfq->user_id = $user->id;
        $rfq->rfq_number = 'RFQ-' . time();
        $rfq->rfq_status = 'pending';
        $rfq->save();

        foreach ($request->products as $product) {
            $rfq_line_item = new RFQLineItem();
            $rfq_line_item->rfq_id = $rfq->id;
            $rfq_line_item->product_id = $product['id'];
            $rfq_line_item->quantity = $product['qty'];
            $rfq_line_item->save();
        }
        return response([
            'message' => 'تم انشاء عرص السعر بنجاح'
        ]);
    });



    Route::get('products/{product}', function ($id) {

        $product = Product::with([
            'thsStage',
            'elevatorType',
            'dispatchItems',
            'dispatchItems.dispatch.user',
            'dispatchItems.dispatch.employee',
            'dispatchItems.dispatch',
            'invoiceDetail',
            'invoiceDetail.supplier',
            'invoiceDetail.invoice'
        ])->find($id);

        return $product;
    });
});

// Route::middleware('auth:sanctum')->get('/stock', function (Request $request) {
//     return $request->user();
// });
