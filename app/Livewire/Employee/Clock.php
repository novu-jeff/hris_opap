<?php

namespace App\Livewire\Employee;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Clock extends Component
{

    public $user_id;
    public $isClockedIn = false;
    public $isClockedOut = false;
    public $isImageCaptured = false;
    public $capturedImage;
    public $capturedLocation;
    public $logs;

    protected $listeners = ['clockin', 'clockout'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $user_id = Auth::user()->employee_no;

        if(is_null($user_id)) {
            return redirect()->route('employee.clock');
        };

        $this->user_id = $user_id;

        $today = Carbon::today();

        $records = EmployeeClockInOut::where('employee_no', $user_id)
            ->whereDate('created_at', $today)->first();

        if ($records) {
            if (!is_null($records->clock_in)) {
                $this->isClockedIn = true;
            }
        
            if (!is_null($records->clock_out)) {
                $this->isClockedOut = true;
            }
        }
    }

    public function clockin(bool $isNotify = true) {
        if ($this->isAlreadyInOut('in')) {
            return;
        }

        if ($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'clockin';

            return $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            $this->dispatch('capture');
        }
    }

    public function clockout(bool $isNotify = true) {
        if ($this->isAlreadyInOut('out')) {
            return;
        }

        if ($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'clockout';

            return $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            $this->dispatch('capture');
        }
    }

    public function isAlreadyInOut(string $type) {
        $today = Carbon::today();

        if ($type == 'in') {
            $records = EmployeeClockInOut::where('employee_no', $this->user_id)
                ->whereDate('created_at', $today)->first();

            if ($records && !is_null($records->clock_in)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'You have already clocked in today at ' . Carbon::parse($records->clock_in)->format('M d, Y h:i A')
                ]);
                return true;
            }
        }

        if ($type == 'out') {
            $records = EmployeeClockInOut::where('employee_no', $this->user_id)
                ->whereDate('created_at', $today)->first();

            if (!$records || is_null($records->clock_in)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'Unable to clock out, you must clock in first!'
                ]);
                return true;
            }

            if (!is_null($records->clock_out)) {
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'info',
                    'title' => 'Oops', 
                    'message' => 'You have already clocked out today at ' . Carbon::parse($records->clock_out)->format('M d, Y h:i A')
                ]);
                return true;
            }
        }
    }

    public function saveLocation() {
        $ip = request()->ip();
        $api = env('IPINFO_API');
        $response = Http::get("http://ipinfo.io/{$ip}/json?token={$api}");

        if ($response->successful()) {
            $locationData = $response->json();

            if (!$locationData) {
                return 'running in local environment';
            }

            if(!isset($locationData['loc'])) {
                return 'running in local environment';
            }

            $location = explode(',', $locationData['loc']);
            $latitude = $location[0]; 
            $longitude = $location[1];

            return $latitude . ' ' . $longitude;
        }
    }

    public function savePhoto($imageData, $isImageCaptured) {
        $this->isImageCaptured = $isImageCaptured;

        if (!$isImageCaptured) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'info',
                'title' => 'Oops',
                'message' => 'Unable to clock in or clock out due to the absence of a captured image. Please ensure that your browser\'s camera is enabled and positioned to capture your face clearly.'
            ]);
        }

        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = $this->user_id . '_' . time() . '.png';

        $this->capturedImage = $imageName;
        Storage::disk('public')->put('clockinout/' . $imageName, base64_decode($image));

        $this->clockInProcess();
    }

    public function clockInProcess() {
        $location = $this->saveLocation();
        $timestamp = Carbon::now();

        $record = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($record && is_null($record->clock_out)) {
            $record->update([
                'clock_out' => $timestamp,
                'captured_image_clockout' => $this->capturedImage,
                'captured_location_clockout' => $location
            ]);
            $this->isClockedOut = true;

        } else {
            EmployeeClockInOut::create([
                'employee_no' => $this->user_id,
                'clock_in' => $timestamp,
                'captured_image_clockin' => $this->capturedImage,
                'captured_location_clockin' => $location
            ]);
            $this->isClockedIn = true;
        }

        $this->isImageCaptured = false;

        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Yey!',
            'message' => 'You\'re clocked in at ' . Carbon::parse($timestamp)->format('M d, Y h:i A')
        ]);
    }


    public function showLogs() {
        $records = EmployeeClockInOut::where('employee_no', $this->user_id)
            ->get();

        $this->logs = $records;
        
        $this->dispatch('showModal', [
            'modal' => 'logs_modal'
        ]);

    }

    public function render()
    {
        return view('livewire.employee.clock');
    }
}
