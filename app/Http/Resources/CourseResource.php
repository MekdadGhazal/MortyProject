<?php

namespace App\Http\Resources;

use App\Models\User;
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
            'id'=> $this->id,
            'teacher'=> User::find($this->teacher_id)->name,
            'title' => $this->title,
            'description' => $this->description,
            'level' => $this->level,
            'price' => $this->price,
            'hours' => $this->hours,
            'image' => $this->image,
            'create_at' => $this->create_at,
        ];
    }
}
