<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateParents extends Model
{
    use HasFactory;

    protected $table = 'employee_update_parents';

    protected $fillable = [
        'employee_no',
        'spouse_surname',
        'spouse_firstname',
        'spouse_middlename',
        'spouse_suffix',
        'spouse_occupation',
        'spouse_business_name_employer',
        'spouse_business_address',
        'spouse_contact_no',
        'father_surname',
        'father_firstname',
        'father_middlename',
        'father_suffix',
        'mother_surname',
        'mother_firstname',
        'mother_middlename',
    ];

    public $timestamps = false;
}
