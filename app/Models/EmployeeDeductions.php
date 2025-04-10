<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeductions extends Model
{
    use HasFactory;

    protected $table = 'employee_deduction';
    protected $fillable = [
        'employee_no',
        'deduction_id',
        'amount',
        'as_of'
    ];

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function deduction() {
        return $this->hasOne(OtherDeductions::class, 'id', 'deduction_id');
    }

}
