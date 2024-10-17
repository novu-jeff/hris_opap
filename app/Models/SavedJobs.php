<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedJobs extends Model
{
    use HasFactory;
    protected $table = 'saved_jobs';
    protected $fillable = [
        'user_id',
        'job_id'
    ];

    public function job() {
        return $this->belongsTo(JobPosts::class, 'job_id');
    }
}