<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'course_id' => $this->course_id,
            'course_name' => $this->course_name,
            'color_code' => $this->color_code,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }
}
