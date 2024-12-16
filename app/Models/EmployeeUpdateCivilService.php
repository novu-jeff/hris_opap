<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateCivilService extends Model
{
    use HasFactory;

    protected $table = 'employee_update_civil_service';

    protected $fillable = [
        'updating_id',
        'employee_no',
        'certification',
        'rating',
        'date_exam',
        'place_exam',
        'license_no',
        'date_validity',
    ];

    public $timestamps = false;
}
