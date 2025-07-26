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

    public $bsd_emp_identical;
    public $employee_no;
    public $gps_location;
    public $status;
    public $logs;
    public $manipulate_timestamp = '07:00';

    protected $listeners = ['getLocation'];

    public function mount() {
        $this->loadRecords();
        $this->toggleStatus();
    }

    public function loadRecords() {
        
        $this->employee_no = Auth::user()->employee_no;
        $this->bsd_emp_identical = config('app.bsd_emp_identical');

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

        $service = app(DailyTimeRecordService::class);

        $timestamp = Carbon::now()->format(format: 'm-Y');
        $dtr = $service->getDailyTimeRecord($this->employee_no, $timestamp);
        
        $filtered = [];

        foreach ($dtr['logs'] as $logs) {
            if (
                is_null($logs['clock_in']) &&
                is_null($logs['lunch_in']) &&
                is_null($logs['lunch_out']) &&
                is_null($logs['clock_out'])
            ) {
                continue;
            }

            $filtered[$logs['date']] = $logs;
        }

        krsort($filtered);

        $this->logs = $filtered;
       
        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function toggleStatus() {
        
        $service = app(DailyTimeRecordService::class);

        $timestamp = Carbon::now()->format('Y-m-d');
        
        $bsd_no = $this->bsd_emp_identical ? $this->employee_no : $service->getBsdNo($this->employee_no);

        $model = EmployeeTimelogs::where('employee_id', $bsd_no)
            ->where('timestamp', 'LIKE', "{$timestamp}%");

        $clockRecords = $model->get();

        $entry = $model->count();

        $hasAccomplishment = $clockRecords->contains(function ($record) {
            return !empty($record['accomplishment']);
        });


        switch (true) {
            case $entry === 0:
                $this->status = 'Clock In';
                break;

            case $entry === 1:
                $this->status = 'Lunch Out';
                break;

            case $entry === 2:
                $this->status = 'Lunch In';
                break;

            case $entry === 3:
                $this->status = 'Clock Out';
                break;

            case $entry === 4 || $hasAccomplishment:
                $this->status = 'Done';
                break;
        }

    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}