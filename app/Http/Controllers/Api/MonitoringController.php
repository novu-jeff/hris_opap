<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function post(Request $request) {
        $data = $request->all();

        return $data;
    }
}
