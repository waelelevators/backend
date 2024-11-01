<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\MaintenanceStoreResquest;
use App\Models\Maintenance;
use App\Models\MaintenancePayment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Maintenance\Http\Requests\MaintenancePaymentsStoreRequest;

class MaintenancePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return MaintenancePayment::orderByDesc('created_at')->get();
    }

    /**
     * Store a newly created resource in storage.
     * @param MaintenanceStoreResquest $request
     * @return Renderable
     */
    public function store(MaintenancePaymentsStoreRequest $request)
    {
        DB::transaction(function () use ($request) {


            if (isset($request['attachment'])) $attachment = ApiHelper::uploadBase64Image(
                $request['attachment'],
                'payments/maintenances'
            );
            else $attachment = '';

            $model = new MaintenancePayment();

            $model->m_id = $request['id'];
            $model->amount = $request['amount'];
            $model->attachment = $attachment;
            $model->user_id = Auth::guard('sanctum')->user()->id;
            $model->save();

            $maintenaceModel = Maintenance::find($request['id']);

            $maintenaceModel->m_status_id = 2; // تحويل العقد الي ساري
            $maintenaceModel->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة الدفعية بنجاح',

        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('maintenance::show');
    }
}
