<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceContractStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'contract_number' => 'required|unique:maintenance_contracts,contract_number',
<<<<<<< HEAD
            'area_id' => 'required|exists:areas,id',
            'contract_type' => 'in:contract,draft',
            'total' => 'numeric',
=======
            'area_id' => 'nullable|exists:areas,id',
            'contract_type' => 'in:contract,draft',
            'total' => 'numeric',
            'region_id' => 'required|exists:regions,id',
>>>>>>> 1ebb111 (Maintenance Part)
            'city_id' => 'required|exists:cities,id',
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
<<<<<<< HEAD
            'client_id' => 'required|exists:clients,id',
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'building_type_id' => 'required|exists:building_types,id',
            'stops_count' => 'required|integer',
=======
            // 'client_id' => 'required|exists:clients,id',
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'machine_type_id' => 'required|exists:machine_types,id',
            'machine_speed_id' => 'required|exists:machine_speeds,id',

            'door_size_id' => 'required|exists:door_sizes,id',
            'stops_count' => 'required|exists:stops_numbers,id',
            'drive_type_id' => 'required|exists:drive_types,id',
            'control_card_id' => 'required|exists:control_cards,id',

            'building_type_id' => 'required|exists:building_types,id',

>>>>>>> 1ebb111 (Maintenance Part)
            'has_window' => 'boolean',
            'has_stairs' => 'boolean',
            'site_images' => 'nullable|array', // تغيير هنا
            // 'site_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // إضافة قاعدة جديدة للتحقق من كل صورة
<<<<<<< HEAD
            // 'start_date' => 'required',
            // 'end_date' => 'required',
            // 'visits_count' => 'required|integer',
=======
            //'start_date' => 'required',
            //  'end_date' => 'required',
            'visits_count' => 'required|integer',
>>>>>>> 1ebb111 (Maintenance Part)
            // 'cost' => 'required|numeric',
            'notes' => 'nullable|string',
            'cancellation_allowance' => 'nullable|numeric',
            'receipt_attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif|max:2048',
            'contract_attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif|max:2048',
        ];
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 1ebb111 (Maintenance Part)
