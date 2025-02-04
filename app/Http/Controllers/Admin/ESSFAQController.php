<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ESSFAQController extends Controller
{
    public function __construct() {
        $this->middleware('permission:read faqs')->only('index');
        $this->middleware('permission:write faqs')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.ess.faqs.index', [
            'action' => 'view',
            'title' => 'All FAQs',
            'header' => 'Manage FAQs',
            'sub' => 'Latest FAQs and updates.'
        ]);
    }

    public function create()
    {
        return view('admin.ess.faqs.index', [
            'action' => 'create',
            'title' => 'Add an Announcement',
            'header' => 'Create an announcement',
            'sub' => 'Create FAQs or inform employees for latest happennings.'
        ]);

    }

    public function edit(int $id)
    {
        return view('admin.ess.faqs.index', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit FAQs',
            'header' => 'Edit Announcement',
            'sub' => 'Modify or update posted FAQs.'
        ]);

    }
}
