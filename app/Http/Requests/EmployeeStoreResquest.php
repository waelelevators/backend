<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EmployeeStoreResquest extends FormRequest
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
            'name' => 'required',
            'department' => 'required',
            'email' => 'required|email|unique:users,email',
            'rule_category_id' => 'required',
            'password' => 'required',
        ];
    }
    public function messages()
    {
        return [

            'name.required' => 'حقل الاسم اجبارى',
            'department.required' => 'حقل القسم اجبارى',
            'email.required'=>'حقل البريد الالكتروني اجباري',
            'email.email' => 'صيغه البريد الالكتورني غير صحيحه',
            'email.unique' => 'البريد الالكتروني موجود مسبقا',
            'password.required' => 'كلمه السر اجباريه'
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
