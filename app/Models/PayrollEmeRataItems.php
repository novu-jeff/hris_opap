<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEmeRataItems extends Model
{
    use HasFactory;

    protected $table = 'payroll_eme_rata_items';

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = $this->resolveFillable();
    }

    protected function resolveFillable(): array
    {
        $product = config('app.product');

        if ($product === 'government') {
            return [
                'payroll_id',
                'employee_no',
                'employment_type_id',
                'name',
                'position',
                'basic_salary',
                'ra',
                'ta',
                'net_amount',
                
                
            ];
        }

        return [
            'payroll_id',
            'employee_no',
            'name',
            'position',
            'basic_salary',
            'overtime_pay',
            'aut',
            'holiday_pay',
            'allowances',
            'gross_amount_earned',
            'sss',
            'pagibig',
            'philhealth',
            'w_tax',
            'other_loans',
            'total_deductions',
            'net_amount',
            'bank_account',
            'bank_name',
            'salary',
        ];
    }

    public function information() {
        return $this->belongsTo(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function payroll() {
        return $this->belongsTo(SalaryPayroll::class, 'payroll_id', 'id');
    }

    public function deductions()
    {
        return $this->hasMany(PayrollSallaryDeduction::class,'payroll_item_id')
                        ->with('loan.loanType'); // 👈 important!
    }

}
