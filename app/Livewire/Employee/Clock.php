<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use App\Models\EmployeeInformation;
use App\Models\EmployeeTimelogs;
use App\Models\ShiftSchedule;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Clock extends Component
{

    public $employee_id;
    public $gps_location;
    public $status;
    public $logs;
    public $manipulate_timestamp = '07:00';

    protected $listeners = ['getLocation'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $bsd_emp_identical = config('app.bsd_emp_identical');
        
        $employee_no = Auth::user()->employee_no;

        $bsd_no = EmployeeInformation::where('employee_no', $employee_no)
            ->first()
            ->bsd_no;

        if($bsd_emp_identical) {
            $this->employee_id = $employee_no;
        } else {
            $this->employee_id = $bsd_no;
        }

    }

    public function getLocation($lng, $lat)
    {
        $accessToken = env('MAPBOX_API'); 
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json";

        $response = Http::get($url, [
            'access_token' => $accessToken
        ]);


        if ($response->successful()) {
            $decoded = json_decode($response->body(), true);
            $gps_location = !empty($decoded['features']) ? $decoded['features'][0] : null;
            $this->gps_location = [
                'place' => $gps_location['place_name'],
                'coordinates' => [
                    'lng' => $lng,
                    'lat' => $lat
                ]
            ];
            $this->dispatch('loadMap', [
                'token' => $accessToken,
                'lng' => $lng,
                'lat' => $lat
            ]);
        } else {
            $this->gps_location = null;
        }
    }

    public function showLogs() {
        $timestamp = Carbon::now()->format('Y-m');
        $logService = app(DailyTimeRecordService::class);
        $logs = $logService->getLogs($timestamp, $this->employee_id);

        $cleanLogs = [];

        foreach ($logs as $date => $entries) {
            if (is_array($entries) && !empty($entries)) {
                $cleanLogs[$date] = array_values($entries)[0];
            }
        }

        krsort($cleanLogs);

        $this->logs = $cleanLogs;
        dd($this->logs);
        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function toggleStatus() {

    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}