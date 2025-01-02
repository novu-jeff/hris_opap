<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $type)
    {
        return view('admin.user.index', compact('type'));
    }

    public function create()
    {
        return view('admin.user.new');
    }

}
