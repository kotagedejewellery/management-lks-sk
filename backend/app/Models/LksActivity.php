<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LksActivity extends Model
{
    use HasUuids;

    protected $fillable = ['code', 'name', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @return HasMany<PeriodActivity, $this> */
    public function periodActivities(): HasMany
    {
        return $this->hasMany(PeriodActivity::class, 'activity_id');
    }
}
