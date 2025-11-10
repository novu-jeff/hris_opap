<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAnnouncementsSeen extends Model
{
    use HasFactory;

    protected $table = 'employee_announcements_seen';
    protected $fillable = [
        'announcement_id',
        'employee_no',
    ];

    public function personal() {
        return $this->belongsTo(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

}
