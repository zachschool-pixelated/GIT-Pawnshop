<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . ($this->customer?->id ?? 'NULL'),
            'phone' => 'required|digits:11|regex:/^09\d{9}$/|unique:customers,phone,' . ($this->customer?->id ?? 'NULL'),
            'address' => 'required|string|max:500',
            'id_type' => 'required|in:national_id,passport,driver_license',
            'id_number' => 'required|string|unique:customers,id_number,' . ($this->customer?->id ?? 'NULL'),
            'occupation' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.digits' => 'Phone number must be exactly 11 digits.',
            'phone.regex' => 'Phone number must be a valid Philippine mobile number (09XXXXXXXXX).',
            'phone.unique' => 'Phone number is already registered.',
        ];
    }
}
