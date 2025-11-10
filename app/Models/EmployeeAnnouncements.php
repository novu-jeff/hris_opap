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
        'content',
        'isDeleted'
    ];

    public function attachments() {
        return $this->hasMany(EmployeeAnnouncementAttachments::class, 'announcement_id');
    }

    public function seen() {
        return $this->hasMany(EmployeeAnnouncementsSeen::class, 'announcement_id');
    }

}
