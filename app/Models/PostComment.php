<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'post_id',
        'replay',
    ];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
    public function post(){
        return $this->belongsTo(Post::class , 'post_id');
    }


    public function replies(){
        return $this->hasMany(ReplayPost::class,'comment_id');
    }
}
