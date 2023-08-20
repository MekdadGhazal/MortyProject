<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCommentReplay extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'content',
    ];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function comment(){
        return $this->belongsTo(CourseComment::class);
    }
}
