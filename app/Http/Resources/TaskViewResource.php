<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $task = Task::find($this->id);
        return [
            'id' => $this->id,
            'task_name' => $this->name,
            'description' => $this->description,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'type' => $this->type,
            'exam_id' => $this->exam_id,
            'course_id' => $this->course_id,
            'course_name' => $this->course_name,
            'color_code' => $this->color_code,
            'semester_name' => $this->semester_name,
            'semester_start_date' => $this->semester_start_date,
            'semester_end_date' => $this->semester_end_date,
            'school_years_start_date' => $this->school_years_start_date,
            'school_years_end_date' => $this->school_years_end_date
        ];
    }
}
