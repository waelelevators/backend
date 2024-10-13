<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'maintenance_contract_id' => 'required|exists:maintenance_contracts,id',
            'notes' => 'required|string',
        ];
    }
}