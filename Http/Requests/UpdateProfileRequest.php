<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:13', 'max:99'],
            'gender' => ['nullable', 'string', 'max:50'],
            'activity_level' => ['nullable', 'string', 'max:100'],
            'primary_goal' => ['nullable', 'string', 'max:100'],
            'height_cm' => ['nullable', 'numeric', 'min:100', 'max:250'],
            'current_weight_kg' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'target_weight_kg' => ['nullable', 'numeric', 'min:30', 'max:300'],
        ];
    }
}
