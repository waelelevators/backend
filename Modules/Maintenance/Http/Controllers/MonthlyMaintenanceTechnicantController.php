<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MonthlyMaintenance;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Maintenance\Http\Resources\MonthlyResource;

class MonthlyMaintenanceTechnicantController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $model =  MonthlyMaintenance::where('tech_id', Auth::guard('sanctum')->user()->id)
            ->orderByDesc('created_at')->get();

        return MonthlyResource::collection($model);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        if (isset($request['started_image']))
            $started_image = ApiHelper::uploadBase64Image(
                $request['started_image'],
                'maintenances/monthly'
            ); // صورة توضح بدء عملية الصيانة 

        else $started_image = '';

        if (isset($request['whats_screen_shot']))
            $whats_screen_shot = ApiHelper::uploadBase64Image(
                $request['whats_screen_shot'],
                'maintenances/monthly'
            ); // لقطة شاشة واتس 

        else $whats_screen_shot = '';

        if (isset($request['call_screen_shot']))
            $call_screen_shot = ApiHelper::uploadBase64Image(
                $request['call_screen_shot'],
                'maintenances/monthly'
            ); //  لقطة شاشة مكالمة 

        else $call_screen_shot = '';

        $model = new MonthlyMaintenance();

        $model->m_id =  $request['id']; // رقم عقد الصيانة
        $model->visit_status_id =  $request['visit_status_id']; // حالة الزيارة  
        $model->visit_date =  now()->format('Y-m-d'); // تاريخ الزيارة
        $model->started_date =  now()->format('Y-m-d H:i:s');
        $model->visit_data = [
            'is_check_motor_comms' => 0,
            'is_check_all_doors' => 0,
            'is_cleaning_machine_motor' => 0,
            'is_lubrication_transmission_rails' => 0,
            'is_complete_elevator_lubrication' => 0,
            'is_cabin_cleaning' => 0,
            'is_lubrication_cab_rails' => 0,
            'is_check_break' => 0,
            'is_cleaning_dashboard' => 0
        ];
        $model->visit_images = [
            'started_image' => $started_image,
            'call_screen_shot' => $call_screen_shot,
            'whats_screen_shot' => $whats_screen_shot,
        ];
        $model->tech_id =  $request['tech_id'];  // الفني
        $model->user_id =  Auth::guard('sanctum')->user()->id; // تم الانشاء بواسطة
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة الزيارة بنجاح',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MonthlyMaintenance::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (isset($request['control_parachute_image']))
            $control_parachute_image = ApiHelper::uploadBase64Image(
                $request['control_parachute_image'],
                'maintenances/monthly'
            ); // صورة للكنترول
        else $control_parachute_image = '';

        if (isset($request['interior_door_stream_image']))
            $interior_door_stream_image = ApiHelper::uploadBase64Image(
                $request['interior_door_stream_image'],
                'maintenances/monthly'
            ); // صورة لمجرى الباب 
        else $interior_door_stream_image = '';

        if (isset($request['machine_room_image']))
            $machine_room_image = ApiHelper::uploadBase64Image(
                $request['machine_room_image'],
                'maintenances/monthly'
            ); // صورة لغرفة المكينة
        else $machine_room_image = '';

        if (isset($request['floor_image']))
            $floor_image = ApiHelper::uploadBase64Image(
                $request['floor_image'],
                'maintenances/monthly'
            ); // صورة الارضية
        else $floor_image = '';

        if (isset($request['bottom_well_image']))
            $bottom_well_image = ApiHelper::uploadBase64Image(
                $request['bottom_well_image'],
                'maintenances/monthly'
            ); // صورة اسفل البئر
        else $bottom_well_image = '';

        if (isset($request['top_cabin_image']))
            $top_cabin_image = ApiHelper::uploadBase64Image(
                $request['top_cabin_image'],
                'maintenances/monthly'
            ); // صورة اعلى الكبينة
        else $top_cabin_image = '';

        if (isset($request['note_image']))
            $note_image = ApiHelper::uploadBase64Image(
                $request['note_image'],
                'maintenances/monthly'
            ); // صورة الملاحظات ان وجدت
        else $note_image = '';

        if (isset($request['ground_floor_image']))
            $ground_floor_image = ApiHelper::uploadBase64Image(
                $request['ground_floor_image'],
                'maintenances/monthly'
            ); // صورة الدور الارضي
        else $ground_floor_image = '';

        if (isset($request['ceiling_image']))
            $ceiling_image = ApiHelper::uploadBase64Image(
                $request['ceiling_image'],
                'maintenances/monthly'
            ); // صورة السقف 
        else $ceiling_image = '';

        if (isset($request['general_video']))
            $general_video = ApiHelper::uploadBase64Image(
                $request['general_video'],
                'maintenances/monthly'
            ); //  مقطع فيديو 
        else $general_video = '';

        if (isset($request['ended_image']))
            $ended_image = ApiHelper::uploadBase64Image(
                $request['ended_image'],
                'maintenances/monthly'
            ); //  مقطع فيديو 
        else $ended_image = '';

        $model =  MonthlyMaintenance::findOrFail($id);

        $model->ended_date =  now()->format('Y-m-d H:i:s');
        $model->visit_data = [
            'is_check_motor_comms' => intval($request['is_check_motor_comms']) ?? '',
            'is_check_all_doors' => intval($request['is_check_all_doors']) ?? '',
            'is_cleaning_machine_motor' => intval($request['is_cleaning_machine_motor']) ?? '',
            'is_lubrication_transmission_rails' => intval($request['is_lubrication_transmission_rails']) ?? '',
            'is_complete_elevator_lubrication' => intval($request['is_complete_elevator_lubrication']) ?? '',
            'is_cabin_cleaning' => intval($request['is_cabin_cleaning']) ?? '',
            'is_lubrication_cab_rails' => intval($request['is_lubrication_cab_rails']) ?? '',
            'is_check_break' => intval($request['is_check_break']) ?? '',
            'is_cleaning_dashboard' => intval($request['is_cleaning_dashboard']) ?? '',
        ];
        $model->notes =  $request['notes'];
        $model->visit_images = [
            'control_parachute_image' => $control_parachute_image,
            'interior_door_stream_image' => $interior_door_stream_image,
            'machine_room_image' => $machine_room_image,
            'floor_image ' => $floor_image,
            'bottom_well_image  ' => $bottom_well_image,
            'top_cabin_image' => $top_cabin_image,
            'note_image' => $note_image,
            'ground_floor_image' => $ground_floor_image,
            'ceiling_image' => $ceiling_image,
            'general_video' => $general_video,
            'ended_image' => $ended_image,
        ];
        $model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة الزيارة بنجاح',
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
