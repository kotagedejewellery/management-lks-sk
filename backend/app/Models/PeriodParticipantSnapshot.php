<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodParticipantSnapshot extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = [
        'period_id', 'user_id', 'participant_name_snapshot', 'gender_snapshot',
        'department_id_snapshot', 'department_name_snapshot', 'team_id_snapshot',
        'team_name_snapshot', 'leader_user_id_snapshot', 'leader_name_snapshot',
        'category_snapshot', 'level_snapshot', 'participation_start_date',
    ];

    protected function casts(): array
    {
        return ['participation_start_date' => 'date'];
    }

    /** @return BelongsTo<LksPeriod, $this> */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LksPeriod::class, 'period_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<LksChecklist, $this> */
    public function checklists(): HasMany
    {
        return $this->hasMany(LksChecklist::class, 'period_participant_id');
    }
}
