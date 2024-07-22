<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceQuotationStoreRequest extends FormRequest
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
            'long' => 'nullable|numeric',


            'buildingType' => 'required|integer',
            'elevatorType' => 'required|integer',
            'stopsNumber' => 'required|integer',
            'machineSpeed' => 'required|integer',
            'doorSize' => 'required|integer',
            'controlCard' => 'required|integer',
            'machineType' => 'required|integer',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'totalVisit' => 'required|integer',
            'isHaveDoor' => 'nullable|integer',
            'isLadder' => 'nullable|integer',
            'q_number'     => 'nullable|string',
            'more_adds'    => 'nullable|string',
            'amount'  => 'required|numeric',

        ];
    }
}
