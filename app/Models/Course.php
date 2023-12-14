<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'name',
        'color_code',
        'teacher',
        'start_date',
        'end_date'
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function school_classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
