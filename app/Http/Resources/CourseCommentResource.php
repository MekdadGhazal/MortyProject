<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = new UserResource($this->user);
        return [
            'user' => $user,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'replay' => $this ->replay,
        ];
    }
}
