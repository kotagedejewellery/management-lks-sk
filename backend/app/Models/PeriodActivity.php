<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodActivity extends Model
{
    use HasUuids;

    protected $appends = ['applicable_genders_list', 'applicable_levels_list'];

    protected $fillable = [
        'period_id', 'activity_id', 'activity_code_snapshot', 'activity_name_snapshot',
        'target_count', 'minimum_target_count', 'max_per_week', 'weight', 'allowed_weekdays',
        'applicable_genders', 'applicable_levels', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'weight' => 'decimal:2'];
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

    public function appliesTo(PeriodParticipantSnapshot $participant): bool
    {
        return in_array($participant->gender_snapshot, $this->applicableGenders(), true)
            && in_array($participant->level_snapshot, $this->applicableLevels(), true);
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
