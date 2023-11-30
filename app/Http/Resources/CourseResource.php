<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color_code' => $this->color_code,
            'semester' => new SemesterResource($this->whenLoaded("semester")),
            'tasks' => TaskResource::collection($this->whenLoaded("tasks")),
        ];
    }
}
