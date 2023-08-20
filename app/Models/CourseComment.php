<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'content',
        'replay'
    ];

    /**
     *  Get the course that comment related to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }

    /**
     *
     *  Get the user that comment on the course
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }


    public function replies(){
        return $this->hasMany(CourseCommentReplay::class,'comment_id');
    }
}
