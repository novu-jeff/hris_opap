<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdatePersonal extends Model
{
    use HasFactory;

    protected $table = 'employee_update_personal';

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

    public function education() {
        return $this->hasMany(EmployeeUpdateEducation::class, 'employee_no', 'employee_no');
    }

    public function parents() {
        return $this->hasOne(EmployeeUpdateParents::class, 'employee_no', 'employee_no');
    }

    public function children() {
        return $this->hasMany(EmployeeUpdateChildren::class, 'employee_no', 'employee_no');
    }

    public function employment_history() {
        return $this->hasMany(EmployeeUpdateEmploymentHistory::class, 'employee_no', 'employee_no');
    }

    public function civil_service() {
        return $this->hasMany(EmployeeUpdateCivilService::class, 'employee_no', 'employee_no');
    }

    public function trainings() {
        return $this->hasMany(EmployeeUpdateTrainings::class, 'employee_no', 'employee_no');
    }

    public function others() {
        return $this->hasMany(EmployeeUpdateOtherWorks::class, 'employee_no', 'employee_no');
    }

    public function skills() {
        return $this->hasMany(EmployeeUpdateSkillsHobbies::class, 'employee_no', 'employee_no');
    }
    


}
