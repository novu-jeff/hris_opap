<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicantsRequirements extends Model
{
    use HasFactory;

    protected $table = 'job_applicants_requirements';
    protected $fillable = [
        'job_applicants_id',
        'requirement_id',
    ];

}
