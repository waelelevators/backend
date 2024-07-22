<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\CabinManufacture;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Installation\Http\Requests\CabinManufactureStoreRequest;
use Modules\Installation\Http\Resources\CabinManufacturerResource;

class CabinManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $models =  CabinManufacture::orderByDesc('created_at')->get();

        return CabinManufacturerResource::collection($models);
    }

    public function changeStatus(Request $request)
    {
        $update = CabinManufacture::findOrFail($request->m_id);

        ApiHelper::changeResponseStatus($request->status, $request->m_id, 'Cabin');

        $update->status_id = $request['status'];
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
    public function store(CabinManufactureStoreRequest $request)
    {
        try {

            isset($request['order_attached']) ? $order_attached = ApiHelper::uploadBase64Image(
                $request['order_attached'],
                'manufacture/cabin'
            ) : $order_attached = '';

            $model = new CabinManufacture();
            $model->contract_id = $request['contract_id'];
            $model->weight_dbg = $request['weight_dbg'];
            $model->weight_location_id = $request['weight_location'];
            $model->cabin_dbg = $request['cabin_dbg'];
            $model->door_size_id = $request['door_size'];
            $model->notes = $request['notes'];
            $model->machine_chair = $request['machine_chair'];
            $model->door_direction_id = $request['door_direction'];
            $model->cover_type_id = $request['cover_type'];
            $model->machine_room_height = $request['machine_room_height'];
            $model->machine_room_width = $request['machine_room_width'];
            $model->machine_room_depth = $request['machine_room_depth'];
            $model->cabin_max_height = $request['cabin_max_height'];
            $model->last_floor_height = $request['last_floor_height'];

            $model->order_attached = $order_attached;
            $model->status_id = 1;
            $model->started_date =  now()->format('Y-m-d H:i:s');
            $model->user_id = Auth::guard('sanctum')->user()->id;
            $model->save();
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
            'message' => 'تم ارسال طلب تصنيع الكبينة الي المصنع',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return CabinManufacture::findOrFail($id);
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
