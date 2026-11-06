<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodMenstruationDispensation extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = ['period_participant_id', 'start_date', 'end_date', 'recorded_by_user_id'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    /** @return BelongsTo<PeriodParticipantSnapshot, $this> */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(PeriodParticipantSnapshot::class, 'period_participant_id');
    }

    /** @return BelongsTo<User, $this> */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    /** @return list<string> */
    public static function datesFor(PeriodParticipantSnapshot $participant): array
    {
        if ($participant->gender_snapshot !== 'akhwat') {
            return [];
        }

        return $participant->menstruationDispensations
            ->flatMap(function (self $dispensation): array {
                $dates = [];
                for ($date = $dispensation->start_date->copy(); $date->lte($dispensation->end_date); $date->addDay()) {
                    $dates[] = $date->toDateString();
                }

                return $dates;
            })
            ->unique()
            ->values()
            ->all();
    }
}
