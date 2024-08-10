<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;
use App\Models\InstallationLocationDetection;
use Illuminate\Support\Facades\Cache;

use Modules\Installation\Http\Requests\InstallationLocationDetectionStoreRequest;
use Modules\Installation\Http\Resources\InstallationLocationDetectionResource;

class LocationDetectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        // return InstallationLocationDetectionResource::collection($models);

        $perPage = $request->input('per_page', 5);
        $models =  InstallationLocationDetection::orderByDesc('created_at')->paginate($perPage);

        return InstallationLocationDetectionResource::collection($models);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(InstallationLocationDetectionStoreRequest $request)
    {

        DB::transaction(function () use ($request) {

            $floor_data =  is_array($request['doorSizes']) ?
                $request['doorSizes'] :
                array($request['doorSizes']);

            if (isset($request['buildingImage'])) $building_image = ApiHelper::uploadBase64Image(
                $request['buildingImage'],
                'building'
            ); // صورة العمارة

            else $building_image = '';

            if (isset($request['wellImage'])) $location_image = ApiHelper::uploadBase64Image(
                $request['wellImage'],
                'location'
            ); // صوة البئر من الداخل

            else $location_image = '';

            // بحث عن العميل موجود ام لا
            //$client =  ApiHelper::handleLocationClientData($request);
            $client =  ApiHelper::handleAddClient($request);


            $representative_id =  ApiHelper::handleGetUsData(
                $request,
                'installations-location-detection'
            ); // كيف وصلت لنا



            $locationModel = new InstallationLocationDetection();

            $locationModel->client_id = $client->id;

            $locationModel->location_data = [
                'region'         => intval($request['region']) ?? '',
                'city'           => intval($request['city']) ?? '',
                'neighborhood'   => intval($request['neighborhood']) ?? '',
                'street'         => $request['street'] ?? '',
                'building_image' => $building_image ?? '',
                'location_url'   => $request['locationBuilding'] ?? '',
                'lat'            => $request['lat'] ?? '',
                'long'           => $request['long'] ?? ''
            ];

            $locationModel->well_data = [
                'well_image'                   =>  $location_image ?? '',
                'well_height'                  =>  $request['wellHeight'] ?? '',
                'well_depth'                   =>  $request['wellDepth'] ?? '',
                'well_width'                   =>  $request['wellWidth'] ?? '',
                'last_floor_height'            =>  $request['lastFloorHeight'] ?? '',
                'bottom_the_elevator'          =>  $request['bottomTheElevator'] ?? '',
                'stop_number_id'               =>  $request['stopsNumber'] ?? '',
                'elevator_trips_id'            =>  $request['elevatorTrips'] ?? '', // مشوار المصعد
                'elevator_type_id'             =>  $request['elevatorType'] ?? '', // نوع المصعد
                'entrances_number_id'          =>  $request['entrancesNumber'] ?? '', // عدد المداخل
                'well_type'                    =>  $request['wellType'] ?? '', // نوع البئر
                'door_open_direction_id'       =>  $request['doorOpenDirection'] ?? '', // اتجاه فتح الباب الخارجي
                'elevator_weight_location_id'  =>  $request['elevatorWeightLocation'] ?? '',  // موقع الثقل
                'weight_cantilever_size_guide' =>  $request['weightCantileverSizeGuide'] ?? '',
                'cabin_cantilever_size_guide'  =>  $request['cabinCantileverSizeGuide'] ?? '',
                'dbg_weight'                   =>  $request['dbgWeight'] ?? '',
                'dbg_cabin'                    =>  $request['dbgCabin'] ?? '',
                'cabin_depth'                  =>  $request['cabinDepth'] ?? '',
                'cabin_width'                  =>  $request['cabinWidth'] ?? '',
                'people_load'                  =>  $request['peopleLoad'] ?? '',
                'machine_load'                 =>  $request['machineLoad'] ?? '',
                'normal_door'                  =>  $request['normalDoor'] ?? '',
                'center_door'                  =>  $request['centerDoor'] ?? '',
                'telescope_door'               =>  $request['telescopeDoor'] ?? '',
            ];

            $locationModel->machine_data = [
                'machine_room_depth'            =>  $request['machineRoomDepth'] ?? '',
                'machine_room_width'            =>  $request['machineRoomWidth'] ?? '',
                'machine_room_height'           =>  $request['machineRoomHeight'] ?? '',
            ];

            $locationModel->floor_data =           json_encode($floor_data);
            $locationModel->notes =                $request['notes'];
            $locationModel->detection_by =         $request['detectionBy'];
            $locationModel->representative_id =    $representative_id;
            $locationModel->well_type =            $request['wellType'];
            $locationModel->user_id =              Auth::guard('sanctum')->user()->id;
            $locationModel->save();
        });

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
        $model = InstallationLocationDetection::with(['client', 'representatives', 'detectionBy', 'user'])
            ->findOrFail($id);
        return $model;
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $locationModel = InstallationLocationDetection::findOrFail($id);

        if (isset($request['buildingImage'])) $building_image = ApiHelper::uploadBase64Image(
            $request['buildingImage'],
            'building'
        ); // صورة العمارة
        else $building_image = $locationModel->location_data['building_image'] ?? '';


        if (isset($request['wellImage'])) $well_image = ApiHelper::uploadBase64Image(
            $request['wellImage'],
            'location'
        ); // صوة البئر من الداخل
        else $well_image = $locationModel->well_data['well_image'] ?? '';

        $floor_data =  is_array($request['doorSizes']) ?
            $request['doorSizes'] :
            array($request['doorSizes']);


        ApiHelper::updateUsData(
            $request,
            $locationModel->representative_id
        ); // كيف وصلت لنا

        $locationModel->location_data = [
            'region'         => intval($request['region']) ?? '',
            'city'           => intval($request['city']) ?? '',
            'neighborhood'   => $request['neighborhood'] ?? '',
            'street'         => $request['street'] ?? '',
            'building_image' => $building_image, // صورة العمارة
            'location_url'   => $request['locationBuilding'] ?? '',
            'lat'            => $request['lat'] ?? '',
            'long'           => $request['long'] ?? ''
        ];

        $locationModel->well_data = [
            'well_image'                   =>  $well_image, // صورة البئر من الداخل
            'well_height'                  =>  $request['wellHeight'] ?? '',
            'well_depth'                   =>  $request['wellDepth'] ?? '',
            'well_width'                   =>  $request['wellWidth'] ?? '',
            'last_floor_height'            =>  $request['lastFloorHeight'] ?? '',
            'bottom_the_elevator'          =>  $request['bottomTheElevator'] ?? '',
            'stop_number_id'               =>  $request['stopsNumber'] ?? '',
            'elevator_trips_id'            =>  $request['elevatorTrips'] ?? '', // مشوار المصعد
            'elevator_type_id'             =>  $request['elevatorType'] ?? '', // نوع المصعد
            'entrances_number_id'          =>  $request['entrancesNumber'] ?? '', // عدد المداخل
            'well_type'                    =>  $request['wellType'] ?? '', // نوع البئر
            'door_open_direction_id'       =>  $request['doorOpenDirection'] ?? '', // اتجاه فتح الباب الخارجي
            'elevator_weight_location_id'  =>  $request['elevatorWeightLocation'] ?? '',  // موقع الثقل
            'weight_cantilever_size_guide' =>  $request['weightCantileverSizeGuide'] ?? '',
            'cabin_cantilever_size_guide'  =>  $request['cabinCantileverSizeGuide'] ?? '',
            'dbg_weight'                   =>  $request['dbgWeight'] ?? '',
            'dbg_cabin'                    =>  $request['dbgCabin'] ?? '',
            'cabin_depth'                  =>  $request['cabinDepth'] ?? '',
            'cabin_width'                  =>  $request['cabinWidth'] ?? '',
            'people_load'                  =>  $request['peopleLoad'] ?? '',
            'machine_load'                 =>  $request['machineLoad'] ?? '',
            'normal_door'                  =>  $request['normalDoor'] ?? '',
            'center_door'                  =>  $request['centerDoor'] ?? '',
            'telescope_door'               =>  $request['telescopeDoor'] ?? '',
        ];

        $locationModel->machine_data = [
            'machine_room_depth'            =>  $request['machineRoomDepth'] ?? '',
            'machine_room_width'            =>  $request['machineRoomWidth'] ?? '',
            'machine_room_height'           =>  $request['machineRoomHeight'] ?? '',
        ];

        $locationModel->floor_data = json_encode($floor_data);
        $locationModel->notes =  $request['notes'];
        $locationModel->detection_by =  $request['detectionBy'];

        $locationModel->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم التعديل بنجاح',
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
        try {
            $detection = InstallationLocationDetection::findOrFail($id);
            $detection->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الكشف بنجاح',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'الكشف غير موجود',
            ], 404);
        } catch (\Exception $e) {

            Log::error('Error deleting InstallationLocationDetection: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف الكشف',
            ], 500);
        }
    }
}
