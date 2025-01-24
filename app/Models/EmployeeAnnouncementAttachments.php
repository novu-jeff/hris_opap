<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAnnouncementAttachments extends Model
{
    use HasFactory;

    protected $table = 'employee_announcements_attachments';
    protected $fillable = [
        'announcement_id',
        'name',
        'file'
    ];

}
