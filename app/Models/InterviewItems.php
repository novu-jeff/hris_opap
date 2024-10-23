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
        'question',
        'response_type'
    ];

    public $timestamps = false;

    public function items() {
        return $this->hasMany(InterviewItems::class, 'interview_id');
    }

    public function answers() {
        return $this->hasMany( InterviewItemsResponses::class, 'interview_item_id');
    }

    public function options() {
        return $this->hasMany(InterviewItemsOptions::class, 'interview_item_id');
    }

}
