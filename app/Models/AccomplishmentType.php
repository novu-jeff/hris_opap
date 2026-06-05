<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccomplishmentType extends Model
{
    protected $fillable = [
        'accomplishment_name',
        'is_active',
    ];
}