<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\Client;
use App\Models\Maintenance;
use App\Models\MaintenanceInfo;
use App\Models\MaintenanceLog;
use App\Models\Representative;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Http\Requests\MaintenanceInfoStoreResquest;
use Modules\Maintenance\Resources\MaintenanceLogResource;

class MaintenanceInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id)
    {
        if ($id === 'all')
            $maintenances =  MaintenanceLog::with('mInfo.contracts')
                ->orderByDesc('created_at')->get();

        else $maintenances = MaintenanceLog::join('maintenances', 'maintenances.id', '=', 'maintenance_logs.m_id')
            ->select('maintenances.*', 'maintenance_logs.*')
            ->with('mInfo.contracts', 'mInfo.representatives')
            ->orderByDesc('maintenance_logs.created_at')
            ->where('maintenances.m_status_id', $id)
            ->get();

        return  MaintenanceLogResource::collection($maintenances);
    }

    /**
     * Store a newly created resource in storage.
     * @param MaintenanceInfoStoreResquest $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        DB::transaction(function () use ($request) {

            $client =  ApiHelper::handleAddClient($request);

            if (isset($request['buildingImage'])) $building_image = $this->uploadBase64Image(
                $request['buildingImage'],
                'maintenances'
            );

            else $building_image = '';

            // $this->handleGetUsData($request, $mInfo->id, 'maintenances'); // كيف وصلت لنا
            $representative  =  ApiHelper::handleGetUsData($request, 'maintenances');

            $mInfo = new MaintenanceInfo();

            $mInfo->client_id = $client->id;
            $mInfo->contract_id = $request['contract_id'] ?? null;
            $mInfo->project_name = $request['projectName'];
            $mInfo->location_data = [
                'region'         => intval($request['region']),
                'city'           => intval($request['city']),
                'neighborhood'   => $request['neighborhood'],
                'street'         => $request['street'],
                'building_image' => $building_image,
                'location_url'   => $request['locationBuilding'],
                'lat'            => $request['lat'] ?? '',
                'long'           => $request['long'] ?? ''
            ];

            $mInfo->elevator_data = [
                'building_type_id'       =>  intval($request['buildingType']),
                'elevator_type_id'       =>  intval($request['elevatorType']),
                'stop_number_id'         =>  intval($request['stopsNumber']),
                'machine_speed_id'       =>  intval($request['machineSpeed']),
                'door_size_id'           =>  intval($request['doorSize']),
                'control_card_id'        =>  intval($request['controlCard']),
                'machine_type_id'        =>  intval($request['machineType']),
                'is_there_window'        =>  intval($request['isHaveDoor'] ?? ''),
                'is_there_stair'         =>  intval($request['isLadder'] ?? ''),
            ];
            $mInfo->representative_id = $representative;
            $mInfo->user_id = Auth::guard('sanctum')->user()->id;
            $mInfo->save();

            $maintenance = new Maintenance();

            $maintenance->m_info_id = $mInfo->id;
            $maintenance->started_date = $request['startDate'];
            $maintenance->ended_date = $request['endDate'];
            $maintenance->visits_number = $request['totalVisit'];
            $maintenance->m_type_id = $request['maintenanceType'];
            $maintenance->m_status_id = $request['maintenanceStatus'];
            $maintenance->cost = $request['amount'];
            $maintenance->user_id = Auth::guard('sanctum')->user()->id;
            $maintenance->save();

            $mLog = new MaintenanceLog();
            $mLog->m_info_id = $mInfo->id;
            $mLog->m_id = $maintenance->id;
            $mLog->area_id = $request['area_id'] ?? 1;
            $mLog->user_id = Auth::guard('sanctum')->user()->id;
            $mLog->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة العقد بنجاح',
        ]);
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MaintenanceLog::with(
            'mInfo.contracts'
        )
            ->findOrFail($id);
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $mInfo = MaintenanceInfo::findOrFail($id);

            if (isset($request['building_image'])) $building_image = $this->uploadBase64Image(
                $request['building_image'],
                'maintenances'
            );

            else $building_image = '';

            $mInfo->project_name = $request['project_name'];
            $mInfo->location_data = [
                'region'        => $request['region'],
                'city'          => $request['city'],
                'neighborhood'  => $request['neighborhood'],
                'street'         => $request['street'],
                'building_image' => $building_image,
                'location_url'   => $request['location_url'],
                'lat'            =>  $request['lat'],
                'long'           =>  $request['long']
            ];

            $mInfo->elevator_data = [
                'elevator_type_id'       => $request['elevator_type'],
                'building_type_id'       => $request['building_type'],
                'stop_number_id'         => $request['stop_number'],
                'machine_speed_id'       => $request['machine_speed'],
                'door_size_id'           => $request['door_size'],
                'control_card_id'        => $request['control_card'],
                'machine_type_id'        => $request['machine_type'],
                'is_there_window'        => $request['is_there_window'],
                'is_there_stair'         => $request['is_there_stair'],
            ];

            $mInfo->save();

            $maintenance = Maintenance::where('m_info_id', $id)->first();

            $maintenance->started_date = $request['started_date'];
            $maintenance->ended_date = $request['ended_date'];
            $maintenance->visits_number = $request['visits_number'];
            $maintenance->m_type_id = $request['m_type'];
            $maintenance->cost = $request['cost'];
            $maintenance->save();
        });

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
        //
        return $id;
    }

    private function uploadBase64Image($base64Image, $path)
    {
        // Decode the base64-encoded image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = uniqid() . '.png'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $imageData);

        $fullPath = asset('storage/app/public/' . $path . '/' . $filename);

        return $fullPath;
    }
}
