<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\InternalDoorManufacturer;
use App\Models\ManufactureResponses;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Installation\Http\Requests\DoorManufactureStoreRequest;
use Modules\Installation\Http\Resources\DoorManufactureResource;

class InternalDoorManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $models = InternalDoorManufacturer::orderByDesc('created_at')->get();

        return DoorManufactureResource::collection($models);
    }

    public function changeStatus(Request $request)
    {
        $update = InternalDoorManufacturer::findOrFail($request->m_id);

        ApiHelper::changeResponseStatus($request->status, $request->m_id, 'Internal');

        // if ($request->status == 2) {
        //     $model = new ManufactureResponses();
        //     $model->accept_time =  now()->format('Y-m-d H:i:s');
        //     $model->m_id = $request->m_id;
        //     $model->user_id = Auth::guard('sanctum')->user()->id;
        //     $model->save();
        // } else if ($request->status == 3) {

        //     $model =  ManufactureResponses::where('m_id', $request->m_id)->get();
        //     $model->user_id = Auth::guard('sanctum')->user()->id;
        //     $model->ended_time =  now()->format('Y-m-d H:i:s');
        //     $model->save();
        // }
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
    public function store(DoorManufactureStoreRequest $request)
    {
        try {

            isset($request['order_attached']) ? $order_attached = ApiHelper::uploadBase64Image(
                $request['order_attached'],
                'manufacture/door'
            ) : $order_attached = '';

            $model = new InternalDoorManufacturer();
            $model->contract_id = $request['contract_id'];
            $model->doors_number = $request['doors_number'];
            $model->door_size_id = $request['door_size'];
            $model->started_date =  now()->format('Y-m-d H:i:s');
            $model->notes = $request['notes'];
            $model->order_attached = $order_attached;
            $model->door_cover_id = $request['door_cover'];;
            $model->status_id = 1;
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
        return InternalDoorManufacturer::findOrFail($id);
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
