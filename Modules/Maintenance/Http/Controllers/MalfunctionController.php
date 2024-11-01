<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\Malfunction;
use App\Models\MalfunctionResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Maintenance\Http\Requests\MalfunctionResponseStoreRequest;
use Modules\Maintenance\Http\Requests\MalfunctionStoreRequest;
use Modules\Maintenance\Http\Resources\MalfunctionResource;

class MalfunctionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $models = Malfunction::get();

        return MalfunctionResource::collection($models);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(MalfunctionStoreRequest $request)
    {
        //
        $model = new Malfunction();

        $model->m_id  = $request['m_id'];
        $model->status_id = 1;
        $model->started_date = now()->format('Y-m-d H:i:s');
        $model->redirect_to_id = Auth::guard('sanctum')->user()->id; // تم الانشاء بواسطة
        $model->user_id = Auth::guard('sanctum')->user()->id; // تم الانشاء بواسطة
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم فتح بلاغ العطل  بنجاح',
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



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(MalfunctionResponseStoreRequest $request, $id)
    {
        //
        $model = new MalfunctionResponse();

        // قطع المصعد
        $elevatorsParts = is_array($request['elevatorsParts']) ?
            $request['elevatorsParts'] :
            array($request['elevatorsParts']);

        // صور العطل
        $malfunctionImages = is_array($request['malfunctionImages']) ?
            $request['malfunctionImages'] :
            array($request['malfunctionImages']);


        // $malfunctionImages = is_array($request['malfunctionVideo']) ?
        //     $request['malfunctionVideo'] :
        //     array($request['malfunctionVideo']);

        // مقطع الفيديو 
        $malfunctionVideo = ApiHelper::uploadBase64Image(
            $request['malfunctionVideo'],
            'maintenances/malfunctions'
        );

        foreach ($malfunctionImages as  $malfunctionImage) {

            if (isset($malfunctionImage))
                $images[] = ApiHelper::uploadBase64Image(
                    $malfunctionImage,
                    'maintenances/malfunctions'
                ); //  الصور
            else $images[] = '';
        }
        //  return $images;
        $model->mal_id = $request['id']; // رقم البلاغ
        $model->cost = $request['cost']; // التكلفة
        $model->mal_type_id = $request['mal_type_id'];
        $model->malfunction_images = implode(',', $images);
        $model->malfunction_videos = $malfunctionVideo; // مفطع فيديو
        $model->status_id = $request['status_id'];
        $model->notes = $request['notes'];
        $model->elevators_parts = json_encode($elevatorsParts);
        $model->user_id = Auth::guard('sanctum')->user()->id; // تم الانشاء بواسطة
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم الاضافة بنجاج',
        ]);
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
