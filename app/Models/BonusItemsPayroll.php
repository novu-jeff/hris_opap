<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusItemsPayroll extends Model
{
    use HasFactory;
    protected $table = 'payroll_bonuses_items';
    protected $fillable = [
        'payroll_id',
        'employee_no',
        'name',
        'position',
        'employment_type_id',
        'basic_salary',
        'bonus',
        'cash_gift',
        'january_amount',
        'february_amount',
        'march_amount',
        'april_amount',
        'may_amount',
        'june_amount',	
        'july_amount',	
        'august_amount',	
        'september_amount',	
        'october_amount',
        'november_amount',	
        'december_amount',	
        'total_amount',
        'date_hired',
        'coverage_from',	
        'coverage_to',
        'semester',
        'tax',
        'net_amount',
    ];
public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

}
