<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read employee-announcements')->only('index');
    }

    public function index() {
        return view('employee.announcements', [
            'action' => 'index',
            'title' => 'ESS | Announcements',
            'header' => 'Announcements ',
            'sub' => 'View Latest News and Happennings'
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
