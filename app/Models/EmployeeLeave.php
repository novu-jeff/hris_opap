<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $table = 'employee_leave';
    protected $fillable = [
        'employee_id',
        'status',
        'type',
        'reason',
        'from',
        'to',
        'measurement',
        'consumed_hours'
    ];

}
