<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewItems extends Model
{
    use HasFactory;

    protected $table = 'job_interview_items';
    protected $fillable = [
        'interview_id',
        'name'
    ];

    public $timestamps = false;

}
