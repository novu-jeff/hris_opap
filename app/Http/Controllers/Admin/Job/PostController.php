<?php

namespace App\Http\Controllers\Admin\Job;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{

    public function __construct() {
        $this->middleware('permission:read jobs')->only('index');
        $this->middleware('permission:write jobs')->only(['create', 'edit']);
    }

    public function index()
    {
        return view('admin.job.posts.index');
    }

    public function create()
    {
        return view('admin.job.posts.create');
    }

    public function edit(string $id)
    {
        return view('admin.job.posts.edit', compact('id'));
    }
}
