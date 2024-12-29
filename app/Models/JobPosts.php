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
        'employment_type_id',
        'min_salary',
        'max_salary',
        'description',
        'slots'
    ];

    public function applicants() {
        return $this->hasMany(JobApplicants::class, 'job_id');
    }

    public function employment_type() {
        return $this->hasOne(EmployementTypes::class, 'id', 'employment_type_id');
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['position', 'company_name', 'location', 'setup', 'type'])
            ->saveSlugsTo('slug'); 
    }

}
