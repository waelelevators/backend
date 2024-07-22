<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationDetectionStoreRequest extends FormRequest
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
            'project_name' => 'nullable|string',

            // تفاصيل الموقع 

            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'street' => 'nullable|string',
            'building_image' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',

            // كيف وصلت لنا 

            'reachUs' => 'required|integer',
            'websiteName' => 'nullable|required_if:reachUs,1|string',
            'socialName' => 'nullable|required_if:reachUs,2|string',
            'clients' => 'required_if:reachUs,3|array',
            'clients.*' => 'required_if:reachUs,3|integer|exists:clients,id',
            'employees' => 'required_if:reachUs,4|array',
            'employees.*' => 'required_if:reachUs,4|integer|exists:employees,id',
            'externalRepresentative' => 'required_if:reachUs,5|array',
            'externalRepresentative.*.name' => 'nullable|required_if:reachUs,5|string',
            'externalRepresentative.*.phone' => 'nullable|required_if:reachUs,5|string',

            //   مواصفات المصعد 

            'elevatorType' => 'required|integer|exists:elevator_types,id',
            'stopsNumber'  => 'required|integer|exists:stops_numbers,id',
            'machineSpeed' => 'required|integer|exists:machine_speeds,id',
            'doorSize'     => 'required|integer|exists:door_sizes,id',
            'controlCard'  => 'required|integer|exists:control_cards,id',
            'machineType'  => 'required|integer|exists:machine_types,id',
            'detectionBy'  => 'required|integer|exists:users,id',
        ];
    }
}
