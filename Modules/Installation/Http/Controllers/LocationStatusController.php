<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\LocationAssignment;
use App\Models\LocationStatus;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Installation\Http\Requests\LocationStatusStoreRequest;
use Modules\Installation\Http\Resources\LocationStatusResource;

class LocationStatusController extends Controller
{

    public function index($filterNoWorkOrder = false)
    {
        $locations = LocationStatus::orderBy('created_at', 'asc')->get();


        if ($filterNoWorkOrder) {

            $locations = $locations->filter(function ($location) {

                $passesFilter = !$location->has_work_order;
                $passesFilter = $passesFilter && $location->status === 1;
                return $passesFilter;
            });
        }

        return LocationStatusResource::collection($locations);
    }


    public function show($id)
    {
        $locationStatus  = LocationStatus::findOrFail($id);

        $resource =  new LocationStatusResource($locationStatus);

        $transformedData = $resource->transformData();

        return response()->json($transformedData);
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    private function locationData(request $request)
    {
        $Data = [];
        if ($request['stage_id'] == 1) {

            $Data = [
                'pouring_the_pit' => $request['pouring_the_pit'],
                'cleaning_well_from_dirt' => $request['cleaning_well_from_dirt'],
                'cleaning_and_disinfecting_the_well' => $request['cleaning_and_disinfecting_the_well'],
                'complete_well_construction' => $request['complete_well_construction'],
                'closing_openings_except_doors' => $request['closing_openings_except_doors'],
                'presence_of_light_source' => $request['presence_of_light_source'],
                'completion_of_beam_works_if_present' => $request['completion_of_beam_works_if_present']
            ];
        }
        if ($request['stage_id'] == 2) {
            $Data = [
                'cleaning_well_from_dirt_and_construction_debris' => $request['cleaning_well_from_dirt_and_construction_debris'],
                'presence_of_ladder_to_machine_room_for_equipment_loading' => $request['presence_of_ladder_to_machine_room_for_equipment_loading'],
                'sealing_around_doors_before_starting_stage_two' => $request['sealing_around_doors_before_starting_stage_two'],
                'sealing_machine_room_even_if_with_wood_before_starting_stage_two' => $request['sealing_machine_room_even_if_with_wood_before_starting_stage_two'],
            ];
        }
        if ($request['stage_id'] == 3) {
            $Data = [
                'sealing_around_doors' => $request['sealing_around_doors'],
                'appropriate_three_phase_switch' => $request['appropriate_three_phase_switch'],
                'presence_of_light_in_room' => $request['presence_of_light_in_room'],
                'presence_of_door_to_machine_room_and_suitable_stairs_to_entrance' => $request['presence_of_door_to_machine_room_and_suitable_stairs_to_entrance'],
                'installation_of_air_conditioner_required_before_delivery' => $request['installation_of_air_conditioner_required_before_delivery'],
                'presence_of_ground_rising_cable' => $request['presence_of_ground_rising_cable'],
            ];
        }
        return $Data;
    }
    public function store(LocationStatusStoreRequest $request)
    {

        $locationData = $this->locationData($request);

        $model = new LocationStatus();

        $model->l_assignment_id = $request['assignment_id'];
        $model->location_data = $locationData;
        $model->notes = $request['notes'];
        $model->detection_by = $request['detection_by'];
        $model->status = $request['status'];

        $model->user_id = Auth::guard('sanctum')->user()->id;
        $model->save();

        LocationAssignment::where(['id' => $request['assignment_id']])
            ->update([
                'status' => 3 // تم عملية الشكف بنجاح
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم الاضافة بنجاح',
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $locationData = $this->locationData($request);

        LocationStatus::where('id', $id)->update(
            [
                'location_data' => $locationData,
                'notes' =>  $request['notes'],
                'status' => $request['status'],
            ]
        );

        // $model->l_assignment_id = $request['assignment_id'];
        // $model->location_data = $locationData;
        // $model->detection_by = $request['detection_by'];
        // $model->user_id = Auth::guard('sanctum')->user()->id;
        //$model->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم التعديل  بنجاح',
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
