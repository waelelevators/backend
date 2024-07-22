<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class QuotationStoreRequest extends FormRequest
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
            //

            /* الجزء الخاص بالعميل*/

            'clientType' =>  'required', 'integer',
            'firstName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'required|integer|digits:9',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'idNumber' => 'nullable|integer',
            'commercialRegistrationNo' => 'nullable|integer',
            'taxNo' => 'nullable|integer',
            'buildingImage' => 'nullable|string',
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


            'q_number'     => 'nullable|string',
            'project_name' => 'nullable|string',
            'more_adds'    => 'nullable|string',
            'notes'    => 'nullable|string',

            'elevatorType' => 'required|integer',
            'machineType' => 'required|integer',
            'stopsNumber' => 'required|integer',
            'peopleLoad' => 'required|integer',
            'driveType' => 'required|integer',
            'machineLoad' => 'required|integer',
            'machineWarranty' => 'required|integer',
            'machineSpeed' => 'required|integer',
            'controlCard' => 'required|integer',
            'doorSize' => 'required|integer',
            'templateName' => 'required|integer',
            'entrancesNumber' => 'required|integer',
            'total_price'     => 'required|numeric',
            'tax'             => 'required|numeric',
            'discount'        => 'required|numeric',





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
