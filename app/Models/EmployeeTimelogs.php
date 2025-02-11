<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimelogs extends Model
{
    use HasFactory;

    protected $table = 'employee_timelogs';

    protected $fillable = [
        'origin',
        'biometricdtrid',
        'bsd_no',
        'isindtr',
        'logdatetime',
        'nfcdeviceid',
        'type',
        'ismanual',
        'captured_image',
        'captured_location',
        'accomplishment'
    ];

    public function employee() {
        return $this->hasOne(EmployeeInformation::class, 'bsd_no', 'bsd_no');
    }

}
