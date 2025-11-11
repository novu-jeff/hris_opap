<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeaveCreditsImport;
use Illuminate\Support\Facades\Log;
use Exception;

class LeaveImportController extends Controller
{
    /**
     * Show the upload form.
     */
    public function index()
    {
        return view('admin.settings.leave-import.index');
    }

    /**
     * Handle the Excel import request.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'import_type' => 'required', // 'vl_sl' or 'credits'
        ]);

        try {
            $file = $request->file('file');
            $importType = $request->input('import_type');

            $isVlSl = $importType === 'vl_sl';
            $employee_no = $request->input('employee_no');
            $leave_type_id = $request->input('leave_type_id');

            Excel::import(
                new LeaveCreditsImport($employee_no, $isVlSl, $leave_type_id),
                $file
            );

            return back()->with('success', '✅ Excel file imported successfully!');

        } catch (Exception $e) {
            Log::error('Leave import failed: ' . $e->getMessage());

            // ✅ Custom message for duplicate year
            if (str_contains($e->getMessage(), 'already exists')) {
                return back()->with('error', '⚠️ ' . $e->getMessage());
            }

            // ✅ General import failure
            return back()->with('error', '❌ Import failed. Please check your file and try again.');
        }
    }
}
