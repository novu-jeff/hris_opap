<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAtro extends Model
{
    use HasFactory;

    protected $table = 'employee_atro';
    protected $fillable = [
        'employee_no',
        'date',
        'start_time',
        'end_time',
        'justification'
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

}
