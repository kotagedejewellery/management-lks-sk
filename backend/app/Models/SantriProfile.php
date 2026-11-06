<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SantriProfile extends Model
{
    protected $appends = ['current_lks_category'];
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'gender', 'department_id', 'team_id', 'leader_user_id',
        'category', 'employment_started_on', 'level', 'status',
    ];

    protected function casts(): array
    {
        return ['employment_started_on' => 'date'];
    }

    public function lksCategoryAt(Carbon|string $date): string
    {
        if ($this->employment_started_on === null) {
            return $this->category === 'magang' ? 'magang' : 'regular';
        }

        return Carbon::parse($date)->startOfDay()->lt($this->employment_started_on->copy()->startOfDay()->addMonthsNoOverflow(3))
            ? 'magang'
            : 'regular';
    }

    public function getCurrentLksCategoryAttribute(): string
    {
        return $this->lksCategoryAt(today());
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<User, $this> */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }
}
