<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LksDocumentVerification extends Model
{
    use HasUuids;

    protected $fillable = ['period_id', 'participant_id', 'public_token', 'document_code', 'issued_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    /** @return BelongsTo<LksPeriod, $this> */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LksPeriod::class, 'period_id');
    }
}
