<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class ClientUpdateResquest extends FormRequest
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
        $id = $this->route('id'); // Get the client ID from the route

        return [
            'clientType' =>  'required',
            'integer',
            'firstName' => 'nullable|required_if:clientType,1|string',
            'secondName' => 'nullable|required_if:clientType,1|string',
            'thirdName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'nullable|required|integer|digits:9,' . $id,
            'anotherPhone' => 'nullable|required|integer|digits:9',
            'whatsappPhone' => 'nullable|required|integer|digits:9',
            'idNumber' => ['nullable', 'required_if:clientType,1', 'unique:clients,id_number,' . $id],
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',
            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer',
            'taxNo' => 'nullable|required_if:clientType,2|integer',
        ];
    }


    public function messages()
    {
        return [
            'clientType.required' => 'نوع العميل مطلوب',
            'clientType.integer' => 'نوع العميل يجب ان يكون رقم',
            'idNumber.required' => 'الهويه مطلوبه',
            'idNumber.integer' => 'الهويه يجب ان تكون رقم',
            'idNumber.digits' => 'حقل الهوية يجب ان يكون 10 ارقام',

            'firstName.required_if' => ' حقل الاسم الاول مطلوب ',
            'secondName.required_if' => ' حقل الاسم الثاني مطلوب ',
            'thirdName.required_if' => ' حقل الاسم الثالث مطلوب',
            'forthName.required_if' => 'حقل اللقب مطلوب',

            'phone.required' => 'رقم الهاتف مطلوب .',
            'phone.numeric' => 'رقم الهاتف يجب أن يكون رقمًا.',
            'phone.digits' => 'رقم الهاتف يجب أن يحتوي على 10 أرقام.',

            'anotherPhone.required' => 'حقل الهاتف الثاني مطلوب .',
            'anotherPhone.numeric' => 'حقل الهاتف الثاني يجب أن يكون رقمًا.',
            'anotherPhone.digits' => 'حقل الهاتف الثاني يجب أن يحتوي على 10 أرقام.',

            'whatsappPhone.required' => 'رقم الواتس مطلوب .',
            'whatsappPhone.numeric' => 'رقم الواتس يجب أن يكون رقمًا.',
            'whatsappPhone.digits' => 'رقم الواتس يجب أن يحتوي على 10 أرقام.',

            'companyName.required_if' => 'اسم المؤسسة مطلوب',
            'entityName.required_if' => 'اسم الجهة مطلوب',
            'represents.required_if' => 'اسم المالك مطلوب',
            'commercialRegistrationNo.required_if' => 'رقم السجل مطلوب',
            'taxNo.required_if' => 'رقم الضريبي مطلوب'
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
