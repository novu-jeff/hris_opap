<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.ess.announcements.index', [
            'action' => 'view',
            'title' => 'All Announcements',
            'header' => 'Manage Announcements',
            'sub' => 'Latest announcements and updates.'
        ]);
    }

    public function create()
    {
        return view('admin.ess.announcements.index', [
            'action' => 'create',
            'title' => 'Add an Announcement',
            'header' => 'Create an announcement',
            'sub' => 'Create announcements or inform employees for latest happennings.'
        ]);

    }

    public function edit(int $id)
    {
        return view('admin.ess.announcements.index', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Announcements',
            'header' => 'Edit Announcement',
            'sub' => 'Modify or update posted announcements.'
        ]);

    }
}
