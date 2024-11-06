<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInformation extends Model
{
    use HasFactory;

    protected $table = 'employee_information';

    protected $fillable = [
        'biometrics_id',
        'department_id',
        'branch_id',
        'position_id',
        'date_hired',
        'date_resignation',
        'type',
        'status',
        'salary_method',
        'leave_credits',
        'monthly_rate',
        'daily_rate',
        'payroll_account_number',
    ];
    public $timestamps = false;

    public function branch() {
        return $this->hasOne(Branches::class, 'id', 'branch_id');
    }

    public function department() {
        return $this->hasOne(DepartmentCenters::class, 'id', 'department_id');
    }

    public function account() {
        return $this->hasOne(EmployeeAccount::class, 'employee_id');
    }

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_id');
    }

    public function education() {
        return $this->hasMany(EmployeeEducation::class, 'employee_id');
    }

    public function parents() {
        return $this->hasOne(EmployeeParents::class, 'employee_id');
    }

    public function children() {
        return $this->hasMany(EmployeeChildren::class, 'employee_id');
    }

    public function employment_history() {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'employee_id');
    }

    public function positions() {
        return $this->hasOne(Positions::class, 'id', 'position_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'from_id', 'id');
    }


}
