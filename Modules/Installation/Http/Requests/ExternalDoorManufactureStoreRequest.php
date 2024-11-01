<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ExternalDoorManufactureStoreRequest extends FormRequest
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

            'contract_id' => 'required|exists:contracts,id',
            'notes' => 'nullable|string',
            'door_size' => 'required|exists:door_sizes,id',
            'doors_number' => 'required|integer',
            'order_attached' => 'required|string',
            'user_id' => 'exists:users,id',

            'externalDoorSpecifications' => 'required|array',
            'externalDoorSpecifications.*.door_number' => 'required|integer|min:1',
            'externalDoorSpecifications.*.outer_door_directions' => 'required|exists:outer_door_directions,id',
            'externalDoorSpecifications.*.door_cover' => 'required|exists:colors,id',

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
