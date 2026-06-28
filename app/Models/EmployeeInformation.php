<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInformation extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'employee_information';

    protected $fillable = [
        'employee_no',
        'bsd_no',
        'shift_id',
        'schedule_id',
        'section_id',
        'position_id',
        'job_completion',
        'date_hired',
        'company_name',
        'date_resignation',
        'employment_type_id',
        'status',
        'salary_method',
        'salary',
        'w_tax',
        'tax_type',
        'step_id',
        'salary_type',
        'bank_account_no',
        'payroll_account_number',
        'isTransferingEmp'
    ];
    

   /* public function section() {
        return $this->hasOne(Sections::class, 'id', 'section_id');
    }*/

     // ✅ Correct relationship: belongsTo, because employee has section_id
    public function section() {
        return $this->belongsTo(Sections::class, 'section_id', 'id')
                    ->with('branch', 'department');
    }   

    public function branch() {
        return $this->hasOne(Branches::class, 'id', 'branch_id');
    }

    public function department() {
        return $this->hasOne(Departments::class, 'id', 'department_id');
    }

    public function account() {
        return $this->hasOne(EmployeeAccount::class, 'employee_no', 'employee_no');
    }

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function education() {
        return $this->hasMany(EmployeeEducation::class, 'employee_no', 'employee_no');
    }

    public function parents() {
        return $this->hasOne(EmployeeParents::class, 'employee_no', 'employee_no');
    }

    public function children() {
        return $this->hasMany(EmployeeChildren::class, 'employee_no', 'employee_no');
    }

    public function employment_history() {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'employee_no', 'employee_no');
    }

    public function civil_service() {
        return $this->hasMany(EmployeeCivilService::class, 'employee_no', 'employee_no');
    }

    public function trainings() {
        return $this->hasMany(EmployeeTrainings::class, 'employee_no', 'employee_no');
    }

    public function others() {
        return $this->hasMany(EmployeeOtherWorks::class, 'employee_no', 'employee_no');
    }

    public function skills() {
        return $this->hasMany(EmployeeSkillsHobbies::class, 'employee_no', 'employee_no');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'employee_no');
    }

    /*public function positions() {
        return $this->hasOne(Positions::class, 'id', 'position_id');
    }*/
    
    public function positions() {
        return $this->belongsTo(Positions::class, 'position_id', 'id');
    }

    public function leave_credits() {
        return $this->hasOne(LeaveCredits::class, 'employee_no', 'employee_no');
    }

    public function earnings() {
        return $this->hasMany(EmployeeEarnings::class, 'employee_no', 'employee_no');
    }

    public function deductions() {
        return $this->hasMany(EmployeeDeductions::class, 'employee_no', 'employee_no');
    }

    public function employment_type() {
        //return $this->hasOne(EmployementTypes::class, 'id', 'employment_type_id');
        return $this->belongsTo(EmployementTypes::class, 'employment_type_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftSchedule::class, 'shift_id');
    }

    public function schedule()
    {
        return $this->belongsTo(ShiftSchedule::class, 'schedule_id');
    }

    public function offsetRequests()
    {
        return $this->hasMany(EmployeeOffsetRequest::class, 'employee_information_id');
    }

}
