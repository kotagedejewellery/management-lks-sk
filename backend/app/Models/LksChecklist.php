<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LksChecklist extends Model
{
    use HasUuids;

    protected $fillable = [
        'period_participant_id', 'period_activity_id', 'checklist_date',
        'is_completed', 'recorded_by_user_id',
    ];

    protected function casts(): array
    {
        return ['checklist_date' => 'date', 'is_completed' => 'boolean'];
    }

    /** @return BelongsTo<PeriodParticipantSnapshot, $this> */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(PeriodParticipantSnapshot::class, 'period_participant_id');
    }

    /** @return BelongsTo<PeriodActivity, $this> */
    public function periodActivity(): BelongsTo
    {
        return $this->belongsTo(PeriodActivity::class);
    }

    /** @return BelongsTo<User, $this> */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
