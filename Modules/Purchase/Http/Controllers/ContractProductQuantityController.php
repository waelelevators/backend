<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\ContractProductQuantity;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Purchase\Http\Requests\ContractStoreProductQuantityRequest;

class ContractProductQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $stageOne = ContractProductQuantity::where('elevator_type_id', $request->elevator_type_id)
            ->where('floor_id', $request->floor)
            ->with('product')
            ->where('stage_id', 1)
            ->get();

        $stageTwo = ContractProductQuantity::where('elevator_type_id', $request->elevator_type_id)
            ->where('floor_id', $request->floor)
            ->with('product')
            ->where('stage_id', 2)
            ->get();

        $stageThree = ContractProductQuantity::where('elevator_type_id', $request->elevator_type_id)
            ->where('floor_id', $request->floor)
            ->with('product')
            ->where('stage_id', 3)
            ->get();

        return [
            'stageOne'   => $stageOne,
            'stageTwo'   => $stageTwo,
            'stageThree' => $stageThree
        ];
    }
    /**
     * Store a newly created resource in storage.
     * @param ContractStoreProductQuantityRequest $request
     * @return Renderable
     */
    public function store(ContractStoreProductQuantityRequest $request)
    {
        $product_quantity = new ContractProductQuantity();

        $product_quantity->product_id = request()->product_id;
        $product_quantity->elevator_type_id = request()->elevator_type_id;
        $product_quantity->floor_id = request()->floor;
        $product_quantity->stage_id = request()->stage;
        $product_quantity->qty = request()->qty;
        $product_quantity->price = request()->price;
        $product_quantity->save();

        return $product_quantity;
    }
    public function show($id)
    {

        return ContractProductQuantity::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ContractStoreProductQuantityRequest $request, $id)
    {
        try {
            // Find the existing record
            $product_quantity = ContractProductQuantity::findOrFail($id);

            // Update the record with new data from the request
            $product_quantity->product_id = request()->product_id;
            $product_quantity->elevator_type_id = request()->elevator_type_id;
            $product_quantity->floor_id = request()->floor;
            $product_quantity->stage_id = request()->stage;
            $product_quantity->qty = request()->qty;
            $product_quantity->price = request()->price;
            $product_quantity->save();

            // Return success message
            return response()->json([
                'status' => 'success',
                'message' => 'تم التعديل البيانات بنجاح',
            ], 200);
        } catch (\Exception $e) {
            // Return error message if something goes wrong
            return response()->json([
                'message' => 'هنالك خطا في عملية التعديل ',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'تم اضافة كشف الموقع بنجاح',
    //     ]);
    // }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */

    function destroy(Request $request, $id)
    {
        return  ContractProductQuantity::where('id', $id)->delete();
    }
}
