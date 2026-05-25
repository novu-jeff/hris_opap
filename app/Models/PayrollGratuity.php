<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollGratuity extends Model
{
    use HasFactory;

    protected $table = 'payroll_gratuity';

    protected $fillable = [
        'batch_id',
        'employment_type',
        'payroll_date',
        'remarks',
        'selected_employees',
        'status',
    ];

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }

    public function items()
    {
        return $this->hasMany(PayrollGratuityItems::class, 'payroll_id');
    }

}
