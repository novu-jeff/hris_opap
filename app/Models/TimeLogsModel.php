<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLogsModel extends Model
{
    use HasFactory;

    protected $table = 'timelogs';
    protected $fillable = [
        'employee_id',
        'timestamp',
        'status',
        'isWeb',
        'captured_image',
        'captured_location',
        'accomplishment',
    ];
}
