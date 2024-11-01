<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class LocationStatusStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            // First Stage
            'pouring_the_pit' =>  'nullable|required_if:stage_id,1|in:0,1',
            'cleaning_well_from_dirt' =>  'nullable|required_if:stage_id,1|in:0,1',
            'cleaning_and_disinfecting_the_well' =>  'nullable|required_if:stage_id,1|in:0,1',
            'complete_well_construction' =>  'nullable|required_if:stage_id,1|in:0,1',
            'closing_openings_except_doors' =>  'nullable|required_if:stage_id,1|in:0,1',
            'presence_of_light_source' =>  'nullable|required_if:stage_id,1|in:0,1',
            'completion_of_beam_works_if_present' =>  'nullable|required_if:stage_id,1|in:0,1',


            // Second Stage
            'cleaning_well_from_dirt_and_construction_debris' =>  'nullable|required_if:stage_id,2|in:0,1',
            'presence_of_ladder_to_machine_room_for_equipment_loading' =>  'nullable|required_if:stage_id,2|in:0,1',
            'sealing_around_doors_before_starting_stage_two' =>  'nullable|required_if:stage_id,2|in:0,1',
            'sealing_machine_room_even_if_with_wood_before_starting_stage_two' =>  'nullable|required_if:stage_id,2|in:0,1',

            // Third Stage
            'sealing_around_doors' =>  'nullable|required_if:stage_id,3|in:0,1',
            'appropriate_three_phase_switch' =>  'nullable|required_if:stage_id,3|in:0,1',
            'presence_of_light_in_room' =>  'nullable|required_if:stage_id,3|in:0,1',
            'presence_of_door_to_machine_room_and_suitable_stairs_to_entrance' =>  'nullable|required_if:stage_id,3|in:0,1',
            'installation_of_air_conditioner_required_before_delivery' =>  'nullable|required_if:stage_id,3|in:0,1',
            'presence_of_ground_rising_cable' =>  'nullable|required_if:stage_id,3|in:0,1',

            'notes' =>  'string',
            'assignment_id' => 'required|exists:location_assignments,id',
            'detection_by' => 'required|exists:users,id',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'failed',
            'message' => 'unprocessable entity',
            'errors' => $validator->errors()
        ], 422));
    }
}
