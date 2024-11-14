<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewItemsResponses extends Model
{
    use HasFactory;

    protected $table = 'job_interview_items_responses';
    protected $fillable = [
        'user_id',
        'interview_item_id',
        'answer'
    ];

    public $timestamps = false;

}
