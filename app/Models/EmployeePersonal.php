<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePersonal extends Model
{
    use HasFactory;

    protected $table = 'employee_personal';
    protected $fillable = [
        'employee_no',
        'profile',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'birthday',
        'age',
        'civil_status',
        'sex',
        'citizenship',
        'citizenship_type',
        'country',
        'birth_certificate',
        'marriage_certificate',
        'present_address',
        'present_province',
        'present_city',
        'permanent_address',
        'permanent_province',
        'permanent_city',
        'mobile_number',
        'tel_no',
        'email',
        'height',
        'weight',
        'blood_type',
        'gsis_no',
        'pagibig_no',
        'philhealth_no',
        'sss_no',
        'tin_no',
    ];

    public $timestamps = false;
    
    public function gsis_item() {
        return $this->hasOne(SocialSecurityBillingItems::class, 'bp_no', 'gsis_no');
    }

    public function account() {
        return $this->hasOne(EmployeeAccount::class, 'employee_no', 'employee_no');
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

}
