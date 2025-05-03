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
            'title' => 'All Frequently Asked Questions (FAQs)',
            'header' => 'Manage Frequently Asked Questions (FAQs)',
            'sub' => 'Latest FAQs and updates.'
        ]);
    }

    public function create()
    {
        return view('admin.ess.faqs.index', [
            'action' => 'create',
            'title' => 'Create Frequently Asked Questions (FAQs)',
            'header' => 'Create a Frequently Asked Questions (FAQs)',
            'sub' => 'Create FAQs or inform employees for latest happennings.'
        ]);

    }

    public function edit(int $id)
    {
        return view('admin.ess.faqs.index', [
            'id' => $id,
            'action' => 'edit',
            'title' => 'Edit Frequently Asked Questions (FAQs)',
            'header' => 'Edit Frequently Asked Questions (FAQs)',
            'sub' => 'Modify or update posted FAQs.'
        ]);

    }
}
