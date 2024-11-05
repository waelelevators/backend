<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Area;
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
}
