<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewItemsOptions extends Model
{
    use HasFactory;

    protected $table = 'job_interview_items_option';
    protected $fillable = [
        'interview_item_id',
        'name'
    ];

    public $timestamps = false;

}
