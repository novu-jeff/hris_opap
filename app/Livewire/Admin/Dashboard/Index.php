<?php

namespace App\Livewire\Admin\Dashboard;

use App\Http\Controllers\Admin\Settings\HRIS\EmploymentTypeController;
use App\Models\CompanyInformation;
use App\Models\EmployeeAtro;
use App\Models\EmployeeBusinessSlip;
use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeave;
use App\Models\EmployementTypes;
use App\Models\SocialSecurityBilling;
use App\Models\JobApplicants;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Faker\Provider\ar_EG\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Index extends Component
{

    public $product;
    public $stats;
    public $now;
    public $trails;
    public $companyInfo;

    public function mount() {
        $this->now = Carbon::now();
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $recruitmentCounts = JobApplicants::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')->toArray();

        $employeeCounts = EmployementTypes::withCount('employees')->get();

        // Format the result for easier readability (optional)
        $employeeCounts = $employeeCounts->map(function ($type) {
            return [
                'employment_type' => $type->name,
                'employee_count' => $type->employees_count,
            ];
        });
            
        $leaveCounts = EmployeeLeave::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')->toArray();

        
        $obsCounts = EmployeeBusinessSlip::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')->toArray();

        $atroCounts = EmployeeAtro::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')->toArray();

        $earnings = OtherEarnings::all();
        $deductions = OtherDeductions::all();

        $social_security = SocialSecurityBilling::with('items')
            ->orderBy('billing_month', 'desc')
            ->first();

        $clockinout = EmployeeTimelogs::whereDate('created_at', Carbon::today())->get();
        
        $this->companyInfo = $this->getCompanyInformation();

        $this->stats = [
            'recruitment' => [
                'pending' => $recruitmentCounts['pending'] ?? 0,
                'interview' => $recruitmentCounts['interview'] ?? 0,
                'placement' => $recruitmentCounts['placement'] ?? 0,
                'onboarding' => $recruitmentCounts['onboarding'] ?? 0,
                'hired' => $recruitmentCounts['hired'] ?? 0,
                'rejected' => $recruitmentCounts['disapproved'] ?? 0
            ],
            'employee' => $employeeCounts,
            'clockinout' => [
                'clockin' => $clockinout->whereNotNull('clock_in')->count(),
                'inprogress' => $clockinout->whereNotNull('clock_in')->whereNull('clock_out')->count(),
                'clockout' => $clockinout->whereNotNull('clock_out')->count(),
            ],
            'leave' => [
                'pending' => $leaveCounts['pending'] ?? 0,
                'granted' => $leaveCounts['approved'] ?? 0,
                'rejected' => $leaveCounts['disapproved'] ?? 0,
            ],
            'obs' => [
                'pending' => $obsCounts['pending'] ?? 0,
                'granted' => $obsCounts['approved'] ?? 0,
                'rejected' => $obsCounts['disapproved'] ?? 0,
            ],
            'atro' => [
                'pending' => $atroCounts['pending'] ?? 0,
                'granted' => $atroCounts['approved'] ?? 0,
                'rejected' => $atroCounts['disapproved'] ?? 0,
            ],
            'earnings' => $earnings,
            'deductions' => $deductions,
            'social_security' => $social_security ? $social_security->toArray() : [],
        ];
        
        $this->getTrails();
    }
   

    private function getTrails() {
        $directory = storage_path('logs/trails');
        
        if (!File::exists($directory)) {
            $this->trails = [];
            return;
        }

        $files = File::files($directory);

        
        $this->trails = collect($files)
            ->sortByDesc(fn($file) => $file->getFilename())
            ->take(5) // ⬅️ limit to 5
            ->map(fn($file) => $file->getFilename())
            ->toArray();
    }

    private function getCompanyInformation() {
        $companyInfo = CompanyInformation::with('type')->first();
        return $companyInfo;
    }

    public function download(string $log) {
        $directory = storage_path('logs/trails');
        $filePath = $directory . DIRECTORY_SEPARATOR . $log;

        if (!File::exists($filePath)) {
            session()->flash('error', 'Log file does not exist.');
            return;
        }

        return response()->download($filePath);
    }

    public function render()
    {
        return view('livewire.admin.dashboard.index');
    }
}