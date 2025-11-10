<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingAllowancePayroll extends Model
{
    use HasFactory;
    protected $table = 'payroll_clothing_allowance';
    protected $fillable = [
        'batch_id',
        'payroll_date',
        'employment_type',
        'status',
    ];

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }

    public function items() {
        return $this->hasMany(ClothingAllowanceItemsPayroll::class, 'payroll_id', 'id');
    }
}
