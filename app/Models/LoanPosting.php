<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'payroll_id', 'payroll_item_id', 'amount', 'posted_at'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function payroll()
    {
        return $this->belongsTo(SalaryPayroll::class);
    }

    public function payrollItem()
    {
        return $this->belongsTo(SalaryItemsPayroll::class);
    }
}
