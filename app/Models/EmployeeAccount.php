<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class EmployeeAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    
    protected $table = 'employee_account';
    protected $fillable = [
        'employee_no',
        'applicant_id',
        'email_id',
        'email',
        'password',
        'isLoggedIn',
        'token'
    ];
    public $timestamps = false;


    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function timekeeping()
    {
        return $this->hasMany(EmployeeClockInOut::class, 'employee_no', 'employee_no');
    }

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

}
