<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'duration' => $this->duration,
            'room' => $this->room,
            'course' => [
                'id' => $this->course_id,
                'name' => $this->course_name,
                'color_code' => $this->color_code,
            ],
            'semester' => [
                'id' => $this->semesters_id,
                'name' => $this->semester_name,
                'start_date' => $this->semester_start_date,
                'end_date' => $this->semester_end_date,
            ],
            'school_year' => [
                'id' => $this->school_years_id,
                'start_date' => $this->school_years_start_date,
                'end_date' => $this->school_years_end_date,
            ],
            'user_id' => $this->user_id,
        ];
    }
}
