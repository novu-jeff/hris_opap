<?php

namespace App\Livewire\Admin\Ess\RequestStatus;

use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\Message;
use App\Models\MessageAttachments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chatbox extends Component
{

    use WithFileUploads;

    public $records;
    public $selected_id;
    public $message;
    public $attachments = [];
    public $preview_attachments;
    protected $listeners = ['selected', 'loadRecords'];

    public function mount() {
        $this->records = [];
        $this->loadRecords();
        $this->dispatch('showLatest');
    }

    public function selected(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        $this->dispatch('showLatest');
    }

    public function loadRecords(int $id = null) {
        $user = EmployeeInformation::with('personal', 'positions')
                    ->when($id, fn($query) => $query->where('id', $id))
                    ->first();
    
        if (!$user) {
            $this->records = [];
            return;
        }
    
        $id = $user->id;
    
        $sent = Message::with('attachments')
            ->where(['from_id' => 0, 'from_role' => 'admin', 'to_id' => $id, 'to_role' => 'employee'])
            ->get();
    
        $received = Message::with('attachments')
            ->where(['from_id' => $id, 'from_role' => 'employee', 'to_id' => 0, 'to_role' => 'admin'])
            ->get();
    
        $messages = $sent->merge($received)->sortBy('id')->values();
    
        $this->records = [
            'user' => $user,
            'messages' => $messages
        ];
        
        $this->selected_id = $id;
        $this->isFirstTime($id);
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
     
    public function isFirstTime(int $id) {

        $model = Message::class;
        $user = EmployeePersonal::where('employee_id', $id)->first();
        $record = $model::where('from_id', $id)
            ->orWhere('to_id', $id)
            ->count();

        if($record <= 0) {

            $name = ucwords($user->firstname . ' ' . $user->lastname);
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
                    'to_id' => $user->employee_id,
                    'to_role' => 'employee',
                    'message' => $message[0] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $this->loadRecords();

        }

    }

    public function send() {

        $this->validate();

        $message = Message::create([
            'from_id' => 0,
            'from_role' => 'admin',
            'to_id' => $this->selected_id,
            'to_role' => 'employee',
            'message' => $this->message ?? null,
        ]);


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
        $this->loadRecords($this->selected_id);
        $this->dispatch('showLatest');
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

    public function makeSeen() {
        return Message::where('from_id', $this->selected_id)
            ->where('to_id', 0)
            ->update([
                'isSeen' => true,
                'seen_timestamp' => Carbon::now()
        ]);
    }

    public function render()
    {
        return view('livewire.admin.ess.request-status.chatbox');
    }
}
