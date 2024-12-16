<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeClockInOut extends Model
{
    use HasFactory;

    protected $table = 'employee_clock_in_out';
    protected $fillable = [
        'employee_no',
        'origin',
        'biometricdtrid',
        'bsd_no',
        'isindtr',
        'nfcdeviceid',
        'type',
        'ismanual',
        'clock_in_am',
        'clock_out_am',
        'mins_consumed_am',
        'clock_in_pm',
        'clock_out_pm',
        'mins_consumed_pm',
        'captured_image_clockin',
        'captured_image_clockout',
        'captured_location_clockin',
        'captured_location_clockout',
        'isLate',
        'isHalfDay',
        'isUnderTime',
        'total_mins_consumed',
        'mins_ot',
        'overall_mins',
        'accomplishment'
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'bsd_no', 'bsd_no');
    }
}
