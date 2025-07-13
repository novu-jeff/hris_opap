<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateOtherWorks extends Model
{
    use HasFactory;

    protected $table = 'employee_update_other_works';

    protected $fillable = [
        'employee_no',
        'organization',
        'date_from',
        'date_to',
        'consumed_hours',
        'position',
        'documents'
    ];
}
