<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCivilService extends Model
{
    use HasFactory;

    protected $table = 'employee_civil_service';
    protected $fillable = [
        'employee_no',
        'certification',
        'rating',
        'date_exam',
        'place_exam',
        'license_no',
        'date_validity'
    ];
}
