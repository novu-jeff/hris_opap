<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSkillsHobbies extends Model
{
    use HasFactory;

    protected $table = 'employee_skills_hobbies';
    protected $fillable = [
        'employee_no',
        'name',
        'recognition',
        'organization',
        'documents'
    ];

}
