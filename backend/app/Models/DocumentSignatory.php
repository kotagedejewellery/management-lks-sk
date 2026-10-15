<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DocumentSignatory extends Model
{
    use HasUuids;

    protected $fillable = ['role', 'title', 'name', 'updated_by'];
}
