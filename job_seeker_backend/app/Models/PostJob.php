<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'experience',
        'requirements',
        'responsibilities',
        'education',
        'vacancies',
        'expiration',
        'salary_minimum',
        'salary_maximum',
        'time_type',
        'job_level',
        'job_type',
        'job_role',
        'city',
        'street',
        'tags',
        'location',
    ];
    public function users(){
        return $this->belongsToMany(User::class);
    }
    public function jobApplications() {
        return $this->hasMany(JobApplication::class);
    }
}
