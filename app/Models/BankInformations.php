<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInformations extends Model
{
    use HasFactory;

    protected $table = 'bank_informations';
    protected $fillable = [
        'name',
        'account_number',
        'department_center_id',
        'isActive'
    ];

    public function department_center() {
        return $this->hasOne(DepartmentCenters::class, 'id', 'department_center_id');
    }

}
