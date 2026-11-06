<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodActivity extends Model
{
    use HasUuids;

    protected $appends = ['applicable_genders_list', 'applicable_levels_list', 'magang_applicable_genders_list', 'magang_applicable_levels_list'];

    protected $fillable = [
        'period_id', 'activity_id', 'activity_code_snapshot', 'activity_name_snapshot',
        'target_count', 'minimum_target_count', 'magang_target_count', 'magang_minimum_target_count',
        'max_per_week', 'weight', 'allowed_weekdays',
        'applicable_genders', 'applicable_levels', 'menstruation_exempt', 'is_active',
        'magang_max_per_week', 'magang_allowed_weekdays', 'magang_applicable_genders',
        'magang_applicable_levels', 'magang_menstruation_exempt', 'magang_is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean', 'menstruation_exempt' => 'boolean', 'weight' => 'decimal:2',
            'magang_is_active' => 'boolean', 'magang_menstruation_exempt' => 'boolean',
        ];
    }

    /** @return BelongsTo<LksPeriod, $this> */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LksPeriod::class, 'period_id');
    }

    /** @return BelongsTo<LksActivity, $this> */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(LksActivity::class, 'activity_id');
    }

    /** @return HasMany<LksChecklist, $this> */
    public function checklists(): HasMany
    {
        return $this->hasMany(LksChecklist::class, 'period_activity_id');
    }

    /** @return list<int> */
    public function allowedWeekdays(): array
    {
        if (is_array($this->allowed_weekdays)) {
            return array_map('intval', $this->allowed_weekdays);
        }

        return $this->allowed_weekdays === null
            ? []
            : array_map('intval', array_filter(explode(',', trim((string) $this->allowed_weekdays, '{}'))));
    }

    /** @return list<int> */
    public function magangAllowedWeekdays(): array
    {
        return $this->intArray($this->magang_allowed_weekdays);
    }

    /** @return list<string> */
    public function applicableGenders(): array
    {
        return $this->stringArray($this->applicable_genders, ['ikhwan', 'akhwat']);
    }

    /** @return list<string> */
    public function applicableLevels(): array
    {
        return $this->stringArray($this->applicable_levels, ['leader', 'staff']);
    }

    /** @return list<string> */
    public function magangApplicableGenders(): array
    {
        return $this->stringArray($this->magang_applicable_genders, ['ikhwan', 'akhwat']);
    }

    /** @return list<string> */
    public function magangApplicableLevels(): array
    {
        return $this->stringArray($this->magang_applicable_levels, ['leader', 'staff']);
    }

    public function isActiveFor(PeriodParticipantSnapshot $participant): bool
    {
        return $this->isMagang($participant) ? $this->magang_is_active : $this->is_active;
    }

    public function maxPerWeekFor(PeriodParticipantSnapshot $participant): ?int
    {
        return $this->isMagang($participant) ? $this->magang_max_per_week : $this->max_per_week;
    }

    /** @return list<int> */
    public function allowedWeekdaysFor(PeriodParticipantSnapshot $participant): array
    {
        return $this->isMagang($participant) ? $this->magangAllowedWeekdays() : $this->allowedWeekdays();
    }

    public function menstruationExemptFor(PeriodParticipantSnapshot $participant): bool
    {
        return $this->isMagang($participant) ? $this->magang_menstruation_exempt : $this->menstruation_exempt;
    }

    public function appliesTo(PeriodParticipantSnapshot $participant): bool
    {
        $genders = $this->isMagang($participant) ? $this->magangApplicableGenders() : $this->applicableGenders();
        $levels = $this->isMagang($participant) ? $this->magangApplicableLevels() : $this->applicableLevels();

        return $this->isActiveFor($participant)
            && in_array($participant->gender_snapshot, $genders, true)
            && in_array($participant->level_snapshot, $levels, true);
    }

    /** @return list<string> */
    public function getApplicableGendersListAttribute(): array
    {
        return $this->applicableGenders();
    }

    /** @return list<string> */
    public function getApplicableLevelsListAttribute(): array
    {
        return $this->applicableLevels();
    }

    /** @return list<string> */
    public function getMagangApplicableGendersListAttribute(): array
    {
        return $this->magangApplicableGenders();
    }

    /** @return list<string> */
    public function getMagangApplicableLevelsListAttribute(): array
    {
        return $this->magangApplicableLevels();
    }

    private function isMagang(PeriodParticipantSnapshot $participant): bool
    {
        return $participant->category_snapshot === 'magang';
    }

    /** @return list<int> */
    private function intArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }

        return $value === null ? [] : array_map('intval', array_filter(explode(',', trim((string) $value, '{}'))));
    }

    /** @param array<int, string> $fallback
     *  @return list<string>
     */
    private function stringArray(mixed $value, array $fallback): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        return $value === null ? $fallback : array_values(array_filter(explode(',', trim((string) $value, '{}'))));
    }
}
