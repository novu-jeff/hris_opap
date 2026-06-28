<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOffsetCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_information_id',
        'overtime_id',
        'earned_hours',
        'used_hours',
        'remaining_hours',
        'earned_date',
    ];

    protected $casts = [
        'earned_date' => 'date',
        'earned_hours' => 'decimal:2',
        'used_hours' => 'decimal:2',
        'remaining_hours' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInformation::class, 'employee_information_id');
    }

    public function overtime()
    {
        return $this->belongsTo(EmployeeOvertimes::class, 'overtime_id');
    }
}