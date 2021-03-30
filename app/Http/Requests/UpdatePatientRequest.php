<?php

namespace App\Http\Requests;

use App\Rules\FullTimeValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            'doctor' => 'required|string',
            'date' => 'required|string|numeric',
            'time' => ['required', new FullTimeValidation()]
        ];
    }
}
