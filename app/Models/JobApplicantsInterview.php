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

    public function interview() {
        return $this->belongsTo(Interview::class, 'job_interview_id', 'id');
    }
}