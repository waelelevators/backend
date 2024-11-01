<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Maintenance\Rules\Base64Image;

class MonthlyMaintenanceTechnicantStoreResquest extends FormRequest
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
        // 'required|integer|exists:regions,id',

        return [
            'id' => 'required|integer|exists:maintenances,id',
            'tech_id' => 'required|integer|exists:users,id',
            'visit_status_id' => 'required|integer|exists:visit_statuses,id',

            'started_image' => 'required_without:whats_screen_shot,call_screen_shot',
            'whats_screen_shot' => 'required_without:started_image,call_screen_shot',
            'call_screen_shot' => 'required_without:started_image,whats_screen_shot',

            'control_parachute_image' => ['nullable', new Base64Image],
            'interior_door_stream_image' => ['nullable', new Base64Image],
            'machine_room_image' => ['nullable', new Base64Image],
            'floor_image' => ['nullable', new Base64Image],
            'bottom_well_image' => ['nullable', new Base64Image],
            'top_cabin_image' => ['nullable', new Base64Image],
            'note_image' => ['nullable', new Base64Image],
            'ground_floor_image' => ['nullable', new Base64Image],
            'ceiling_image' => ['nullable', new Base64Image],

            'is_check_motor_comms' => 'nullable|in:0,1',
            'is_check_all_doors' => 'nullable|in:0,1',
            'is_cleaning_machine_motor' => 'nullable|in:0,1',
            'is_lubrication_transmission_rails' => 'nullable|in:0,1',
            'is_complete_elevator_lubrication' => 'nullable|in:0,1',
            'is_cabin_cleaning' => 'nullable|in:0,1',
            'is_lubrication_cab_rails' => 'nullable|in:0,1',
            'is_check_break' => 'nullable|in:0,1',
            'is_cleaning_dashboard' => 'nullable|in:0,1',
        ];
    }
}
