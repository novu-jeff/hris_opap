<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOffsetAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_offset_request_id',
        'attachment',
    ];

    public function request()
    {
        return $this->belongsTo(
            EmployeeOffsetRequest::class,
            'employee_offset_request_id'
        );
    }
}