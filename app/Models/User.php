<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verify',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function admin(){
        return $this->hasOne(Admin::class);
    }

    /**
     *  the event that Created by a user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(){
        return $this->hasMany(Event::class);
    }

    /**
     *  the name of course/s that user has joined in it
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses(){
        return $this->belongsToMany(Course::class, 'user_course_pivot');
    }

    /**
     *  the name of course/s that created by a user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracherCoursesName(){
        return $this->hasMany(Course::class,'teacher_id');
    }


    public function comment(){
        return $this->hasMany(CourseComment::class);
    }
    public function Replay(){
        return $this->hasMany(CourseCommentReplay::class);
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }




}
