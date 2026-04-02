<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFoodLogEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meal_slot' => ['required', 'string', 'max:50'],
            'food_item_id' => ['nullable', 'integer', 'exists:food_items,id'],
            'food_name' => ['nullable', 'string', 'max:100', 'required_without:food_item_id'],
            'grams' => ['nullable', 'integer', 'min:1', 'max:5000', 'required_without:food_item_id'],
            'calories' => ['nullable', 'integer', 'min:0', 'max:10000', 'required_without:food_item_id'],
            'protein' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'serving_label' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn (): bool => $this->filled('food_item_id')),
            ],
        ];
    }
}
