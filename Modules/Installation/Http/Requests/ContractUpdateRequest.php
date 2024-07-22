<?php

namespace Modules\Installation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class ContractUpdateRequest extends FormRequest
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
            'cost' => ['required', 'numeric', 'between:-999999.99,999999.99'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'project_name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'elevator_type_id' => ['required', 'integer', 'exists:elevator_types,id'],
            'elevator_rail_id' => ['required', 'integer', 'exists:elevator_rails,id'],
            'number_of_stops' => ['required', 'integer'],
            'elevator_journey' => ['required', 'integer'],
            'elevator_room_id' => ['required', 'integer', 'exists:elevator_rooms,id'],
            'elevator_weight_id' => ['required', 'integer', 'exists:elevator_weights,id'],
            'machine_type_id' => ['required', 'integer', 'exists:machine_types,id'],
            'machine_warranty' => ['required', 'integer'],
            'machine_load_id' => ['required', 'integer', 'exists:machine_loads,id'],
            'machine_speed' => ['required', 'string'],
            'people_load' => ['required', 'integer'],
            'control_card' => ['required', 'string'],
            'number_of_stages' => ['required', 'integer'],
            'door_opening_direction_id' => ['required', 'integer', 'exists:door_opening_directions,id'],
            'door_opening_size_id' => ['required', 'integer', 'exists:door_opening_sizes,id'],
            'elevator_warranty' => ['required', 'integer'],
            'free_maintenance' => ['required', 'integer'],
            'total_number_of_visits' => ['required', 'integer'],
            'how_did_you_get_to_us' => ['required', 'string'],
            'contract_status' => ['required', 'in:Draft,Completed,Other'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
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
