<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'type',
        'exam_id'
    ];
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
