<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeave;
use App\Models\GSISBilling;
use App\Models\JobApplicants;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $product;
    public $stats;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords()
    {

        $recruitmentCounts = JobApplicants::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status');

        $employeeCounts = EmployeeInformation::groupBy('job_category_id')
            ->select('job_category_id', DB::raw('count(*) as total'))
            ->pluck('total', 'job_category_id');
    
        $leaveCounts = EmployeeLeave::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status');

        $earnings = OtherEarnings::all()->toArray() ?? [];
        $deductions = OtherDeductions::all()->toArray() ?? [];

        $gsis_billing = GSISBilling::with('items')
            ->orderBy('billing_month', 'desc')
            ->first();

        $gsis_billing = $gsis_billing ? $gsis_billing->toArray() : [];

        $clockinout = EmployeeClockInOut::whereDate('created_at', Carbon::today()) 
            ->get();


        $this->stats = [
            'recruitment' => [
                'pending' => $recruitmentCounts['pending'] ?? 0,
                'interview' => $recruitmentCounts['interview'] ?? 0,
                'placement' => $recruitmentCounts['placement'] ?? 0,
                'onboarding' => $recruitmentCounts['onboarding'] ?? 0,
                'hired' => $recruitmentCounts['hired'] ?? 0,
                'rejected' => $recruitmentCounts['rejected'] ?? 0
            ],
            'employee' => [
                'rc' => $employeeCounts[1] ?? 0,
                'cos' => $employeeCounts[2] ?? 0,
                'jo' => $employeeCounts[3] ?? 0,
            ],
            'clockinout' => [
                'clockin' => $clockinout->whereNotNull('clock_in')->count(),
                'inprogress' => $clockinout->whereNotNull('clock_in')->whereNull('clock_out')->count(), // Clocked in but not clocked out
                'clockout' => $clockinout->whereNotNull('clock_out')->count(), 
            ],
            'leave' => [
                'pending' => $leaveCounts['pending'] ?? 0,
                'granted' => $leaveCounts['granted'] ?? 0,
                'rejected' => $leaveCounts['rejected'] ?? 0,
            ],
            'earnings' => $earnings,
            'deductions' => $deductions,
            'gsis_billing' => $gsis_billing
        ];

    }    

    public function render()
    {
        return view('livewire.admin.dashboard.index');
    }
}
