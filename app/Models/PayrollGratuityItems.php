<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollGratuityItems extends Model
{
    use HasFactory;
    protected $table = 'payroll_gratuity_items';
    protected $fillable = [
        'payroll_id',
        'employee_no',
        'name',
        'position',
        'employment_type_id',
        'basic_salary',
        'date_hired',
        'gratuity_pay',	
        'tax',
        'net_amount',
    ];
public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

}
