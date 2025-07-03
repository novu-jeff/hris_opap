<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPayroll extends Model
{
    use HasFactory;

    protected $table = 'payroll_overtime';
    protected $fillable = [
        'batch_id',
        'period',
        'employment_type',
        'status'
    ];

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }
    
     public function items() {
        return $this->hasMany(OTItemsPayroll::class, 'payroll_id', 'id');
    }

}
