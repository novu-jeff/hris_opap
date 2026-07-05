<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\OffsetPdfService;

class OffsetPdfController extends Controller
{
    public function download($id, OffsetPdfService $service)
    {
        return $service->download($id);
    }
}