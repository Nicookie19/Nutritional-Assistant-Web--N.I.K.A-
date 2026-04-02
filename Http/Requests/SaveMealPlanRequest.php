<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveMealPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'daily_calories' => ['required', 'integer', 'min:1200', 'max:6000'],
            'tags' => ['nullable', 'string', 'max:255'],
        ];
    }
}
