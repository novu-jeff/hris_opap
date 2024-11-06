<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EmployeeAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'employee_account';
    protected $fillable = [
        'employee_id',
        'applicant_id',
        'email',
        'password'
    ];
    public $timestamps = false;

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_id', 'employee_id');
    }

}
