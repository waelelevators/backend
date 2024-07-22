<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\ExternalDoorManufacturer;
use App\Models\ExternalDoorSpecificationManufacturer;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Installation\Http\Requests\ExternalDoorManufactureStoreRequest;
use Modules\Installation\Http\Resources\ExternalDoorManufacturerResource;

class ExternalDoorManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $models = ExternalDoorManufacturer::orderByDesc('created_at')->get();


        return ExternalDoorManufacturerResource::collection($models);
    }
    public function changeStatus(Request $request)
    {
        $update = ExternalDoorManufacturer::findOrFail($request->m_id);

        ApiHelper::changeResponseStatus($request->status, $request->m_id, 'External');

        $update->status_id = $request->status;
        $update->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل الطلب',
        ]);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ExternalDoorManufactureStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {

                if (isset($request['order_attached'])) // مرفقات 
                    $order_attached = ApiHelper::uploadBase64Image(
                        $request['order_attached'],
                        'manufacture'
                    );

                $externalDoorSpecifications = is_array($request['externalDoorSpecifications']) ?
                    $request['externalDoorSpecifications'] :
                    array($request['externalDoorSpecifications']); // مواصفات الباب الخارجي

                $model = new ExternalDoorManufacturer();

                $model->contract_id =  $request['contract_id'];
                $model->door_size_id =  $request['door_size'];
                $model->doors_number =  $request['doors_number'];
                $model->notes =  $request['notes'] ?? '';
                $model->order_attached =  $order_attached;
                $model->status_id =  1;
                $model->started_date = now()->format('Y-m-d H:i:s');
                $model->user_id = Auth::guard('sanctum')->user()->id;
                $model->save();

                foreach ($externalDoorSpecifications as $externalSpecification) {

                    $externalSpecificationModel = new ExternalDoorSpecificationManufacturer();

                    $externalSpecificationModel->ex_do_ma_id =  $model->id;

                    $externalSpecificationModel->doors_number =  $externalSpecification['door_number'];
                    $externalSpecificationModel->do_spec_id =  $externalSpecification['outer_door_directions'];
                    $externalSpecificationModel->door_cover_id =  $externalSpecification['door_cover'];
                    $externalSpecificationModel->save();
                }
            });
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // This is a duplicate entry error
                return response()->json([
                    'message' => 'Record with this contract_id already exists',
                    'status' => 'exists'
                ], 409);
            } else {
                // Handle other database errors
                return response()->json(['message' => 'Database error occurred: ' . $e->getMessage()], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم ارسال طلب التلبيس الي المصنع',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('installation::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('installation::edit');
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
