<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateTrainings extends Model
{
    use HasFactory;

    protected $table = 'employee_update_trainings';

    protected $fillable = [
        'updating_id',
        'employee_no',
        'type',
        'name',
        'date_from',
        'date_to',
        'consumed_hours',
        'sponsored_by',
    ];

    public $timestamps = false;
}
