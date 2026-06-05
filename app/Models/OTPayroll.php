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
        'status',
        'selected_employees'
    ];

    public function employment_types() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type');
    }

    public function employment_type()
    {
        return $this->belongsTo(
            EmployementTypes::class,
            'employment_type'
        );
    }
    
     public function items() {
        return $this->hasMany(OTItemsPayroll::class, 'payroll_id', 'id');
    }

}
