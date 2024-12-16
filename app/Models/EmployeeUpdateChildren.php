<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateChildren extends Model
{
    use HasFactory;

    protected $table = 'employee_update_children';

    protected $fillable = [
        'updating_id',
        'employee_no',
        'firstname',
        'middlename',
        'lastname',
        'birthdate',
    ];

    public $timestamps = false;
}
