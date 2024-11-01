<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class ContractStoreRequest extends FormRequest
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
            'companyName' => 'required_if:clientType,2|string',
            'entityName' => 'required_if:clientType,3|string',
            'represents' => 'nullable|required_if:clientType,2|string',
            'idNumber' => 'nullable|required_if:clientType,1|integer',
            'commercialRegistrationNo' => 'nullable|required_if:clientType,2|integer|digits:10',
            'taxNo' => 'nullable|required_if:clientType,2|integer|digits:10',
            'building_image' => 'nullable|string',
            'region' => 'required|integer|exists:regions,id',
            'city' => 'required|integer|exists:cities,id',
            'neighborhood' => 'required|string',
            'image' => 'nullable|string',
            'locationBuilding' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',



            
            'taxValue' => 'required|numeric',
            'priceIncludeTax' => 'required|numeric',
            'discountValue' => 'nullable|numeric',
            'elevatorType' => 'required|integer',
            'cabinRailsSize' => 'required|integer',
            'stopsNumber' => 'required|integer',
            'elevatorTrip' => 'required|integer',
            'elevatorWarranty' => 'required|string',
            'entrancesNumber' => 'required|integer',
            'freeMaintenance' => 'required|integer',
            'innerDoorType' => 'required|integer',
            'machineLoad' => 'required|integer',
            'machineSpeed' => 'required|integer',
            'outerDoorDirection' => 'required|integer',
            'peopleLoad' => 'required|integer',
            'totalFreeVisit' => 'required|integer',
            'projectName' => 'nullable|string',
            'street' => 'nullable|string',
            'doorSize' => 'required|integer',
            'controlCard' => 'required|integer',
            'stage' => 'required|integer',
            'elevatorRoom' => 'required|integer',
            'machineWarranty' => 'required|integer',
            'otherAdditions' => 'nullable|array',
            'machineType' => 'required|integer',
            'counterweightRailsSize' => 'required|integer',
            'reachUs' => 'required|integer',
            'webSiteName' => 'nullable|required_if:reachUs,1|string',
            'socialName' => 'nullable|required_if:reachUs,2|string',
            'clients' => 'required_if:reachUs,3|array',
            'clients.*' => 'required_if:reachUs,3|integer',
            'employees' => 'required_if:reachUs,4|array',
            'employees.*' => 'required_if:reachUs,4|integer',
            'representatives' => 'required_if:reachUs,5|array',
            'representatives.*.name' => 'nullable|required_if:reachUs,5|string',
            'representatives.*.phone' => 'nullable|required_if:reachUs,5|string',

            'externalDoorSpecifications' => 'required|array',
            'externalDoorSpecifications.*.floor' => 'required|integer',
            'externalDoorSpecifications.*.door_number' => 'required|integer',

            'externalDoorSpecifications.*.external_door_specifications' => 'required|integer',
            'externalDoorSpecifications.*.door_opening_direction' => 'required|integer',

            'externalDoorSpecifications.*.external_door_specifications2' => 'nullable|required_if:externalDoorSpecifications.*.door_number,2|integer',
            'externalDoorSpecifications.*.door_opening_direction2' => 'nullable|required_if:externalDoorSpecifications.*.door_number,2|integer',

            'paymentStages' => 'required|array',
            'paymentStages.*.amount' => 'required|numeric',
            'paymentStages.*.amountWithTaxed' => 'required|numeric',
            'paymentStages.*.stage' => 'required|integer',
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
            'template.required' => 'قالب التصميم',

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

            'entityName.required_if' => 'اسم الجهة مطلوب',
            'represents.required_if' => 'اسم المالك مطلوب',
            'companyName.required_if' => 'اسم المؤسسة مطلوب',
            'commercialRegistrationNo.required_if' => 'رقم السجل مطلوب',
            'taxNo.required_if' => 'رقم الضريبي مطلوب',

            'region.required' => 'حقل المنطقة مطلوب',
            'region.integer' => 'حقل المنطقة يجب ان يكون رقما',

            'city.required' => 'حقل المدينة مطلوب',
            'city.integer' => 'حقل المدينة يجب ان يكون رقما',

            'neighborhood.required' => 'حقل الحي مطلوب',

            'priceIncludeTax.required' => 'حقل سعر المصعد مطلوب',
            'priceIncludeTax.numeric' => 'حقل المدينة يجب ان يكون رقما',

            'taxValue.required' => 'حقل الضريبة مطلوب',
            'taxValue.numeric' => 'حقل الضريبة يجب يكون رقما',

            'discountValue.numeric' => 'حقل التخفيض يجب يكون رقما',

            'elevatorType.required' => 'حقل نوع المصعد مطلوب',
            'elevatorType.integer' => 'حقل  نوع المصعد ان يكون رقما',

            'cabinRailsSize.required' => 'حقل  مقاس سكك الكبينة مطلوب',
            'cabinRailsSize.integer' => 'حقل مقاس سكك الكبينة يجب ان يكون رقما',

            'stopsNumber.required' => 'حقل عدد الوقفات مطلوب',
            'stopsNumber.integer' => 'حقل عدد الوقفات يجب ان يكون رقما',

            'elevatorTrip.required' => 'حقل مشوار المصعد مطلوب',
            'elevatorTrip.integer' => 'حقل مشوار المصعد يجب ان يكون رقما',

            'elevatorWarranty.required' => 'حقل ضمان المصعد مطلوب',
            'elevatorWarranty.integer' => 'حقل ضمان المصعد يجب ان يكون رقما',

            'entrancesNumber.required' => 'حقل عدد المداخل مطلوب',
            'entrancesNumber.integer' => 'حقل عدد المداخل  يجب ان يكون رقما',

            'freeMaintenance.required' => 'حقل الصيانة المجانية مطلوب',
            'freeMaintenance.integer' => 'حقل الصيانة المجانية يجب ان يكون رقما',

            'innerDoorType.required' => 'حقل الباب الداخلي مطلوب',
            'innerDoorType.integer' => 'حقل الباب الداخلي يجب ان يكون رقما',

            'machineLoad.required' => 'حقل حمولة المكينة مطلوب',
            'machineLoad.integer' => 'حقل حمولة المكينة يجب ان يكون رقما',

            'machineSpeed.required' => 'حقل سرعة المكينة مطلوب',
            'machineSpeed.integer' => 'حقل سرعة المكينة يجب ان يكون رقما',

            'outerDoorDirection.required' => 'حقل الباب الخارجي مطلوب',
            'outerDoorDirection.integer' => 'حقل الباب الخارجي يجب ان يكون رقما',

            'peopleLoad.required' => 'حقل حمولة الاشخاص مطلوب',
            'peopleLoad.integer' => 'حقل حمولة الاشخاص يجب ان يكون رقما',

            'doorSize.required' => 'حقل مقاس الباب مطلوب',
            'doorSize.integer' => 'حقل مقاس الباب يجب ان يكون رقما',

            'totalFreeVisit.required' => 'حقل اجمالي عدد الزيارات مطلوب',
            'totalFreeVisit.integer' => 'حقل اجمالي عدد الزيارات يجب ان يكون رقما',

            'controlCard.required' => 'حقل كرت الكنترول مطلوب',
            'controlCard.integer' => 'حقل كرت الكنترول يجب ان يكون رقما',

            'stage.required' => 'حقل يبدأ من المرحلة مطلوب',
            'stage.integer' => 'حقل يبدأ من المرحلة يجب ان يكون رقما',

            'elevatorRoom.required' => 'حقل غرفة المصعد مطلوب',
            'elevatorRoom.integer' => 'حقل غرفة المصعد يجب ان يكون رقما',

            'machineWarranty.required' => 'حقل ضمان المكينة مطلوب',
            'machineWarranty.integer' => 'حقل ضمان المكينة يجب ان يكون رقما',

            'machineType.required' => 'حقل نوع المكينة مطلوب',
            'machineType.integer' => 'حقل نوع المكينة يجب ان يكون رقما',

            'reachUs.required' => 'حقل كيف وصلت لنا مطلوب',
            'reachUs.integer' => 'حقل كيف وصلت لنا يجب ان يكون رقما',

            'webSiteName.required_if' => 'حقل اسم الموقع مطلوب في حالة طريقة وصولك لنا عن طريق الموقع',
            'socialName.required_if' => 'اسم موقع التواصل الاجتماعي مطلوب في حالة وصولك لنا عن طريق وسائل التواصل الاجتماعي',
            'representatives.required_if' => 'حقل المندوب الخارجي مطلوب وفي شكل مصفوفة',
            'clients.required_if' => ' اسم العميل مطلوب في حالة طريقة الوصول عملي لدى المؤسسة  وفي شكل مصفوفة',
            'employees.required_if' => ' اسم المندوب الداخلي مطلوب وفي شكل مصفوفة في حالة طريقة الوصول عن طريق المندوب الداخلي',
            'representatives.*.name.required_if' => ' حقل اسم المندوب الخارجي مطلوب',
            'representatives.*.phone.required_if' => 'حقل جوال المندوب الخارجي مطلوب',

            'counterweightRailsSize.required' => 'حقل مقاس سكك الثقل مطلوب',
            'counterweightRailsSize.integer' => 'حقل مقاس سكك الثقل يجب ان يكون رقما',

            'externalDoorSpecifications.required' => 'مواصفات الباب الخارجي مطلوب',
            'externalDoorSpecifications.array' => 'مواصفات الباب الخارجي في شكل مصفوفة',
            'externalDoorSpecifications.*.floor.required' => 'حقل الطابق في مواصفات الباب الخارجي مطلوب',
            'externalDoorSpecifications.*.floor.integer' => 'حقل الطابق في مواصفات الباب الخارجي يجب ان تكون رقما',

            'externalDoorSpecifications.*.door_number.required' => 'حقل عدد الابواب  في مواصفات الباب الخارجي مطلوب',
            'externalDoorSpecifications.*.door_number.integer' => 'حقل عدد الابواب في مواصفات الباب الخارجي يجب ان تكون رقما',

            'externalDoorSpecifications.*.door_opening_direction.required_if' => 'حقل اتجاه فتح الباب الثاني اجباري',
            'externalDoorSpecifications.*.door_opening_direction2.required_if' => 'حقل اتجاه فتح الباب الثاني اجباري في حالة عدد الابواب اثنين',

            'externalDoorSpecifications.*.external_door_specifications.required_if' => ' حقل موصفات الباب الخارجي  اجباري',
            'externalDoorSpecifications.*.external_door_specifications2.required_if' => ' حقل موصفات الباب الخارجي الثاني اجباري في حالة عدد الابواب اثنين',

            'paymentStages.required' => 'البيانات المالية مطلوبة',
            'paymentStages.*.amount.required' => 'حقل الدفعة غير شامل الضريبة مطلوب',
            'paymentStages.*.amount.numeric' => 'حقل الدفعة غير شامل الضريبة يجب ان يكون رقما',

            'paymentStages.*.amountWithTaxed.required' => 'حقل الدفعة  شامل الضريبة مطلوب',
            'paymentStages.*.amountWithTaxed.numeric' => 'حقل الدفعة  شامل الضريبة يجب ان يكون رقما',

            'paymentStages.*.stage.required' => 'حقل اسم الدفعة مطلوب',
            'paymentStages.*.stage.integer' => 'حقل اسم الدفعة يجب ان يكون رقما',

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
