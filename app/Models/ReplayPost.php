<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplayPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'comment_id',
        'user_id'
    ];

    public function comment(){
        return $this->belongsTo(PostComment::class , 'comment_id');
    }
}
