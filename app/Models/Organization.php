<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    // Define relationship to get children organizations
    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }
}