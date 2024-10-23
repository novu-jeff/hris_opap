<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicantsInterview extends Model
{
    use HasFactory;

    protected $table = 'job_applicants_interview';
    protected $fillable = [
        'job_applicants_id',
        'job_interview_id',
    ];

    public function details() {
        return $this->belongsTo(Interview::class, 'job_interview_id', 'id');
    }

    public function items() {
        return $this->hasMany(InterviewItems::class, 'interview_id', 'job_interview_id');
    }

    public function applicant() {
        return $this->belongsTo(JobApplicants::class, 'job_applicants_id', 'id');
    }

    public function options() {
        return $this->hasMany(InterviewItemsOptions::class, 'interview_item_id');
    }

    public function answers() {
        return $this->hasMany( InterviewItemsResponses::class, 'interview_item_id');
    }

}