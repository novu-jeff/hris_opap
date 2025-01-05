<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';
    protected $fillable = [
        'code',
        'name',
        'credits',
        'isCummulative'
    ];

    public function credits() {
        return $this->hasOne(LeaveCredits::class, 'leave_type_id', 'id');
    }

}
