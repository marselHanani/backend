<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_description',
        'company_logo',
        'company_cover',
        'company_social',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
