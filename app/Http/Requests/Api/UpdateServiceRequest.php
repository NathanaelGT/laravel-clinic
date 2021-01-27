<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'doctorServiceId' => 'integer|min:1',
            'day' => 'required_with:doctor_service_id|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'quota' => 'required|integer|min:1',
            'timeStart' => 'required|string|size:5',
            'timeEnd' => 'required|string|size:5'
        ];
    }
}
