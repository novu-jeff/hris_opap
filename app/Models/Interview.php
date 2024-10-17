<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $table = 'job_interview';
    protected $fillable = [
        'name',
        'description',
        'level'
    ];

    public function items() {
        return $this->hasMany(InterviewItems::class, 'interview_id');
    }

}
