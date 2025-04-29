<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAtroRelative extends Model
{
    use HasFactory;

    protected $table = 'employee_atro_relative';
    protected $fillable = [
        'employee_atro_id',
        'employee_no',
    ];

    public function atro() {
        return $this->belongsTo(EmployeeAtro::class, 'employee_atro_id', 'id');
    }

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }
}
