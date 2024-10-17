<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosts extends Model
{
    use HasFactory;
    use HasSlug;
    use HasFactory;

    protected $fillable = [
        'slug',
        'position',
        'company_name',
        'location',
        'setup',
        'type',
        'min_salary',
        'max_salary',
        'description',
        'slots'
    ];

    public function applicants() {
        return $this->hasMany(JobApplicants::class, 'job_id');
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['position', 'company_name', 'location', 'setup', 'type'])
            ->saveSlugsTo('slug'); 
    }

}
