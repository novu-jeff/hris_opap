<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEarnings extends Model
{
    use HasFactory;

    protected $table = 'employee_earnings';
    protected $fillable = [
        'employee_no',
        'earning_id',
        'amount_type',
        'amount',
        'first_term',
        'second_term'
    ];

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function earning() {
        return $this->belongsTo(OtherEarnings::class, 'earning_id', 'id');
    }

}
