<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceLocationDetection;
use App\Models\Representative;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Maintenance\Http\Requests\LocationDetectionStoreRequest;
use Modules\Maintenance\Http\Resources\LocationDetectionResource;
class LocationDetectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $model = MaintenanceLocationDetection::orderByDesc('created_at')->get();

        return  LocationDetectionResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(LocationDetectionStoreRequest $request)
    {
        $client =  ApiHelper::handleClientData($request); // العميل

        if (isset($request['buildingImage'])) $building_image = ApiHelper::uploadBase64Image(
            $request['buildingImage'],
            'maintenances'
        );

        else $building_image = '';

        $mLocationDetection = new MaintenanceLocationDetection();

        $mLocationDetection->client_id = $client->id;
        $mLocationDetection->projects_name   = $request['projects_name'];
        $mLocationDetection->location_data = [
            'region'         => intval($request['region']),
            'city'           => intval($request['city']),
            'neighborhood'   => $request['neighborhood'],
            'street'         => $request['street'],
            'building_image' => $building_image,
            'location_url'   => $request['locationBuilding'],
            'lat'            => $request['lat'] ?? '',
            'long'           => $request['long'] ?? ''
        ];
        $mLocationDetection->elevator_data = [
            'elevator_type_id'       =>  intval($request['elevatorType']),
            'stop_number_id'         =>  intval($request['stopsNumber']),
            'machine_speed_id'       =>  intval($request['machineSpeed']),
            'door_size_id'           =>  intval($request['doorSize']),
            'control_card_id'        =>  intval($request['controlCard']),
            'machine_type_id'        =>  intval($request['machineType'])
        ];
        $mLocationDetection->how_did_you_get_to_us =  intval($request['reachUs']);
        $mLocationDetection->detection_by = $request['detectionBy'];
        $mLocationDetection->user_id = Auth::guard('sanctum')->user()->id;
        $mLocationDetection->save();

        ApiHelper::handleGetUsData($request, $mLocationDetection->id, 'main-locations'); // كيف وصلت لنا

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة كشف الموقع بنجاح',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MaintenanceLocationDetection::findOrFail($id);
    }

    //  public function changeStatus()
    // {
    //  return 11;
    // Validator::make($request, [
    //     'status' => 'required|in:active,inactive,pending',
    // ]);

    // $mLocationDetection =  MaintenanceLocationDetection::findOrFail($id);

    // $mLocationDetection->status = $request['status'];
    // $mLocationDetection->save();
    //}


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (isset($request['buildingImage'])) $building_image = $this->uploadBase64Image(
            $request['buildingImage'],
            'maintenances'
        );

        else $building_image = '';

        $mLocationDetection =  MaintenanceLocationDetection::findOrFail($id);

        $mLocationDetection->projects_name   = $request['projects_name'];
        $mLocationDetection->location_data = [
            'region'         => intval($request['region']),
            'city'           => intval($request['city']),
            'neighborhood'   => $request['neighborhood'],
            'street'         => $request['street'],
            'building_image' => $building_image,
            'location_url'   => $request['locationBuilding'],
            'lat'            => $request['lat'] ?? '',
            'long'           => $request['long'] ?? ''
        ];
        $mLocationDetection->elevator_data = [
            'elevator_type_id'       =>  intval($request['elevatorType']),
            'stop_number_id'         =>  intval($request['stopsNumber']),
            'machine_speed_id'       =>  intval($request['machineSpeed']),
            'door_size_id'           =>  intval($request['doorSize']),
            'control_card_id'        =>  intval($request['controlCard']),
            'machine_type_id'        =>  intval($request['machineType'])
        ];
        $mLocationDetection->how_did_you_get_to_us =  intval($request['reachUs']);
        $mLocationDetection->detection_by = $request['detectionBy'];
        $mLocationDetection->user_id = Auth::guard('sanctum')->user()->id;
        $mLocationDetection->save();

        $this->handleUpdateGetUsData($request, $mLocationDetection->id, 'main-locations');

        // return $modelsss;
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'تم تعديل البيانات بنجاح',
        // ]);
    }

    public function handleUpdateGetUsData($request, $contract_id, $contract_type)
    {

        //where('area_id', $id)->get();

        $representatives = Representative::where([
            'id', $contract_id,
            'contract_type', 'main-locations'
        ])->get();

        return $representatives;

        //  public static function handleGetUsData($request, $contract_id, $contract_type)
        //  {
        // if ($request->reachUs == 1) { // موقع الكتروني

        //     $r = new Representative();
        //     $r->name = $request['website_name'] ?? '';
        //     $r->representativeable_id = 0;
        //     $r->contract_type = $contract_type;
        //     $r->contract_id = $contract_id;
        //     $r->save();
        // }
        // if ($request->reachUs == 2) { // وسائل التواصل

        //     $r = new Representative();
        //     $r->name = $request['social_name'] ?? '';
        //     $r->representativeable_id = 0;
        //     $r->contract_type = $contract_type;
        //     $r->contract_id = $contract_id;
        //     $r->save();
        // }

        // if ($request->reachUs == 3) { // عميل لدى المؤسسة
        //     //  $representatives = $request['clients'];
        //     //  foreach ($representatives as $index => $value) {

        //     $r = new Representative();
        //     $r->representativeable_type = 'App\Models\Client';
        //     $r->representativeable_id = collect($request['clients']);
        //     $r->contract_type = $contract_type;
        //     $r->contract_id = $contract_id;
        //     $r->save();
        //     //   }
        // } elseif ($request->reachUs == 4) { // مندوب داخلي
        //     //$representatives = $request['employees'];
        //     //   foreach ($representatives as $index => $value) {

        //     $r = new Representative();
        //     $r->representativeable_type = 'App\Models\Employee';
        //     $r->representativeable_id = collect($request['employees']);
        //     $r->contract_type = $contract_type;
        //     $r->contract_id = $contract_id;
        //     $r->save();
        //     //   }
        // } elseif ($request->reachUs == 5) { //  مندوب خارجي

        //     $representatives = is_array($request['representatives']) ?
        //         $request['representatives'] :
        //         array($request['representatives']);

        //     foreach ($representatives as $representative) {
        //         $r = new Representative();
        //         // $r->representativeable_type = 'null';
        //         $r->representativeable_id = 0;
        //         $r->contract_type = $contract_type;
        //         $r->contract_id = $contract_id;
        //         $r->name = $representative['representative_name'];
        //         $r->phone = $representative['representative_phone'];
        //         $r->save();
        //     }
        //}
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
