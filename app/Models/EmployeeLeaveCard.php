<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveCard extends Model
{
    use HasFactory;

    protected $table = 'employee_leave_cards';
    protected $fillable = [
        'employee_no',
        'period',
        'year',
        'vl_particulars',
        'vl_earned',
        'vl_aut_w_pay',
        'vl_bal',
        'vl_aut_wo_pay',
        'vl_remarks',
        'sl_earned',
        'sl_aut_w_pay',
        'sl_bal',
        'sl_aut_wo_pay',
        'sl_particulars',
        'sl_remarks',
    ];

}
