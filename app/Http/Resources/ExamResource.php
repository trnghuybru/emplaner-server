<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "start_date" => $this->start_date,
            "start_time" => $this->start_time,
            "duration" => $this->duration,
            "room" => $this->room,
            "course" => new CourseResource($this->whenLoaded("course")),
            "tasks" => TaskResource::collection($this->whenLoaded("tasks"))
        ]; 
    }
}
