<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'serviceName' => 'required|string',
            'doctorName' => 'required|string',
            'time' => 'required|array|min:1',
            'time.*' => 'required|array|size:2',
            'time.*.*' => 'required|string|size:5',
            'quota' => 'required|array|min:1',
            'quota.*' => 'required|integer|min:1',
            'day' => 'required|array|min:1',
            'day.*' => 'required|array|min:1',
            'day.*.*' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'
        ];
    }
}
