<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\Monitor;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function post(Request $request) {
        $data = $request->all();

        try {
            Monitor::createOrUpdate([
                'application',
                'created_at'
            ], [
                
            ])
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
