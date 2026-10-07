<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodHolidaySnapshot extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = ['period_id', 'holiday_date', 'name', 'type'];

    protected function casts(): array
    {
        return ['holiday_date' => 'date'];
    }

    /** @return BelongsTo<LksPeriod, $this> */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LksPeriod::class, 'period_id');
    }
}
