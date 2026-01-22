<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = ['school_id', 'name'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
