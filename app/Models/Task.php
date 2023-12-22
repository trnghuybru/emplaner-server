<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status'
    ];
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function type_task(): HasOne
    {
        return $this->hasOne(TypeTask::class);
    }

    // public function scopeTitle(EloquentBuilder $query, string $title): EloquentBuilder
    // {
    //     return $query->where('name','LIKE','%'.$title.'%');
    // }

}
