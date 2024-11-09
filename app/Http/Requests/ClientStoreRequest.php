<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class ClientStoreRequest extends FormRequest
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
<<<<<<< HEAD
            'clientType' =>  'required', 'integer',
=======
            'clientType' => 'required|in:1,2,3',
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            'firstName' => 'nullable|required_if:clientType,1|string',
            'secondName' => 'nullable|required_if:clientType,1|string',
            'thirdName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'nullable|digits:9|unique:clients,phone',
<<<<<<< HEAD
            'idNumber' => ['nullable:required_if:clientsType,1', 'digits:10', 'unique:clients,id_number'],
            'taxNo' => 'nullable|required_if:clientType,2|integer|digits:10',
=======
            'idNumber' => ['nullable:required_if:clientType,1', 'unique:clients,id_number'],
            'taxNo' => 'nullable|required_if:clientType,2|integer',
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer|unique:clients,id_number',
            'anotherPhone' => 'nullable|required|digits:9',
            'whatsappPhone' => 'nullable|required|digits:9',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',
<<<<<<< HEAD

=======
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
        ];
    }

    public function messages()
    {
        return [

            'clientType.required' => 'نوع العميل مطلوب',
            'clientType.integer' => 'نوع العميل يجب ان يكون رقم',
<<<<<<< HEAD
            'idNumber.required' => 'الهويه مطلوبه',
            'idNumber.integer' => 'الهويه يجب ان تكون رقم',
            'idNumber.digits' => 'حقل الهوية يجب ان يكون 10 ارقام',
=======

            'idNumber.required' => 'الهويه مطلوبه',
            'idNumber.integer' => 'الهويه يجب ان تكون رقم',
            'idNumber.digits' => 'حقل الهوية يجب ان يكون 9 ارقام',
            'idNumber.unique' => 'رقم الهوية المدخل مستخدم من قبل عميل اخر',
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7

            'firstName.required_if' => ' حقل الاسم الاول مطلوب ',
            'secondName.required_if' => ' حقل الاسم الثاني مطلوب ',
            'thirdName.required_if' => ' حقل الاسم الثالث مطلوب',
            'forthName.required_if' => 'حقل اللقب مطلوب',

            'phone.required' => 'رقم الهاتف مطلوب .',
<<<<<<< HEAD

=======
            'phone.unique' => 'رقم الجوال المدخل مستخدم من قبل عميل اخر',
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            'phone.digits' => 'رقم الهاتف يجب أن يحتوي على 9 أرقام.',

            'anotherPhone.required' => 'حقل الهاتف الثاني مطلوب .',

            'anotherPhone.digits' => 'حقل الهاتف الثاني يجب أن يحتوي على 9 أرقام.',

            'whatsappPhone.required' => 'رقم الواتس مطلوب .',

            'whatsappPhone.digits' => 'رقم الواتس يجب أن يحتوي على 9 أرقام.',

            'companyName.required_if' => 'اسم المؤسسة مطلوب',
            'entityName.required_if' => 'اسم الجهة مطلوب',
            'represents.required_if' => 'اسم المالك مطلوب',
            'commercialRegistrationNo.required_if' => 'رقم السجل مطلوب',
            'taxNo.required_if' => 'رقم الضريبي مطلوب',
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
