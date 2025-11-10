<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentTypeSetting extends Model
{
    use HasFactory;

    protected $table  = 'employment_type_settings';

    protected $guarded = ['id', 'created_at','updated_at'];
}
