<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealPlanItemRequest extends FormRequest
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
            'item_name' => ['required_without:food_item_id', 'string', 'max:255'],
            'serving_label' => ['required', 'string', 'max:100'],
        ];
    }
}
