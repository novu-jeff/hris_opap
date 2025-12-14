<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no', 'loan_type_id', 'principal_amount', 'term_months',
        'monthly_amortization', 'balance', 'start_payroll_id', 'released_at',
        'status', 'disapproval_note', 'created_by', 'approved_by'
    ];

    // Link to EmployeePersonal to get name
    public function personal()
    {
        return $this->belongsTo(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    // Optional: link to EmployeeInformation
    public function information()
    {
        return $this->belongsTo(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    // Loan type relationship
    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    // Loan postings
    public function postings()
    {
        return $this->hasMany(LoanPosting::class);
    }
}
