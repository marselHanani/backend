<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'title',
        'company',
        'description',
        'type',
        'salary',
        'location',
        'category',
        'postedDate',
        'deadline'
    ];

    protected $casts = [
        'postedDate' => 'datetime',
        'deadline' => 'datetime',
    ];

    protected $table = 'jobs';

   
    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
