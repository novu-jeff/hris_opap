<?php

namespace App\Livewire\Employee;

use App\Livewire\Admin\Ess\RequestStatus\Chatbox;
use App\Models\EmployeeAccount;
use App\Models\Message;
use App\Models\MessageAttachments;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class RequestStatus extends Component
{
    
    use WithFileUploads;

    public $user;
    public $message;
    public $records;
    public $attachments = [];
    public $preview_attachments;
    
    public function mount() {
        $this->user = Auth::user()->load('personal')->personal;
        $this->isFirstTime();
        $this->loadRecords();
        $this->makeSeen();
        $this->dispatch('showLatest');
    }

    public function loadRecords() {
        $sent = Message::with('attachments')->where('from_id', $this->user->employee_no)
            ->where('from_role', 'employee')
            ->where('to_id', 0)
            ->where('to_role', 'admin')
            ->get();
        $received = Message::with('attachments')->where('from_id', 0)
            ->where('from_role', 'admin')
            ->where('to_id', $this->user->employee_no)
            ->where('to_role', 'employee')
            ->get();

        $mergedMessages = $sent->merge($received);
        $sortedMessages = $mergedMessages->sortBy('id')->values();

        $this->records = $sortedMessages;

    }

    public function isFirstTime() {

        $model = Message::class;
        $record = $model::where('from_id', $this->user->employee_no)
            ->orWhere('to_id', $this->user->employee_no)
            ->count();

        if($record <= 0) {

            $name = ucwords($this->user->firstname . ' ' . $this->user->lastname);
            $messages = [
                [
                    'Hello ' . $name
                ], [
                    'I\'m Juan Dela Cruz from the HR department. I just wanted to check in and see if there\'s anything we can assist you with. If you have any questions or need support, feel free to reach out. We\'re here to help!'
                ]
            ];

            foreach ($messages as $message) {
                Message::insert([
                    'from_id' => 0,
                    'from_role' => 'admin',
                    'to_id' => $this->user->employee_no,
                    'to_role' => 'employee',
                    'message' => $message[0] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }


        }

    }

    public function updated($propertyName) {
        if ($propertyName === 'attachments' && isset($this->attachments)) {
            
            $this->preview_attachments = [];
    
            foreach ($this->attachments as $attachment) {

                if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                    $extension = strtolower($attachment->getClientOriginalExtension());
    
                    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                        $this->preview_attachments[] = [
                            'type' => 'image',
                            'url' => $attachment->temporaryUrl(),
                        ];
                    } elseif ($extension === 'pdf') {
                        $filename = $attachment->store('public/temp');
                        $url = Storage::url($filename);
    
                        $this->preview_attachments[] = [
                            'type' => 'pdf',
                            'url' => $url,
                        ];
                    } 
                } else {
                    $this->validate();
                }
            }
        }
    }

    public function rules() {
        return [
            'message' => 'required_without:attachments', 
            'attachments' => 'required_without:message|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ];
    }

    public function messages() {
        return [
            'message.required_without' => 'Message is Required',
            'attachments.required_without' => 'Attachment is Required'
        ];
    }

    public function makeSeen() {

        $employee_no = $this->user->employee_no;

        return Message::where('to_id', $employee_no)
            ->update([
                'isSeen' => true,
            ]);
    }

    public function download($message, $attachment) {

        $record = MessageAttachments::where('message_id', $message)
            ->where('id', $attachment)
            ->first();
        
        if(is_null($record)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Attachment does not exists'
            ]);
        }
        
        $path = 'messages/' . $record->attachment;
        
        if(!Storage::disk('public')->exists($path)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Attachment does not exists'
            ]);
        } 

        return response()->download(Storage::disk('public')->path($path), $record->original);
    }

    public function send() {

        $this->validate();

        $message = Message::create([
            'from_id' => $this->user->employee_no,
            'from_role' => 'employee',
            'to_id' => '0',
            'to_role' => 'admin',
            'message' => $this->message ?? null,
        ]);

        $sender_name = $this->user->firstname . ' ' . $this->user->lastname  . '(employee)';
        $user = EmployeeAccount::find($this->user->id);
        $user?->notify(new Notifications('message', $sender_name . ' sent you a message.', route('ess.request-status', ['employee_no' => $this->user->employee_no]), 'admin'));

        foreach ($this->attachments as $index => $attachment) {

            if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                $extension = $attachment->getClientOriginalExtension();
                $original_filename = $attachment->getClientOriginalName();
                $new_filename = time() . '_' . $message->id . '_' . $index . '.' . $extension;
                
                $path = 'messages';

                $attachment->storeAs($path, strtolower($new_filename), 'public');
            
                MessageAttachments::insert([
                    'message_id' => $message->id,
                    'original' => $original_filename,
                    'attachment' => $new_filename
                ]);
            
            }

        }

        $this->reset('message', 'preview_attachments', 'attachments');
        $this->loadRecords();
        $this->dispatch('showLatest');

    }

    public function render()
    {
        return view('livewire.employee.request-status');
    }
}
