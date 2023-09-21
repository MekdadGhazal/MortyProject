<?php

namespace App\Http\Resources;

use App\Http\Controllers\ReplayPostController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostReplayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $commentsController = new ReplayPostController();

        $user = new UserResource($this->user);
        return [
            'user' => $user,
            'comment_id' =>$this->id,
            'content' => $this->content,
            'created_at' => isset($this->created_at)?$this->created_at->diffForHumans():0,
            'replay' => $this ->replay,
            'replies' =>$commentsController->replies($this->id),
        ];
    }
}
