<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\HandOverItem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandOverController extends Controller
{

    public function update(Request $request, $id)
    {

        $model =  HandOverItem::findOrFail($id);
        $model->status = 1;
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم التحديث بنجاح',
        ]);
    }
    public function store(Request $request)
    {
        $externalDoors = is_array($request['externalDoorSpecifications']) ?
            $request['externalDoorSpecifications'] : array($request['externalDoorSpecifications']);

        // Filter the array to select only 'door_cover_name' and 'doors_number'
        $filteredDoors = array_map(function ($door) {
            return [
                'doors_number' => $door['doors_number'],
                'external_door_name' => $door['external_door_specification']['name'],
                'door_cover_name' => $door['door_cover']['name'],

            ];
        }, $externalDoors);

        $handOverModel = new HandOverItem();

        $handOverModel->status         = 0;
        $handOverModel->type           = 'External';
        $handOverModel->m_responses_id = $request->m_responses_id;
        $handOverModel->work_order_id  = $request->work_order_id;
        $handOverModel->employee_id    = $request->employee_id;
        $handOverModel->item_data      = json_encode($filteredDoors);


        $handOverModel->user_id = Auth::guard('sanctum')->user()->id;
        $handOverModel->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم اسناد الابواب الي الفني بنجاج في انتظار تاكيد الاستلام',
        ]);
    }

    public function show($id)
    {
        try {
            // Attempt to find the HandOverItem by work_order_id
            $model = HandOverItem::with('employee')
                ->where('work_order_id', $id)->firstOrFail();

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'JSON Error',
                    'errors' => json_last_error_msg()
                ], 500);
            }

            // Return the decoded itemData or a default empty array if null
            return response()->json([
                'status' => 'success',
                'data' => $model ?? []
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => 'failed',
                'message' => 'Unprocessable Entity',
                'errors' => 'There is no item with the given ID'
            ], 422);
        }
    }
}
