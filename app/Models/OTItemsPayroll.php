<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTItemsPayroll extends Model
{
    use HasFactory;

    protected $table = 'payroll_overtime_items';

    protected $fillable = [
        'payroll_id',
        'employee_no',
        'name',
        'position',
        'basic_salary',
        'duration',
        'amount',
        'tax',
        'net_amount'
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

}
