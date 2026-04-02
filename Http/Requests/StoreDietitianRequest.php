<?php

namespace App\Http\Requests;

use App\Models\Dietitian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDietitianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Dietitian|null $dietitian */
        $dietitian = $this->route('dietitian');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('dietitians', 'email')->ignore($dietitian)],
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
        ];
    }
}
