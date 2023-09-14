<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddVideoNotifyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'video_id' => isset($this->data['video_id']) ? $this->data['video_id'] : '0',
            'title' => $this->data['title'],
            'description' => $this->data['description'],
            'teacher_info' =>new UserResource(User::find($this->data['teacher_id'])),
            'read_at' => $this->read_at ,
            'created_at' => $this ->created_at
        ];
    }
}
