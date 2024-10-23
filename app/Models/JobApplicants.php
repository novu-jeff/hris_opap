<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicants extends Model
{
    use HasFactory;

    protected $table = 'job_applicants';
    protected $fillable = [
        'user_id',
        'applicant_no',
        'job_id',
        'status',
        'isInterviewResponded'
    ];

    public function applicant() {
        return $this->belongsTo(ApplicantUsers::class, 'user_id');
    }

    public function job() {
        return $this->belongsTo(JobPosts::class, 'job_id');
    }

    public function interview() {
        return $this->hasMany(JobApplicantsInterview::class, 'job_applicants_id');
    }

    public function offer() {
        return $this->hasOne(JobApplicantsOffer::class, 'job_applicants_id');
    }

    public function requirements() {
        return $this->hasMany(JobApplicantsRequirements::class, 'job_applicants_id');
    }

}