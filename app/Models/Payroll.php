<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';
    protected $fillable = [
        'type',
        'payroll_date',
        'cut_off_period',
        'employment_type',
        'saved_payroll',
        'status'
    ];

    public function deductions() {
        return $this->hasOne(PayrollDeductions::class, 'payroll_id', 'id');
    }

    public function earnings() {
        return $this->hasOne(PayrollEarnings::class, 'payroll_id', 'id');
    }

}
