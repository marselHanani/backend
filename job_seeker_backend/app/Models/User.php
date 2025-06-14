<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail , JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'image',
        'role_id',
        'is_verified',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function jobs() {
        return $this->belongsToMany(PostJob::class, 'job_user');
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function jobApplications() {
        return $this->hasMany(JobApplication::class);
    }

    public function posts() {
        return $this->hasOne(Employer::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_id' => $this->id,
            'username' => $this->username,
            'role' => $this->role->name,
        ];
    }
}
