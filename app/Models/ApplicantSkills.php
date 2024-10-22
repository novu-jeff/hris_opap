<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantSkills extends Model
{
    use HasFactory;

    protected $table = 'job_applicant_skills';
    protected $fillable = [
        'user_id',
        'skill_id'
    ];

    public function skills() {
        return $this->hasOne(SkillList::class, 'id', 'skill_id');
    }

}