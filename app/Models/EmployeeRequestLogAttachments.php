<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRequestLogAttachments extends Model
{
    use HasFactory;

    protected $table = 'employee_request_logs_attachments';
    protected $fillable = [
        'employee_requests_id',
        'attachment',
    ];

    public $timestamps = false;

}
