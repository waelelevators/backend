<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Area;
use App\Models\MaintenanceContract;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AreaController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return Area::get();
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $request->validate(
            [
                'name' => 'required|string|unique:areas',
                'description' => 'nullable|string',

            ],
            [
                'name.required' => 'الاسم اجبارى',
                'name.unique' => 'تم اضافة هذة المنطقة من قبل'
            ]
        );

        $model = new Area();
        $model->name = $request['name'];
        $model->description = $request['description'];
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة المرحلة بنجاح',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return Area::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $model = Area::findOrFail($id);
        $model->name = $request['name'];
        $model->description = $request['description'];
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل البيانات بنجاح',
        ]);
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $model = Area::findOrFail($id);

        $model->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم الحذف بنجاح البيانات بنجاح',
        ]);
    }

    // vertion and more for maintenance contracts

    // changeContractArea
    function changeContractArea(Request $request)
    {
        $request->validate(
            [
                'maintenance_contract_ids' => 'required|array',
                'maintenance_contract_ids.*' => 'required|exists:maintenance_contracts,id',
                'area_id' => 'required|exists:areas,id',
            ],
            [
                'contract_id.required' => 'رقم العقد مطلوب',
                'contract_id.exists' => 'رقم العقد غير موجود',
                'area_id.required' => 'رقم المنطقة مطلوب',
                'area_id.exists' => 'رقم المنطقة غير موجود',
            ]
        );

        MaintenanceContract::whereIn('id', $request->maintenance_contract_ids)
            ->update(['area_id' => $request->area_id]);

        return response([
            'message' => 'Contract updated successfully',
            'status' => 'success',
        ]);
    }
}