<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicantsOffer extends Model
{
    use HasFactory;

    protected $table = 'job_applicants_offer';
    protected $fillable = [
        'job_applicants_id',
        'subject',
        'body',
        'attachment',
        'starting_date',
        'salary'
    ];

    public $timestamps = false;

}
