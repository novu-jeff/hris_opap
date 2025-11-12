<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class TimeKeepingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read timelogs')->only('index');
        $this->middleware('permission:write timelogs')->only(['create', 'edit']);
        $this->middleware('permission:read correction-timelogs')->only(['correction', 'correction_apply']);
    }

    public function upload()
    {
        return view('admin.timekeeping.upload');
    }

    public function correction(?string $month = null, ?int $day = null, ?int $year = null)
    {
        if (is_null($month) || is_null($day) || is_null($year)) {
            [$year, $month, $day] = $this->resolveDateFrom('created_at');
            return redirect()->route('timekeeping.correction', compact('month', 'day', 'year'));
        }

        $setup = request()->query('setup');
        return view('admin.timekeeping.correction', compact('month', 'day', 'year', 'setup'));
    }

    public function job(string $id)
    {
        return Bus::findBatch($id);
    }

    public function correction_apply(string $bsd_no, string $date)
    {
        return view('admin.timekeeping.correction-apply', compact('bsd_no', 'date'));
    }

    /**
     * Resolves the most recent date from a specified column or uses current date.
     *
     * @param string $column
     * @return array [$year, $month, $day]
     */
    private function resolveDateFrom(string $column): array
    {
        $latest = EmployeeTimelogs::orderByDesc($column)->first();
        $date = $latest ? Carbon::parse($latest->{$column}) : now();

        return [
            $date->year,
            str_pad($date->month, 2, '0', STR_PAD_LEFT),
            str_pad($date->day, 2, '0', STR_PAD_LEFT),
        ];
    }
}
