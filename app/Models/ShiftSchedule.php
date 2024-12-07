<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    use HasFactory;

    protected $table = 'shift_schedule';
    protected $fillable = [
        'mobile_earliest_clockin',
        'mobile_latest_clockin',
        'web_earliest_clockin',
        'web_latest_clockin',
        'break_from',
        'break_to',
        'min_ot_mins',
        'max_ot_time',
        'is_late_strict',
        'is_strict_undertime',
    ];

}
