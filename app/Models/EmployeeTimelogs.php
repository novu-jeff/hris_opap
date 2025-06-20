<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimelogs extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; 
    protected $table = 'attendances';

    protected $fillable = [
        'sn',
        'table',
        'stamp',
        'employee_id',
        'timestamp',
        'status1',
        'isWeb',
        'captured_image',
        'captured_location',
        'accomplishment',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInformation::class, 'employee_id', 'bsd_no');
    }
}
