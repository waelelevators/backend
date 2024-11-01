<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceLocationDetectionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'clientType' =>  'required', 'integer',
            'firstName' => 'nullable|required_if:clientType,1|string',
            'secondName' => 'nullable|required_if:clientType,1|string',
            'thirdName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'nullable|required|integer|digits:10',
            'anotherPhone' => 'nullable|required|integer|digits:10',
            'whatsappPhone' => 'nullable|required|integer|digits:10',
            'idNumber' => 'required|integer|digits:10',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',
            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer|digits:10',
            'taxNo'          => 'nullable|required_if:clientType,2|integer|digits:10',
            'image' => 'nullable|string',
            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'locationBuilding' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric'
        ];
    }
}
