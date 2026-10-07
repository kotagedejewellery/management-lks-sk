<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LksPeriod extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'status', 'final_passing_threshold',
        'activated_at', 'closed_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'activated_at' => 'datetime',
            'closed_at' => 'datetime',
            'final_passing_threshold' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<PeriodActivity, $this> */
    public function periodActivities(): HasMany
    {
        return $this->hasMany(PeriodActivity::class, 'period_id');
    }

    /** @return HasMany<PeriodParticipantSnapshot, $this> */
    public function participants(): HasMany
    {
        return $this->hasMany(PeriodParticipantSnapshot::class, 'period_id');
    }

    /** @return HasMany<PeriodHolidaySnapshot, $this> */
    public function holidaySnapshots(): HasMany
    {
        return $this->hasMany(PeriodHolidaySnapshot::class, 'period_id');
    }
}
