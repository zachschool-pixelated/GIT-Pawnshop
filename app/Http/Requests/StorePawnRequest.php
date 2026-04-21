<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePawnRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string|max:1000',
            'category' => 'required|string|max:100',
            'assessed_value' => 'required|numeric|min:0.01',
            'condition' => 'required|in:excellent,good,fair,poor',
            'loan_amount' => 'required|numeric|min:0.01',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_days' => 'required|integer|min:1|max:365',
        ];
    }
}
