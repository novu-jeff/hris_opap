<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOffsetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_information_id',
        'office_order_no',
        'filing_date',
        'date_from',
        'date_to',
        'hours_requested',
        'reason',
        'status',
        'recommended_by',
        'certified_by',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'date_from' => 'date',
        'date_to' => 'date',
        'hours_requested' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInformation::class, 'employee_information_id');
    }

   /* public function recommender()
    {
        return $this->belongsTo(Admin::class, 'recommended_by');
    }

    public function certifier()
    {
        return $this->belongsTo(Admin::class, 'certified_by');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }*/
}