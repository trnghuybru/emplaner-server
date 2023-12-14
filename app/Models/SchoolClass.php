<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'room',
        'date',
        'start_time',
        'end_time',
        'day_of_week'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
