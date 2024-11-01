<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Maintenance\Rules\Base64Image;

class MonthlyMaintenanceStoreResquest extends FormRequest
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
            'visit_date' => 'required|date',
            'tech_id' => 'required|integer|exists:users,id',
            'visit_status_id' => 'required|integer|exists:visit_statuses,id',
            'control_parachute_image' => ['nullable', new Base64Image],
            'interior_door_stream_image' => ['nullable', new Base64Image],
            'machine_room_image' => ['nullable', new Base64Image],
            'floor_image' => ['nullable', new Base64Image],
            'bottom_well_image' => ['nullable', new Base64Image],
            'top_cabin_image' => ['nullable', new Base64Image],
            'note_image' => ['nullable', new Base64Image],
            'ground_floor_image' => ['nullable', new Base64Image],
            'ceiling_image' => ['nullable', new Base64Image],
            'is_check_motor_comms' => 'required|in:0,1',
            'is_check_all_doors' => 'required|in:0,1',
            'is_cleaning_machine_motor' => 'required|in:0,1',
            'is_lubrication_transmission_rails' => 'required|in:0,1',
            'is_complete_elevator_lubrication' => 'required|in:0,1',
            'is_cabin_cleaning' => 'required|in:0,1',
            'is_lubrication_cab_rails' => 'required|in:0,1',
            'is_check_break' => 'required|in:0,1',
            'is_cleaning_dashboard' => 'required|in:0,1',
        ];
    }
}
