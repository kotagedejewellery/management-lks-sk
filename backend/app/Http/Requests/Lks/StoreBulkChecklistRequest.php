<?php

namespace App\Http\Requests\Lks;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->isAdmin();
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'period_activity_id' => ['required', 'uuid', 'exists:period_activities,id'],
            'checklist_dates' => ['required', 'array', 'min:1', 'max:366'],
            'checklist_dates.*' => ['required', 'date', 'distinct'],
        ];
    }
}
