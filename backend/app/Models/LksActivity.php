<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LksActivity extends Model
{
    use HasUuids;

    protected $fillable = [
        'code', 'name', 'description', 'default_target_count', 'default_minimum_target_count',
        'default_magang_target_count', 'default_magang_minimum_target_count',
        'default_max_per_week', 'default_allowed_weekdays', 'default_applicable_genders',
        'default_applicable_levels', 'default_menstruation_exempt', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'default_menstruation_exempt' => 'boolean'];
    }

    /** @return HasMany<PeriodActivity, $this> */
    public function periodActivities(): HasMany
    {
        return $this->hasMany(PeriodActivity::class, 'activity_id');
    }
}
