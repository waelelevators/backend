<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class MaintenancePaymentsStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:maintenances,id',
            'attachment' =>  'required', 'string',
            'amount' => 'required|numeric',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    public function messages()
    {
        return [
            'id.required' => 'حقل رقم العقد  مطلوب',
            'id.integer' => 'رقم العقد يجب ان يكون رقم',
            'attachment.required' => 'حقل المرفق يجب ان يكون مطلوب',
            'amount.required' => 'حقل المبلغ مطلوب',
            'amount.numeric' => 'رقم المبلغ يجب ان يكون رقم',

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
