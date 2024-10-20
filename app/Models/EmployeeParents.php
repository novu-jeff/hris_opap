<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeParents extends Model
{
    use HasFactory;

    protected $table = 'employee_parents';
    protected $fillable = [
        'employee_id',
        'father_surname',
        'father_firstname',
        'father_middlename',
        'suffix',
        'father_occupation',
        'father_business_name',
        'father_business_address',
        'father_tel_no',
        'mother_surname',
        'mother_firstname',
        'mother_middlename',
        'mother_occupation',
        'mother_business_name',
        'mother_business_address',
        'mother_tel_no',
    ];

    public $timestamps = false;

}
