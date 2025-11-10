<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingAllowanceItemsPayroll extends Model
{
    use HasFactory;

    protected $table = 'payroll_clothing_allowance_items';
    protected $fillable = [
        'payroll_id',
        'employee_no',
        'name',
        'position',
        'date_hired',
        'allowance',
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

}
