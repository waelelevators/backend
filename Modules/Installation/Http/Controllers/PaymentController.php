<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\MyHelper;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Installation\Http\Resources\PaymentResource;


class PaymentController extends Controller
{
    function index()
    {
        $payments = Payment::with(
            'contract'
        )
            ->orderBy('created_at', 'desc')
            ->get();

        return PaymentResource::collection($payments);
    }

    private function uploadBase64Pdf($base64Pdf, $path)
    {
        $pdfData = base64_decode(preg_replace('#^data:application/pdf;base64,#i', '', $base64Pdf));

        // Generate a unique filename
        $filename = uniqid() . '.pdf'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $pdfData);

        $fullPath = 'storage/' . $path . '/' . $filename;

        return $fullPath;
    }

    public function store(Request $request)
    {

        $data = [
            'amount' => $request->amount,
            'contract_id' => $request->contract_id,
            'files' => $request->files,
            'stage_id' => $request->stage,
        ];

        $validator = Validator::make($data, [
            'contract_id' => 'required',
            'stage_id' => 'required|integer|between:1,3',
            'amount' => [
                'required', 'numeric', 'gt:0',
                function ($attribute, $value, $fail) use ($request) {
                    $contract = Contract::find($request->contract_id);
                    $remainingAmount =  $contract->getRemainingAmountInStage($request->stage);
                    //   $isPreviousStagePaid =  $contract->isPreviousStagePaid($request->stage);

                    if ($value === null) {
                        $fail('المبلغ اجباري');
                    } elseif ($remainingAmount == 0) {
                        $fail('لقد تم دفع قسط المرحلة كاملأ');
                    }

                    // elseif (!$isPreviousStagePaid) {
                    //     $fail('الرجاء قم بدفع قسط المرحلة السابقة اولا');
                    // } 

                    elseif ($value > $remainingAmount) {
                        $fail('المبلغ المراد دفعه لايمكن ان يكون اكبر من متبقي الدفعة ' . $remainingAmount);
                    }
                }
            ],

        ], [

            'amount.required' => 'المبلغ اجبارى',
            'amount.gt' => 'يجب ان يكون المبلغ  اكبر من صفر',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response([
                'errors' => $errors
            ], 422);
        }


        DB::transaction(function () use ($request, $data) {

            $contract = Contract::find($request->contract_id);

            $filePath = $this->uploadBase64Pdf(
                $request['files'],
                'contract/payments'
            );

            $payment = new Payment;
            $payment->contract_id = $request->contract_id;
            $payment->stage_id = $request->stage;
            $payment->amount = $request->amount;
            $payment->attachments = $filePath ?? null;
            $payment->user_id = Auth::guard('sanctum')->user()->id;
            $payment->save();

            ApiHelper::LocationAssignment($contract, $contract->id);

            if ($contract->getIsReadyToStart($request->stage)) {

                // Queue notifications for performance improvement
                $emails = Cache::remember('installation_and_purchase_emails', 60, function () {
                    return User::whereIn('level', ['installations', 'purchases'])->pluck('email');
                });

                MyHelper::pushNotification($emails, [
                    'title' => 'تم دفع مرحله للعقد رقم #' . $contract->id,
                    'body' => 'تم دفع المرحله ' . $contract->stage_id
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة الدفعية بنجاح'
        ]);
    }
}
