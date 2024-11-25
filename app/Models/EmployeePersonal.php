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
        'present_address',
        'present_province',
        'present_city',
        'permanent_address',
        'permanent_province',
        'permanent_city',
        'mobile_number',
        'tel_no',
        'company_email',
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

}
