<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MalfunctionResponseStoreRequest extends FormRequest
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
            //
            //'id' => 'required|exists:malfunctions,id',
            'mal_type_id' => 'required|string',
            //   'malfunction_images' => 'nullable|string',
            'malfunction_videos' => 'nullable|string',
            'status_id' => 'required|exists:malfunction_statuses,id',
            'cost' => 'required|numeric',
            //  'elevators_parts' => 'required|json',
            'notes' => 'nullable|string'
        ];
    }
}
