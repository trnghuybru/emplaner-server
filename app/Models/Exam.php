<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'start_date',
        'start_time',
        'duration',
        'room',
    ];



    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function type_task(): HasOne
    {
        return $this->hasOne(TypeTask::class);
    }

    public function scopeUserExams($query, $user, $date)
{
    return $query->whereHas('course', function ($query) use ($user) {
        $query->whereHas('semester', function ($query) use ($user) {
            $query->whereHas('school_year', function ($query) use ($user) {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            });
        });
    })->where('start_date','=', $date);
}

}

