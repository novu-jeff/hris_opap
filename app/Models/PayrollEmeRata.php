<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEmeRata extends Model
{
    use HasFactory;

    protected $table = 'payroll_eme_rata';
    protected $fillable = [
        'batch_id',
        'payroll_date',
        'cut_off_period',
        'employment_type',
        'hasDeductions',
        'selected_employees',
        'status'
    ];

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }

    public function items() {
        return $this->hasMany(PayrollEmeRataItems::class, 'payroll_id', 'id');
    }
}
