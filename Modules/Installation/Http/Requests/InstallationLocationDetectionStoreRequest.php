<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class InstallationLocationDetectionStoreRequest extends FormRequest
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

            /* الجزء الخاص بالعميل*/

            'clientType' =>  'required', 'integer',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'required|integer|digits:9',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'idNumber' => 'nullable|integer',
            'commercialRegistrationNo' => 'nullable|integer',
            'taxNo' => 'nullable|integer',
            'building_image' => 'nullable|string',
            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'image' => 'nullable|string',
            'locationBuilding' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',

            /* الجزء الخاص بالعميل*/

            /* كيف وصلت لنا */

            'reachUs' => 'required|integer',
            'webSiteName' => 'nullable|required_if:reachUs,1|string',
            'socialName' => 'nullable|required_if:reachUs,2|string',

            'clients' => 'required_if:reachUs,3|integer',
            'employees' => 'required_if:reachUs,4|integer',
            'others' => 'required_if:reachUs,5|string',

            /* كيف وصلت لنا */

            'wellHeight' => 'required|string',
            'wellDepth' => 'required|string',
            'wellWidth' => 'required|string',
            'lastFloorHeight' => 'required|string',
            'bottomTheElevator' => 'required|string',
            'stopsNumber' => 'required|string',
            'elevatorType' => 'required|string',
            'entrancesNumber' => 'required|string',
          
            'wellType' => 'required|integer',
            'elevatorWeightLocation' => 'required|string',
            'weightCantileverSizeGuide' => 'required|string',
            'cabinCantileverSizeGuide' => 'required|string',
            'dbgWeight' => 'required|string',
            'dbgCabin' => 'required|string',
            'peopleLoad' => 'required|string',
            'machineLoad' => 'required|string',
            'normalDoor' => 'required|string',
            'centerDoor' => 'required|string',
            'telescopeDoor' => 'required|string',
            'machineRoomDepth' => 'required|string',
            'machineRoomWidth' => 'required|string',
            'machineRoomHeight' => 'required|string',
            'detectionBy' => 'required|integer|exists:users,id',
            'notes' => 'nullable|string',
            'doorSizes' => 'required|array',
            'doorSizes.*.door_size' => 'required|string',
            'doorSizes.*.door_height' => 'required|string',
            'doorSizes.*.right_shoulder_size' => 'required|string',
            'doorSizes.*.left_shoulder_size' => 'required|string',
            'doorSizes.*.well_depth' => 'required|string',
            'doorSizes.*.well_width' => 'required|string',
            'doorSizes.*.floor_height' => 'required|string',
            'doorSizes.*.floor_id' => 'required|integer',
            'doorSizes.*.outer_door_directions' => 'required|integer',

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
