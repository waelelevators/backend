<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallationClientLocationStoreRequest extends FormRequest
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
            'client_id' => 'nullable|exists:clients,id',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'elevator_trip_id' => 'nullable|exists:elevator_trips,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'visitReason' => 'required|exists:visit_reasons,id',
            'building_type' => 'nullable|exists:building_types,id',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'location_image' => 'nullable|string',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'created_by' => 'nullable|exists:users,id',
        ];
    }
}
