<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOtherWorks extends Model
{
    use HasFactory;

    protected $table = 'employee_other_works';
    protected $fillable = [
        'employee_no',
        'organization',
        'address',
        'date_from',
        'date_to',
        'consumed_hours',
        'position',
        'documents'
    ];

}
