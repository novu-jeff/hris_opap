<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeClockInOut extends Model
{
    use HasFactory;

    protected $table = 'employee_clock_in_out';
    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'captured_image_clockin',
        'captured_image_clockout',
        'captured_location_clockin',
        'captured_location_clockout'
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'id', 'employee_id');
    }
}
