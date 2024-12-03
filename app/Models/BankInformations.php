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
        'department_id',
        'isActive'
    ];

    public function departments() {
        return $this->hasOne(Departments::class, 'id', 'department_id');
    }

}
