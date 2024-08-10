<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MaintenanceInfoStoreResquest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clientType' =>  'required', 'integer',
            'firstName' => 'nullable|required_if:clientType,1|string',
            'secondName' => 'nullable|required_if:clientType,1|string',
            'thirdName' => 'nullable|required_if:clientType,1|string',
            'forthName' => 'nullable|required_if:clientType,1|string',
            'phone' => 'nullable|required|integer|digits:9',
            'anotherPhone' => 'nullable|required|integer|digits:9',
            'whatsappPhone' => 'nullable|required|integer|digits:9',
            'idNumber' => 'required|integer|digits:9',
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',
            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer|digits:9',
            'taxNo'          => 'nullable|required_if:clientType,2|integer|digits:9',
            'image' => 'nullable|string',
            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'locationBuilding' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',



            'contract_id' => 'nullable|integer|exists:contracts,id', // رقم العقد
            'elevatorType' => 'required|integer|exists:elevator_types,id',
            'buildingType' => 'required|integer|exists:building_types,id',
            'stopsNumber' => 'required|integer|exists:stops_numbers,id',
            'machineSpeed' => 'required|integer|exists:machine_speeds,id',
            'project_name' => 'nullable|string',
            'street' => 'nullable|string',
            'doorSize' => 'required|integer|exists:door_sizes,id',
            'controlCard' => 'required|integer|exists:control_cards,id',
            'machineType' => 'required|integer|exists:machine_types,id',
            'building_image' => 'nullable|string',

            'maintenanceType' => 'required|integer|exists:maintenance_types,id',

            'reachUs' => 'required|integer',
            'websiteName' => 'nullable|required_if:reachUs,1|string',
            'socialName' => 'nullable|required_if:reachUs,2|string',

            'clients' => 'required_if:reachUs,3|array',
            'clients.*' => 'required_if:reachUs,3|integer',
            'employees' => 'required_if:reachUs,4|array',
            'employees.*' => 'required_if:reachUs,4|integer',
            'externalRepresentative' => 'required_if:reachUs,5|array',
            'externalRepresentative.*.name' => 'nullable|required_if:reachUs,5|string',
            'externalRepresentative.*.phone' => 'nullable|required_if:reachUs,5|string',

            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d',

            'totalVisit' => 'required|integer',
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
            'clientType.required' => 'نوع العميل مطلوب',
            'clientType.integer' => 'نوع العميل يجب ان يكون رقم',
            'idNumber.required' => 'الهويه مطلوبه',
            'idNumber.integer' => 'الهويه يجب ان تكون رقم',
            'idNumber.digits' => 'حقل الهوية يجب ان يكون 10 ارقام',

            'firstName.required_if' => ' الاسم الاول مطلوب في حالة نوع العميل فرد',
            'secondName.required_if' => 'الاسم الثاني مطلوب في حالة نوع العميل فرد',
            'thirdName.required_if' => 'الاسم الثالث مطلوب في حالة نوع العميل فرد',
            'forthName.required_if' => 'اللقب مطلوب في حالة نوع العميل فرد',

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
            'taxNo.required_if' => 'رقم الضريبي مطلوب',

            'reachUs.required' => 'حقل كيف وصلت لنا مطلوب',
            'reachUs.integer' => 'حقل كيف وصلت لنا يجب ان يكون رقما',

            'websiteName.required_if' => 'حقل اسم الموقع مطلوب في حالة طريقة وصولك لنا عن طريق الموقع',
            'socialName.required_if' => 'اسم موقع التواصل الاجتماعي مطلوب في حالة وصولك لنا عن طريق وسائل التواصل الاجتماعي',
            'externalRepresentative.required_if' => 'حقل المندوب الخارجي مطلوب وفي شكل مصفوفة',
            'clients.required_if' => ' اسم العميل مطلوب في حالة طريقة الوصول عملي لدى المؤسسة  وفي شكل مصفوفة',
            'employees.required_if' => ' اسم المندوب الداخلي مطلوب وفي شكل مصفوفة في حالة طريقة الوصول عن طريق المندوب الداخلي',
            'externalRepresentative.*.name.required_if' => ' حقل اسم المندوب الخارجي مطلوب',
            'externalRepresentative.*.phone.required_if' => 'حقل جوال المندوب الخارجي مطلوب',

            'region.required' => 'حقل المنطقة مطلوب',
            'region.integer' => 'حقل المنطقة يجب ان يكون رقما',
            'region.exists' => 'Not an existing ID',

            'city.required' => 'حقل المدينة مطلوب',
            'city.integer' => 'حقل المدينة يجب ان يكون رقما',
            'city.exists' => 'Not an existing ID',

            'neighborhood.required' => 'حقل الحي يجب ان يكون نص',
            'lat.numeric' => 'حقل  خط الطول يجب ان يكون رقما',
            'long.numeric' => 'حقل  خط العرض يجب ان يكون رقما',

            'elevatorType.required' => 'حقل نوع المصعد مطلوب',
            'elevatorType.integer' => 'حقل  نوع المصعد ان يكون رقما',
            'elevatorType.exists' => 'Not an existing ID',

            'buildingType.required' => 'حقل نوع المبنى مطلوب',
            'buildingType.integer' => 'حقل  نوع المبنى ان يكون رقما',
            'buildingType.exists' => 'Not an existing ID',

            'stopsNumber.required' => 'حقل عدد الوقفات مطلوب',
            'stopsNumber.integer' => 'حقل عدد الوقفات يجب ان يكون رقما',
            'stopsNumber.exists' => 'Not an existing ID',

            'machineSpeed.required' => 'حقل سرعة المكينة مطلوب',
            'machineSpeed.integer' => 'حقل سرعة المكينة يجب ان يكون رقما',
            'machineSpeed.exists' => 'Not an existing ID',

            'doorSize.required' => 'حقل مقاس الباب مطلوب',
            'doorSize.integer' => 'حقل مقاس الباب يجب ان يكون رقما',
            'doorSize.exists' => 'Not an existing ID',

            'totalVisit.required' => 'حقل اجمالي عدد الزيارات مطلوب',
            'totalVisit.integer' => 'حقل اجمالي عدد الزيارات يجب ان يكون رقما',
            'totalVisit.exists' => 'Not an existing ID',

            'controlCard.required' => 'حقل كرت الكنترول مطلوب',
            'controlCard.integer' => 'حقل كرت الكنترول يجب ان يكون رقما',
            'controlCard.exists' => 'Not an existing ID',

            'machineType.required' => 'حقل نوع المكينة مطلوب',
            'machineType.integer' => 'حقل نوع المكينة يجب ان يكون رقما',
            'machineType.exists' => 'Not an existing ID',

            'maintenanceType.required' => 'حقل نوع عقد الصيانة مطلوب',
            'maintenanceType.integer' => 'حقل نوع عقد الصيانة يجب ان يكون رقما',


            'startDate.required' => 'حقل تاريخ البداية مطلوب',
            //  'started_date.date' => 'حقل تاريخ البداية يجب ان يكون تاريخ',

            'endDate.required' => 'حقل تاريخ النهاية  مطلوب',
            // 'ended_date.date' => 'حقل تاريخ النهاية يجب ان يكون رقما',
            'amount.required' => 'حقل التكلفة  مطلوب',
            'amount.numeric' => 'حقل التكلفة يجب ان يكون رقما',



        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'failed',
            'message' => 'unprocessable entity',
            'error' => $validator->errors(),
        ], 422));


        // throw new HttpResponseException($this->response(
        //     $this->formatErrors($validator)
        // ));
    }
}
