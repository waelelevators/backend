<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContractStoreProductQuantityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'elevator_type_id' =>  'required|integer',
            'stage' =>  'required|integer',
            'product_id' =>  'required|integer',
            'qty' =>  'required|integer',
            'floor' =>  'required|integer',
            'price' =>   'required|numeric',
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
            'elevator_type_id.required' => 'حقل نوع المصعد مطلوب',
            'elevator_type_id.integer' => 'حقل نوع المصعد يجب ان يكون رقما',

            'stage.required' => 'حقل المرحلة مطلوب',
            'stage.integer' => 'حقل المرحلة يجب ان يكون رقما',

            'price.required' => 'حقل سعر الوحدة مطلوب',
            'price.integer' => 'حقل سعر الوحدة يجب ان يكون رقما',

            'product_id.required' => 'حقل المنتج مطلوب',
            'product_id.integer' => 'حقل المنتج يجب ان يكون رقما',

            'qty.required' => 'حقل الكمية مطلوب',
            'qty.integer' => 'حقل الكمية يجب ان يكون رقما',

            'floor.required' => 'حقل الطابق مطلوب',
            'floor.integer' => 'حقل الطابق يجب ان يكون رقما',

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
