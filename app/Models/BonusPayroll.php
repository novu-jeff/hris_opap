<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusPayroll extends Model
{
    use HasFactory;

    protected $table = 'payroll_bonuses';

    protected $fillable = [
        'batch_id',
        'employment_type',
        'bonus_type',
        'payroll_date',
        'status',
    ];

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }

    public function items()
    {
        return $this->hasMany(BonusItemsPayroll::class, 'payroll_id');
    }

}
