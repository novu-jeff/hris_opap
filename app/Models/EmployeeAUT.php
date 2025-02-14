<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAUT extends Model
{
    use HasFactory;

    protected $table = 'employee_aut';
    protected $fillable = [
        'date',
        'bsd_no',
        'absences',
        'undertime',
        'lates'
    ];
}
