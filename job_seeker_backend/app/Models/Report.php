<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'last_updated',
        'views',
        'downloads',
        'icon',
        'description'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'views' => 'integer',
        'downloads' => 'integer'
    ];
}
