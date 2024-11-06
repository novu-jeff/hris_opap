<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ApplicantUsers extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'applicant_users';
    protected $fillable = [
        'image',
        'firstname',
        'middlename',
        'lastname',
        'phone_no',
        'tel_no',
        'sex',
        'birthday',
        'civil_status',
        'address',
        'province',
        'city',
        'resume',
        'level',
        'school_name',
        'course',
        'started',
        'finished',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function skills() {
        return $this->hasMany(ApplicantSkills::class, 'user_id');
    }

    public function applied() {
        return $this->hasMany(JobApplicants::class, 'user_id');
    }

    public function saved_jobs() {
        return $this->hasMany(SavedJobs::class, 'user_id');
    }

}