<?php

namespace App\Http\Resources;

use App\Http\Controllers\API\Courses\CourseCommentController;
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
        $commentsController = new CourseCommentController();

        $user = new UserResource($this->user);
        return [
            'user' => $user,
            'comment_id' =>$this->id,
            'content' => $this->content,
            'created_at' => $this->created_at->diffForHumans(),
            'replay' => isset($this ->replay)? $this ->replay: 0,
            'replies' =>$commentsController->replies($this->id),
        ];
    }
}
