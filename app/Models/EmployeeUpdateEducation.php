<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_update_education';

    protected $fillable = [
        'employee_no',
        'level',
        'school_name',
        'course',
        'from_year',
        'to_year',
    ];

    public $timestamps = false;
}
