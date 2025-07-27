<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployementTypes extends Model
{
    use HasFactory;
    protected $table  = 'employment_types';
    protected $fillable = [
        'name',
        'code',
    ];

    public function employees() {
        return $this->hasMany(EmployeeInformation::class, 'employment_type_id', 'id');
    }

    public function setting()
    {
        return $this->hasOne(EmploymentTypeSetting::class,'employment_type_id','id');
    }

}
