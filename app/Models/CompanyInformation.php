<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInformation extends Model
{
    use HasFactory;

    protected $table = 'company_information';
    protected $fillable = [
        'name',
        'address',
        'contact',
        'type_id'
    ];

}
