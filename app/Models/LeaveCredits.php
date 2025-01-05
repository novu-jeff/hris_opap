<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCredits extends Model
{
    use HasFactory;

    protected $table = 'leave_credits';
    protected $fillable = [
        'leave_type_id',
        'employee_no',
        'credits'
    ];  

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

}
