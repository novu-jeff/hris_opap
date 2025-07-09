<?php

namespace App\Livewire\Employee\TimeAdjustments;

use App\Models\EmployeeAccount;
use App\Models\EmployeeTimeAdjustments;
use App\Models\EmployeeTimeAdjustmentsAttachments;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Apply extends Component
{

    use WithFileUploads;

    public $record_id;
    public $employee_no;
    public $employee_id;
    public $date;
    public $clock_in;
    public $clock_out;
    public $break_in;
    public $break_out;
    public $reason;
    public $attachments = [];
    public $preview_attachments = [];

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {


        $employee_no = Auth::user()->employee_no;
        $employee_id = Auth::user()->id;

        $this->employee_no = $employee_no;
        $this->employee_id = $employee_id;
 
        if(!is_null($this->record_id)) {
            $records = EmployeeTimeAdjustments::where('id', $this->record_id)
                ->where('employee_no', $employee_no)
                ->first();
        
            if(!$records) {
                return redirect()
                    ->route('employee.request-timelog');
            }

            $this->date = $records->date;
            $this->clock_in = $records->clock_in;
            $this->clock_out = $records->clock_out;
            $this->break_in = $records->break_in;
            $this->break_out = $records->break_out;
            $this->reason = $records->reason;
            $this->preview_attachments = $records->attachments->toArray() ?? [];
        }

    }


    public function rules() {
        return [
            'date' => 'required|date',
            'clock_in' => 'required|date_format:H:i',
            'break_out' => 'required|date_format:H:i',
            'break_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'attachments' => 'required|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf',
        ];
    }

    public function removeAttachment(int $id)
    {
        $record = EmployeeTimeAdjustmentsAttachments::find($id);

        if ($record) {
            $record->delete();

            if (Storage::disk('public')->exists('request-timelogs/' . $record->attachment)) {
                Storage::disk('public')->delete('request-timelogs/' . $record->attachment);
                $this->preview_attachments = array_filter($this->preview_attachments, function ($record) use ($id) {
                    return $record['id'] != $id;
                });                
            }
        } 
    }


    public function save(bool $isNotify = true) {
        $this->validate();
    
        if ($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'Yes, I am sure that all the information I have provided is accurate and true. This ensures that there will be no issues as we proceed.';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {
            try {
                
    
                $model = EmployeeTimeAdjustments::updateOrCreate([
                    'id' => $this->record_id,
                ], [
                    'employee_no' => $this->employee_no,
                    'date' => $this->date,
                    'clock_in' => $this->clock_in,
                    'break_out' => $this->break_out,
                    'break_in' => $this->break_in,
                    'clock_out' => $this->clock_out,
                    'reason' => $this->reason,
                ]);

                foreach ($this->attachments as $attachment) {

                    if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                        $filename = strtolower(str_replace(' ', '_', $attachment->getClientOriginalName()));
                    } else {
                        $filename = $attachment;
                    }

                    if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                        $attachment->storeAs('request-timelogs', strtolower($filename), 'public');
                    }

                    $existingAttachment = EmployeeTimeAdjustmentsAttachments::where('employee_requests_id', $model->id)->first();
                    if ($existingAttachment && $existingAttachment->file) {
                        Storage::disk('public')->delete('request-timelogs/' . $existingAttachment->file);
                    }

                    EmployeeTimeAdjustmentsAttachments::updateOrCreate([
                        'employee_requests_id' => $model->id,
                        'attachment' => $filename,
                    ], [
                        'employee_requests_id' => $model->id,
                        'attachment' => $filename,
                    ]);
                    
                }


                if (is_null($this->record_id)) {
                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!',
                        'message' => 'Your application has been submitted. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.'
                    ]);
    
                    $user = EmployeeAccount::find($this->employee_id);
                    $message = 'Employee <strong>' . $this->employee_no . '</strong> has submitted an application for <strong>request timelog</strong>.';
                    $redirect = route('ess.time-adjustments');

                    $user->notify(new Notifications('info', $message, $redirect, 'admin'));
    
                    $this->resetExcept('employee_no', 'employee_id');

                    return;
                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!',
                        'message' => 'Your application has been updated. You will receive an email regarding your application status as soon as we review it. Thank you for your understanding.',
                        'redirect' => route('employee.time-adjustments.edit', ['id' => $this->record_id])
                    ]);
                }
            } catch (\Exception $e) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }
    

    public function render()
    {
        return view('livewire.employee.time-adjustments.apply');
    }
}