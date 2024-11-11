<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAnnouncements;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index() {
        $records = EmployeeAnnouncements::all();
        return [
            'status' => 'success',
            'total' => $records->count(),
            'data' => $records
        ];
    }
}
