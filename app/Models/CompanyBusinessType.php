<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBusinessType extends Model
{
    use HasFactory;
    
    protected $table = 'company_business_types';
    protected $fillable = [
        'name'
    ];

}
