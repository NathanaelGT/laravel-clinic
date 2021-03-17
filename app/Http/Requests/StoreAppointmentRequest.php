<?php

namespace App\Http\Requests;

use App\Rules\FullTimeValidation;
use App\Rules\PhoneNumberValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|digits_between:8,15',
            'phone-number' => ['required', new PhoneNumberValidation()],
            'address' => 'required|string',
            'doctor' => 'required|string',
            'date' => 'required|string|numeric',
            'time' => ['required', new FullTimeValidation()]
        ];
    }
}
