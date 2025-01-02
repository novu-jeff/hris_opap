<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:read users')->only('index');
        $this->middleware('permission:write users')->only(['create', 'edit']);
    }

    public function index(string $type)
    {
        return view('admin.user.index', compact('type'));
    }

    public function create()
    {
        return view('admin.user.new');
    }

}
