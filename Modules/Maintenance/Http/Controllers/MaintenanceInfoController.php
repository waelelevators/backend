<?php

namespace Modules\Maintenance\Http\Controllers;

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
            $maintenances =  MaintenanceLog::with('mInfo.contracts', 'mInfo.representatives')
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
    public function store(MaintenanceInfoStoreResquest $request)
    {

        DB::transaction(function () use ($request) {

            $client =  $this->handleClientData($request);

            if (isset($request['buildingImage'])) $building_image = $this->uploadBase64Image(
                $request['buildingImage'],
                'maintenances'
            );

            else $building_image = '';

            $mInfo = new MaintenanceInfo();

            $mInfo->client_id = $client->id;
            $mInfo->contract_id = $request['contract_id'] ?? 1;
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
            $mInfo->how_did_you_get_to_us =  intval($request['reachUs']);
            $mInfo->user_id = Auth::guard('sanctum')->user()->id;
            $mInfo->save();

            $this->handleGetUsData($request, $mInfo->id, 'maintenances'); // كيف وصلت لنا

            $maintenance = new Maintenance();

            $maintenance->m_info_id = $mInfo->id;
            $maintenance->started_date = $request['startDate'];
            $maintenance->ended_date = $request['endDate'];
            $maintenance->visits_number = $request['totalVisit'];
            $maintenance->m_type_id = $request['maintenanceType'];
            $maintenance->cost = $request['amount'];
            $maintenance->user_id = Auth::guard('sanctum')->user()->id;
            $maintenance->save();

            $mLog = new MaintenanceLog();
            $mLog->m_info_id = $mInfo->id;
            $mLog->m_id = $maintenance->id;
            $mLog->user_id = Auth::guard('sanctum')->user()->id;
            $mLog->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة العقد بنجاح',
        ]);
    }

    private function handleGetUsData(request $request, $contract_id, $contract_type)
    {
        if ($request->reachUs == 1) { // موقع الكتروني

            $r = new Representative();
            $r->name = $request['website_name'] ?? '';
            $r->representativeable_id = 0;
            $r->contract_type = $contract_type;
            $r->contract_id = $contract_id;
            $r->save();
        }
        if ($request->reachUs == 2) { // وسائل التواصل

            $r = new Representative();
            $r->name = $request['social_name'] ?? '';
            $r->representativeable_id = 0;
            $r->contract_type = $contract_type;
            $r->contract_id = $contract_id;
            $r->save();
        }

        if ($request->reachUs == 3) { // عميل لدى المؤسسة
            //  $representatives = $request['clients'];
            //  foreach ($representatives as $index => $value) {

            $r = new Representative();
            $r->representativeable_type = 'App\Models\Client';
            $r->representativeable_id = collect($request['clients']);
            $r->contract_type = $contract_type;
            $r->contract_id = $contract_id;
            $r->save();
            //   }
        } elseif ($request->reachUs == 4) { // مندوب داخلي
            //$representatives = $request['employees'];
            //   foreach ($representatives as $index => $value) {

            $r = new Representative();
            $r->representativeable_type = 'App\Models\Employee';
            $r->representativeable_id = collect($request['employees']);
            $r->contract_type = $contract_type;
            $r->contract_id = $contract_id;
            $r->save();
            //   }
        } elseif ($request->reachUs == 5) { //  مندوب خارجي

            $representatives = is_array($request['representatives']) ?
                $request['representatives'] :
                array($request['representatives']);

            foreach ($representatives as $representative) {
                $r = new Representative();
                // $r->representativeable_type = 'null';
                $r->representativeable_id = 0;
                $r->contract_type = $contract_type;
                $r->contract_id = $contract_id;
                $r->name = $representative['representative_name'];
                $r->phone = $representative['representative_phone'];
                $r->save();
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MaintenanceLog::with('mInfo.contracts', 'mInfo.representatives')
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

    private  function handleClientData($request)
    {

        $clientType = $request['clientType'];

        if ($clientType == 1) { // فرد
            $findClient = Client::whereJsonContains('data->id_number', $request['idNumber'])
                ->where('type', $clientType)
                ->first();

            if ($findClient) {
                return $findClient;
            }
        } elseif ($clientType == 2) { // قطاع خاص
            $findClient = Client::whereJsonContains('data->id_number', $request['idNumber'])
                ->where('type', $clientType)
                ->first();

            if ($findClient) {
                return $findClient;
            }
        } elseif ($clientType == 3) { // مؤسسة حكومية
            $findClient = Client::whereJsonContains('data->id_number', $request['idNumber'])
                ->where('type', $clientType)
                ->first();
            if ($findClient) {
                return $findClient;
            }
        }

        if (isset($request['image'])) $image = $this->uploadBase64Image($request['image'], 'client');
        else $image = '';

        $client =  new Client;

        $client->type = $clientType;

        if ($clientType == 1) {
            $client->data = [
                'first_name' => $request['firstName'],
                'second_name' => $request['secondName'],
                'third_name' => $request['thirdName'],
                'last_name' => $request['forthName'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'id_number' => $request['idNumber'],
                'image' => $image,
            ];
        } elseif ($clientType == 2) {


            $client->data = [
                'name' => $request['companyName'],
                'owner_name' => $request['represents'],
                'commercial_register' => $request['commercial_register'],
                'tax_number' => $request['taxNo'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'id_number' => $request['idNumber'],
                'image' => $image,
            ];
        } else {
            $client->data = [
                'name' => $request['entityName'],
                'id_number' => $request['idNumber'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'image' => $image,
            ];
        }

        $client->save();
        return $client;
    }
}
