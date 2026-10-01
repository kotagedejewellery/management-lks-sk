<?php

namespace App\Http\Requests\Lks;

use Illuminate\Foundation\Http\FormRequest;

class ActivatePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }
}
