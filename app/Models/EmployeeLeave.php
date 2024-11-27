<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $table = 'employee_leave';
    protected $fillable = [
        'employee_no',
        'status',
        'type',
        'reason',
        'from',
        'to',
        'measurement',
        'consumed_hours'
    ];

    public function employment() {
        return $this->hasOne(EmployeeInformation::class, 'id', 'employee_id');
    }

    public function employee() {
        return $this->hasOne(EmployeePersonal::class, 'employee_id', 'employee_id');
    }

}
