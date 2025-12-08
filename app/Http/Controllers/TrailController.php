<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrailController extends Controller
{
    public function index() {
        return view('admin.settings.audit-trails', [
            'action' => 'index',
            'title' => 'Audit Trails',
            'header' => 'Audit Trails',
        ]);
    }
}
