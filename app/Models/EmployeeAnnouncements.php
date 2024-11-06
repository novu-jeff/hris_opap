<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAnnouncements extends Model
{
    use HasFactory;

    protected $table = 'employee_announcements';
    protected $fillable = [
        'banner',
        'title',
        'content'
    ];

}
