<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAccount extends Model
{
    use HasFactory;

    protected $table = 'employee_account';
    protected $fillable = [
        'employee_id',
        'applicant_id',
        'email',
        'password'
    ];
    public $timestamps = false;

}
