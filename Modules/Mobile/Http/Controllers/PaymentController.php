<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceUpgrade;
use App\Service\GeneralLogService;
use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Mpdf\Tag\Main;

class PaymentController extends Controller
{
    private $secretKey;
    private $callbackUrl;

    public function __construct()
    {
        $this->secretKey = config('moyasar.secret_key');
        $this->callbackUrl = config('moyasar.callback_url');
    }

    public function initiatePayment(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'payment_type' => 'required|string',
            'payment_id' => 'required',
        ]);

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post('https://api.moyasar.com/v1/payments', [
                    'amount' => $request->amount * 100, // Convert to smallest currency unit
                    'currency' => 'SAR',
                    'description' => $request->description,
                    'callback_url' => $this->callbackUrl,
                    'source' => [
                        'type' => 'creditcard',
                        'name' => $request->source['name'],
                        'number' => $request->source['number'],
                        'cvc' => $request->source['cvc'],
                        'month' => $request->source['month'],
                        'year' => $request->source['year'],
                    ],
                ]);

            if ($request->payment_type == 'upgrade') {
                $upgrade = MaintenanceUpgrade::find($request->payment_id);
                $upgrade->update([
                    'status' => 'pending',
                    'payment_id' => $response->json()['id'],
                    'payment_method' => $response->json()['source']['type'],
                ]);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyPayment($id)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("https://api.moyasar.com/v1/payments/{$id}");

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        // Verify webhook signature if provided by Moyasar
        // Process the webhook data
        $paymentId = $request->id;
        $status = $request->status;
        $amount = $request->amount;

        // Update your database with payment status
        // Trigger any necessary business logic

        return response()->json(['status' => 'success']);
    }

    function paymentCallback()
    {
        // get payment id
        $id = request('id');
        $status = request('status');

        if ($status == 'failed') {
            MaintenanceUpgrade::where('payment_id', $id)->update([
                'status' => 'failed',
                'payment_method' => null,
            ]);

            GeneralLogService::log(MaintenanceUpgrade::where('payment_id', $id)->first(), 'upgrade_failed', 'تم رفض الدفع');
        }

        if ($status == 'paid') {
            MaintenanceUpgrade::where('payment_id', $id)->update([
                'status' => 'paid',
                'payment_method' => request('source.type'),
            ]);

            GeneralLogService::log(MaintenanceUpgrade::where('payment_id', $id)->first(), 'upgrade_paid', 'تم تأكيد الدفع');
        }
        return request();
    }
}
