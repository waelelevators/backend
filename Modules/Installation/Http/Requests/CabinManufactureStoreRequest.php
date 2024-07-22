<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CabinManufactureStoreRequest extends FormRequest
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
            'weight_dbg' => 'required|string',
            'weight_location' => 'required|exists:weight_locations,id',
            'cabin_dbg' => 'required|string',
            'door_size' => 'required|exists:door_sizes,id',
            'machine_chair' => 'required|string',
            'door_direction' => 'required|integer',
            'cover_type' => 'required|exists:cover_types,id',
            'machine_room_height' => 'required|string',
            'machine_room_width' => 'required|string',
            'machine_room_depth' => 'required|string',
            'cabin_max_height' => 'required|string',
            'last_floor_height' => 'required|string',
            'order_attached' => 'required|string',
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
