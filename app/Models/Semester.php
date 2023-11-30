<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;
    
    public function school_year(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
