<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    // Define which columns are mass assignable
    protected $fillable = [
        'name',
        'description',
        'monday',
        'monday_remarks',
        'tuesday',
        'tuesday_remarks',
        'wednesday',
        'wednesday_remarks',
        'thursday',
        'thursday_remarks',
        'friday',
        'friday_remarks',
        'saturday',
        'saturday_remarks',
        'sunday',
        'sunday_remarks',
    ];

    public function employees() {
        return $this->hasMany(EmployeeInformation::class, 'schedule_id', 'id');
    }
}
