<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeElevatorStoreRequest extends FormRequest
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
            'm_location_id' => 'required|exists:maintenance_location_detections,id',
            //'elevatorsParts' => 'required|json',
            'total_cost' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'status' => 'integer|in:0,1,2', // Add more values if needed
            'done_by' => 'required|exists:users,id',
     
        ];
    }
}
