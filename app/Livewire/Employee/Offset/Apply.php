<?php

namespace App\Livewire\Employee\Offset;

use App\Models\EmployeeAccount;
use App\Models\EmployeeOffsetCredit;
use App\Models\EmployeeOffsetRequest;
use App\Models\EmployeeInformation;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\EmployeeOffsetAttachment;

class Apply extends Component
{
    use WithFileUploads;

    public $record_id;

    public $employee_id;
    public $employee_no;

    public $earnedHours = 0;
    public $usedHours = 0;
    public $remainingHours = 0;
    public $attachments = [];
    public $preview_attachments = [];

    public array $fields = [
        'filing_date' => '',
        'office_order_no' => '',
        'date_from' => '',
        'date_to' => '',
        'hours_requested' => '',
        'reason' => '',
    ];

    protected $listeners = ['save'];

    public function mount()
    {
        $this->employee_id = Auth::user()->id;
        $this->employee_no = Auth::user()->employee_no;

        $this->loadCredits();

        if ($this->record_id) {

            $record = EmployeeOffsetRequest::where('id', $this->record_id)
                ->where('employee_no', $this->employee_no)
                ->firstOrFail();

            $this->fields = [

                'filing_date' => $record->filing_date,
                'office_order_no' => $record->office_order_no,
                'date_from' => $record->date_from,
                'date_to' => $record->date_to,
                'hours_requested' => $record->hours_requested,
                'reason' => $record->reason,
                'status' => $record->status,
                'disapproval_note' => $record->disapproval_note,

            ];

            $this->preview_attachments = $record->attachments
                ->map(function ($attachment) {

                    return [

                        'id' => $attachment->id,

                        'attachment' => $attachment->attachment,

                    ];

                })
                ->toArray();
        } else {

            $this->fields['filing_date'] = now()->toDateString();

        }
    }

    private function loadCredits()
    {
        $credits = EmployeeOffsetCredit::where('employee_no', $this->employee_no);

        $this->earnedHours = $credits->sum('earned_hours');
        $this->usedHours = $credits->sum('used_hours');
        $this->remainingHours = $this->earnedHours - $this->usedHours;
    }

    protected function rules()
    {
        return [

            'fields.office_order_no' => 'nullable|max:100',

            'fields.filing_date' => 'required|date',

            'fields.date_from' => 'required|date',

            'fields.date_to' => 'required|date|after_or_equal:fields.date_from',

            'fields.hours_requested' => [

                'required',

                'numeric',

                'min:0.5',

                function ($attribute, $value, $fail) {

                    if ($value > $this->remainingHours) {

                        $fail('Requested hours exceed your available offset credits.');

                    }

                }

            ],

            'fields.reason' => 'required|string|max:500',

            'attachments' => [
                $this->record_id ? 'nullable' : 'required',
                'array'
            ],

            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',

        ];
    }

    protected $messages = [

        'fields.filing_date.required' => 'Date Filed is required.',

        'fields.date_from.required' => 'Date From is required.',

        'fields.date_to.required' => 'Date To is required.',

        'fields.date_to.after_or_equal' => 'Date To must be after Date From.',

        'fields.hours_requested.required' => 'Hours Requested is required.',

        'fields.reason.required' => 'Purpose is required.',

    ];

    public function save($notify = true)
    {
        $this->validate();

        if ($notify) {

            $this->dispatch('showConfirmation', [

                'title' => 'Submit Application?',

                'message' => 'Please confirm that all information is correct.',

                'action' => 'save'

            ]);

            return;
        }

        try {

            if (!$this->record_id) {

                $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();

                $offset = EmployeeOffsetRequest::create([

                    'employee_information_id' => $employee->id,

                    'employee_no' => $this->employee_no,

                    'office_order_no' => $this->fields['office_order_no'],

                    'filing_date' => $this->fields['filing_date'],

                    'date_from' => $this->fields['date_from'],

                    'date_to' => $this->fields['date_to'],

                    'hours_requested' => $this->fields['hours_requested'],

                    'reason' => $this->fields['reason'],

                    'status' => 'pending',

                ]);

                if (!empty($this->attachments)) {

                    foreach ($this->attachments as $file) {
                
                        $path = $file->store(
                            'employee/offset',
                            'public'
                        );
                
                        EmployeeOffsetAttachment::create([
                
                            'employee_offset_request_id' => $offset->id,
                
                            'attachment' => $path,
                
                        ]);
                
                    }
                
                }

                $user = EmployeeAccount::find($this->employee_id);

                $message = 'Employee <strong>'.$this->employee_no.'</strong> submitted an <strong>Authority to Render Offsetting</strong> application.';

                $user->notify(
                    new Notifications(
                        'info',
                        $message,
                        route('ess.offset'), // temporary
                        'admin'
                    )
                );

            } else {

                $offset = EmployeeOffsetRequest::findOrFail($this->record_id);
                $offset->update([

                        'office_order_no' => $this->fields['office_order_no'],

                        'filing_date' => $this->fields['filing_date'],

                        'date_from' => $this->fields['date_from'],

                        'date_to' => $this->fields['date_to'],

                        'hours_requested' => $this->fields['hours_requested'],

                        'reason' => $this->fields['reason'],

                    ]);

                    if (!empty($this->attachments)) {

                        foreach ($this->attachments as $file) {
                    
                            $path = $file->store(
                                'employee/offset',
                                'public'
                            );
                    
                            EmployeeOffsetAttachment::create([
                    
                                'employee_offset_request_id' => $offset->id,
                    
                                'attachment' => $path,
                    
                            ]);
                    
                        }
                    
                    }

            }

            $this->dispatch('alert', [

                'showAlert' => true,

                'status' => 'success',

                'title' => 'Success',

                'message' => $this->record_id
                    ? 'Offset application updated successfully.'
                    : 'Offset application submitted successfully.',

                'redirect' => '_reload'

            ]);

            $this->attachments = [];

            $this->dispatch('form-reset');

        } catch (\Exception $e) {

            $this->dispatch('alert', [

                'showAlert' => true,

                'status' => 'error',

                'title' => 'Error',

                'message' => $e->getMessage(),

            ]);

        }

    }

    public function removeAttachment($id)
    {
        $attachment = EmployeeOffsetAttachment::find($id);

        if (!$attachment) {
            return;
        }

        if (Storage::disk('public')->exists($attachment->attachment)) {
            Storage::disk('public')->delete($attachment->attachment);
        }

        $attachment->delete();

        $this->preview_attachments = collect($this->preview_attachments)
            ->reject(fn ($item) => $item['id'] == $id)
            ->values()
            ->toArray();
    }

    public function updatedAttachments()
    {
        $this->validateOnly('attachments');
    }

    public function getRemainingBalanceProperty()
    {
        return max(
            0,
            (float) $this->remainingHours - (float) ($this->fields['hours_requested'] ?? 0)
        );
    }

    public function render()
    {
        return view('livewire.employee.offset.apply');
    }
}