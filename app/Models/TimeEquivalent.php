<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEquivalent extends Model
{
    use HasFactory;

    protected $table = 'time_equivalents';
    protected $fillable = [
        'type',
        'time',
        'equiv'
    ];

    public $timestamps = false;

}
