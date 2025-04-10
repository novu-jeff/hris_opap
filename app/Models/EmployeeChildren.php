<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeChildren extends Model
{
    use HasFactory;

    protected $table = 'employee_children';
    protected $fillable = [
        'employee_no',
        'firstname',
        'middlename',
        'lastname',
        'birthdate',
        'documents'
    ];
    public $timestamps = false;
}
