<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CalendarHoliday extends Model
{
    use HasUuids;

    protected $fillable = ['holiday_date', 'name', 'type'];

    protected function casts(): array
    {
        return ['holiday_date' => 'date'];
    }
}
