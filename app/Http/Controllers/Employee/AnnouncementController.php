<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index() {
        return view('employee.announcements', [
            'action' => 'index',
            'title' => 'ESS | Announcements',
            'header' => 'Announcements ',
            'sub' => 'Latest Happennings'
        ]);
    }

    public function view(int $id) {
        return view('employee.announcements', [
            'id' => $id,
            'action' => 'view',
            'title' => 'ESS | Announcements',
            'header' => 'Announcements ',
            'sub' => 'Latest Happennings'
        ]);
    }
}
