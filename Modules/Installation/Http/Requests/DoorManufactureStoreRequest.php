<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class DoorManufactureStoreRequest extends FormRequest
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
            'contract_id' => 'required|exists:contracts,id',
            'started_date' => 'nullable|date_format:Y-m-d H:i:s',
            'accept_date' => 'nullable|date_format:Y-m-d H:i:s',
            'ended_date' => 'nullable|date_format:Y-m-d H:i:s',
            'door_cover' => 'required|exists:colors,id',
            'door_size' => 'required|exists:door_sizes,id',
            // 'status' => ' nullable|exists:statues,id',
            'doors_number' => 'required|integer|min:1',
            'order_attached' => 'required|string',
            'notes' => 'nullable|string'
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
