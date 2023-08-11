<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'level',
        'hours',
        'teacher_id',
        'image',
        'price',
    ];

    /**
     *
     *  The Students are Joined to Course
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     */
    public function users(){
        return $this->belongsToMany(User::class,'user_course_pivot');
    }

    /**
     *  The Teacher of Course
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher(){
        return $this->belongsTo(User::class);
    }
}
