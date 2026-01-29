<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeductions extends Model
{
    use HasFactory;

    protected $table = 'employee_deductions';
    protected $fillable = [
        'employee_no',
        'deduction_id',
        'amount',
        'valid_until'
    ];

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function deduction() {
        return $this->belongsTo(OtherDeductions::class,'deduction_id', 'id');
    }

}
