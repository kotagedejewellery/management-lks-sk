<?php

namespace App\Http\Requests\Lks;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'period_activity_id' => ['required', 'uuid', 'exists:period_activities,id'],
            'participant_id' => ['nullable', 'uuid', 'exists:period_participant_snapshots,id'],
            'checklist_date' => ['required', 'date'],
            'is_completed' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
