<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->class_id,
            'teacher' => $this->teacher,
            'room' => $this->room,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "day_of_week" => $this->day_of_week,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "course_name" => $this->course_name

        ];
    }
}
