<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeTimelogs;
use App\Models\EmployeeInformation;
use App\Services\ClockInOutService;
use App\Services\DailyTimeRecordService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Clock extends Component
{
    use WithFileUploads;

    public $bsd_emp_identical;
    public $employee_id;
    public $employee_no;
    public $gps_location;
    public $isFaceDetected;
    public $status;
    public $entry;
    public $imageCaptured;
    public $isForcedOut = false;
    public $accomplishment;
    public $logs = [];
    public $manipulate_timestamp = '07:00';

    protected $listeners = [
        'getLocation',
        'imageCaptured',
        'triggerClockForced',
        'saveAccomplishment'
    ];

    public function mount(): void
    {
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
        $this->employee_no = Auth::user()->employee_no;

        $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();
        if (!$employee) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Employee not found.'
            ]);
            return;
        }

        $this->employee_id = $this->bsd_emp_identical ? $employee->employee_no : $employee->bsd_no;

        $this->toggleStatus();
    }

    public function loadRecords()
    {
        $this->employee_no = Auth::user()->employee_no;
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
    }

    public function getLocation($lng, $lat)
    {
        $accessToken = env('MAPBOX_API');
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json";

        $response = Http::get($url, ['access_token' => $accessToken]);

        if ($response->successful()) {
            $decoded = $response->json();
            $gps_location = $decoded['features'][0] ?? null;
            $this->gps_location = $gps_location ? [
                'place' => $gps_location['place_name'] ?? '',
                'coordinates' => ['lng' => $lng, 'lat' => $lat]
            ] : null;

            $this->dispatch('loadMap', [
                'token' => $accessToken,
                'lng' => $lng,
                'lat' => $lat,
                'place' =>  $gps_location['place_name'] ?? null
            ]);
        } else {
            $this->gps_location = null;
        }
    }

    public function showLogs()
    {
        $this->logs = $this->getLogs();
        $this->dispatch('showModal', ['modal' => 'logs_modal']);
    }

    private function getLogs()
    {
        $records = EmployeeTimelogs::with('employee.personal')
            ->where('employee_id', $this->employee_id)
            ->whereMonth('timestamp', now()->month)
            ->whereYear('timestamp', now()->year)
            ->get();

        return $records
            ->groupBy(fn($record) => optional(Carbon::parse($record->timestamp))->format('j/n/Y') . '|' . ($record->employee_id ?? 'undefined'))
            ->filter()
            ->map(function ($logs, $key) {
                [$date, $employee_id] = explode('|', $key);
                $logs = $logs->sortBy('timestamp')->values();

                $formatLog = fn($log) => [
                    'time' => optional(Carbon::parse($log->timestamp))->format('H:i:s'),
                    'captured_image' => $log->captured_image,
                    'captured_location' => $log->captured_location,
                    'accomplishment' => $log->accomplishment ?? null
                ];

                $baseData = [
                    'date' => $date,
                    'bsd_no' => $employee_id,
                    'employee' => optional($logs->first())->employee,
                    'origin' => optional($logs->first())->origin,
                ];

                $count = $logs->count();
                $lastHasAccomplishment = !empty(optional($logs->last())->accomplishment);

                if ($count === 2 && $lastHasAccomplishment) {
                    return array_merge($baseData, ['logs' => [
                        $formatLog($logs[0]),
                        [],
                        [],
                        $formatLog($logs[1]),
                    ]]);
                }

                if ($count === 3 && $lastHasAccomplishment) {
                    return array_merge($baseData, ['logs' => [
                        $formatLog($logs[0]),
                        $formatLog($logs[1]),
                        [],
                        $formatLog($logs[2]),
                    ]]);
                }

                return array_merge($baseData, ['logs' => $logs->map($formatLog)->values()->all()]);
            })
            ->sortByDesc(fn($item) => Carbon::createFromFormat('j/n/Y', $item['date']))
            ->values();
    }

    public function toggleStatus()
    {
        $service = app(DailyTimeRecordService::class);
        $shiftSchedule = $service->getShiftSchedule($this->employee_no);
        $hasBreaktime = $shiftSchedule->is_breaktime_required ?? false;

        $timestamp = now()->format('Y-m-d');
        $bsd_no = $this->bsd_emp_identical ? $this->employee_no : $service->getBsdNo($this->employee_no);

        $model = EmployeeTimelogs::where('employee_id', $bsd_no)->where('timestamp', 'LIKE', "{$timestamp}%");
        $clockRecords = $model->get();
        $entry = $model->count();

        $hasAccomplishment = $clockRecords->contains(fn($record) => !empty($record->accomplishment));
        $this->entry = $entry;

        if ($hasBreaktime) {
            $this->status = match (true) {
                $entry === 0 => 'Clock In',
                $entry === 1 => 'Lunch Out',
                $entry === 2 => 'Lunch In',
                $entry === 3 => 'Clock Out',
                $entry === 4 || $hasAccomplishment => 'Done',
                default => 'Done',
            };
        } else {
            $this->status = match (true) {
                $entry === 0 => 'Clock In',
                $entry === 1 => 'Clock Out',
                $entry === 2 || $hasAccomplishment => 'Done',
                default => 'Done',
            };
        }
    }


   public function triggerClock()
    {

        if (!$this->isFaceDetected) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'No Face Detected',
                'message' => 'No face detected. Please ensure your face is visible to the camera.',
            ]);
            return;
        }

        if (is_null($this->gps_location)) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'No Location Detected',
                'message' => 'No location detected. Please make sure to enable your location or GPS.',
            ]);
            return;
        }

        if (empty($this->imageCaptured)) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Image Missing',
                'message' => 'No image was captured.',
            ]);
            return;
        }

        $service = app(ClockInOutService::class);

        $toProcess = [
            'timestamp' => $this->manipulate_timestamp,
            'captured_image' => $this->imageCaptured,
            'captured_location' => $this->gps_location,
            'accomplishment' => $this->accomplishment ?? null,
        ];

        $response = $service->process($this->entry, $toProcess, $this->employee_no);

        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => $response['alert'],
            'title' => $response['title'],
            'message' => $response['message'],
        ]);

        $this->imageCaptured = null;
        $this->toggleStatus();
    }

    public function delete()
    {
        $date = now()->format('Y-m-d');
        EmployeeTimelogs::where('timestamp', 'like', "%{$date}%")->delete();
        $this->toggleStatus();
    }

    public function imageCaptured($imageData, $isFaceDetected = false, $isForcedOut = false)
    {
        $this->imageCaptured = $imageData;
        $this->isFaceDetected = $isFaceDetected;
        $this->isForcedOut = $isForcedOut;
    }

    public function saveAccomplishment()
    {
        if (!$this->accomplishment) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Missing File',
                'message' => 'Please upload an accomplishment report.',
            ]);
            return;
        }

        $file = $this->accomplishment;
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['doc', 'docx', 'pdf'])) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Invalid File',
                'message' => 'Only DOC, DOCX, and PDF are allowed.',
            ]);
            return;
        }

        $fileName = $this->employee_no . '_' . time() . '.' . $ext;
        $file->storeAs('accomplishments', $fileName, 'public');
        $this->accomplishment = $fileName;

        $this->triggerClock();
    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}
