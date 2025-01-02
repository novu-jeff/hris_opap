<?php

namespace App\Http\Controllers\Admin\Job;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{



    public function index()
    {
        if(Gate::denies('read jobs')) {
            abort(403);
        }

        return view('admin.job.posts.index');
    }

    public function create()
    {

        if(Gate::denies('write jobs')) {
            abort(403);
        }

        return view('admin.job.posts.create');
    }

    public function edit(string $id)
    {
        return view('admin.job.posts.edit', compact('id'));
    }
}
