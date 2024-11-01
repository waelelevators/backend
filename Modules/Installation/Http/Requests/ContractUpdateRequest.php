<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class ContractUpdateRequest extends FormRequest
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
            'clientType' => 'required|integer',
            'firstName' => 'nullable|required_if:clientType,1|string',
            'secondName' => 'nullable|required_if:clientType,1|string',
            'thirdName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'anotherPhone' => 'nullable|integer|digits:9',
            'whatsappPhone' => 'nullable|integer|digits:9',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',

            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer|digits:10',
            'taxNo' => 'nullable|required_if:clientType,2|integer|digits:10',
            'building_image' => 'nullable|string',
            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'image' => 'nullable|string',
            'locationBuilding' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'taxValue' => 'required|numeric',
            'priceIncludeTax' => 'required|numeric',
            'discountValue' => 'nullable|numeric',
            'elevatorType' => 'required|integer',
            'cabinRailsSize' => 'required|integer',
            'stopsNumber' => 'required|integer',
            'elevatorTrip' => 'required|integer',
            'elevatorWarranty' => 'required|string',
            'entrancesNumber' => 'required|integer',
            'freeMaintenance' => 'required|integer',
            'innerDoorType' => 'required|integer',
            'machineLoad' => 'required|integer',
            'machineSpeed' => 'required|integer',
            'doorsNumbers' => 'required|integer',
            'outerDoorDirection' => 'required|integer',
            'peopleLoad' => 'required|integer',
            'totalFreeVisit' => 'required|integer',
            'projectName' => 'nullable|string',
            'doorSize' => 'required|integer',
            'controlCard' => 'required|integer',
            'stage' => 'required|integer',
            'elevatorRoom' => 'required|integer',
            'machineWarranty' => 'required|integer',
            'otherAdditions' => 'nullable|array',
            'machineType' => 'required|integer',
            'counterweightRailsSize' => 'required|integer',
            'reachUs' => 'required|integer',
            'webSiteName' => 'nullable|required_if:reachUs,1|string',
            'socialName' => 'nullable|required_if:reachUs,2|string',
            'clients' => 'nullable|required_if:reachUs,3|integer',
            'employees' => 'nullable|required_if:reachUs,4|integer',
            'others' => 'nullable|required_if:reachUs,5|string',


            'externalDoorSpecifications' => 'required|array',

            'externalDoorSpecifications.*.floor' => 'required|integer',
            'externalDoorSpecifications.*.door_number' => 'required|integer',

            'externalDoorSpecifications.*.external_door_specifications' => 'required|integer',
            'externalDoorSpecifications.*.door_opening_direction' => 'required|integer',

            'externalDoorSpecifications.*.external_door_specifications2' => 'nullable|required_if:externalDoorSpecifications.*.door_number,2|integer',
            'externalDoorSpecifications.*.door_opening_direction2' => 'nullable|required_if:externalDoorSpecifications.*.door_number,2|integer',

            'paymentStages' => 'required|array',
            'paymentStages.*.amount' => 'required|numeric',
            'paymentStages.*.amountWithTaxed' => 'required|numeric',
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
