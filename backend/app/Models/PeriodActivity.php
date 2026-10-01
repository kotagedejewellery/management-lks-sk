<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodActivity extends Model
{
    use HasUuids;

    protected $fillable = [
        'period_id', 'activity_id', 'activity_code_snapshot', 'activity_name_snapshot',
        'target_count', 'weight', 'allowed_weekdays', 'is_active', 'sort_order',
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
}
