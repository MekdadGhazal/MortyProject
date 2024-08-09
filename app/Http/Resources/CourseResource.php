<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         *  the subscribed has three values:
         *  0: if user is not login in site
         *  1: if user logged in and not subscribed in the course yet
         *  2: if user logged in successfully and subscribed in course
         */
        $subscribe = 0 ;
        if(auth()->user()){
            $subscribe =  (DB::table('user_course_pivot')->where('course_id', $this->id)->where('user_id', auth()->user()->id)->first()) ? 2 : 1 ;
        }
        return [
            'id'=> $this->id,
            'teacher'=> User::find($this->teacher_id)->name,
            'teacher_id'=> $this->teacher_id,
            'title' => $this->title,
            'description' => $this->description,
            'level' => $this->level,
            'price' => $this->price,
            'hours' => $this->hours,
            'image' => 'http://127.0.0.1:8000/images/'.$this->image,
            'create_at' => $this->create_at,
            'subscribe' => $subscribe
        ];
    }
}
