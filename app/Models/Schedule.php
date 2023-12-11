<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'day_of_week',
        'start_time',
        'end_time'
    ];
    public function school_class(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
