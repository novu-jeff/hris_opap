<?php

namespace App\Livewire\Admin\Ess\RequestStatus;

use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\Message;
use App\Models\MessageAttachments;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chatbox extends Component
{
    use WithFileUploads;

    public $employee_no;
    public $records = [];
    public $selected_id;
    public $message;
    public $attachments = [];
    public $preview_attachments;

    protected $listeners = ['selected', 'loadRecords'];

    public function mount()
    {
        $this->selected($this->employee_no);
        $this->dispatch('showLatest');
    }

    public function selected(string $employee_no)
    {
        $this->selected_id = $employee_no;
        $this->loadRecords($employee_no);
        $this->dispatch('showLatest');
    }

    public function loadRecords(string $employee_no = null)
    {
        
        $user = EmployeeInformation::with(['personal', 'positions'])
            ->when($employee_no, fn($query) => $query->where('employee_no', $employee_no))
            ->first();


        if (!$user) {
            return redirect()->route('ess.request-status');
        }

        $sent = Message::with('attachments')
            ->where(['from_id' => 0, 'from_role' => 'admin', 'to_id' => $user->employee_no, 'to_role' => 'employee'])
            ->get();

        $received = Message::with('attachments')
            ->where(['from_id' => $user->employee_no, 'from_role' => 'employee', 'to_id' => 0, 'to_role' => 'admin'])
            ->get();

        $this->records = [
            'user' => $user,
            'messages' => $sent->merge($received)->sortBy('id')->values(),
        ];

        $this->isFirstTime($user->employee_no);
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'attachments') {
            $this->preview_attachments = collect($this->attachments)
                ->filter(fn($attachment) => $attachment instanceof \Illuminate\Http\UploadedFile)
                ->map(fn($attachment) => $this->processAttachmentPreview($attachment))
                ->values()
                ->toArray();
        }
    }

    private function processAttachmentPreview($attachment)
    {
        $extension = strtolower($attachment->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return ['type' => 'image', 'url' => $attachment->temporaryUrl()];
        }

        if ($extension === 'pdf') {
            $filename = $attachment->store('public/temp');
            return ['type' => 'pdf', 'url' => Storage::url($filename)];
        }

        return null;
    }

    public function rules()
    {
        return [
            'message' => 'required_without:attachments',
            'attachments' => 'required_without:message|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'message.required_without' => 'Message is required',
            'attachments.required_without' => 'Attachment is required',
        ];
    }

    public function isFirstTime(string $id)
    {
        if (Message::where('from_id', $id)->orWhere('to_id', $id)->doesntExist()) {
            $user = EmployeePersonal::where('employee_no', $id)->first();
            $name = ucwords("{$user->firstname} {$user->lastname}");

            $messages = [
                "Hello {$name}",
                "I’m Juan Dela Cruz from the HR department. I just wanted to check in and see if there’s anything we can assist you with. If you have any questions or need support, feel free to reach out. We’re here to help!",
            ];

            foreach ($messages as $message) {
                Message::create([
                    'from_id' => 0,
                    'from_role' => 'admin',
                    'to_id' => $id,
                    'to_role' => 'employee',
                    'message' => $message,
                    'created_at' => Carbon::now(),
                ]);
            }

            $this->loadRecords();
        }
    }

    public function send()
    {
        $this->validate();

        $message = Message::create([
            'from_id' => 0,
            'from_role' => 'admin',
            'to_id' => $this->selected_id,
            'to_role' => 'employee',
            'message' => $this->message,
        ]);

        foreach ($this->attachments as $index => $attachment) {
            if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                $this->storeAttachment($attachment, $message->id, $index);
            }
        }

        $sender = Auth::user();
        $sender_name = $sender->name . ' (' . $sender->roles[0]->name . ')';
        $user = EmployeeAccount::where('employee_no', $this->selected_id)->first();
        $user?->notify(new Notifications('message', $sender_name . ' sent you a message.', route('employee.request-status'), 'employee'));

        $this->reset('message', 'preview_attachments', 'attachments');
        $this->loadRecords($this->selected_id);
        $this->dispatch('showLatest');
    }

    private function storeAttachment($attachment, $messageId, $index)
    {
        $extension = $attachment->getClientOriginalExtension();
        $originalFilename = $attachment->getClientOriginalName();
        $newFilename = time() . "_{$messageId}_{$index}." . strtolower($extension);
        $path = 'messages';

        $attachment->storeAs($path, $newFilename, 'public');

        MessageAttachments::create([
            'message_id' => $messageId,
            'original' => $originalFilename,
            'attachment' => $newFilename,
        ]);
    }

    public function download($message, $attachment)
    {
        $record = MessageAttachments::where('message_id', $message)
            ->where('id', $attachment)
            ->first();

        if (!$record || !Storage::disk('public')->exists('messages/' . $record->attachment)) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Attachment does not exist',
            ]);
        }

        return response()->download(Storage::disk('public')->path('messages/' . $record->attachment), $record->original);
    }

    public function makeSeen()
    {
        if ($this->selected_id) {
            Message::where('from_id', $this->selected_id)
                ->where('to_id', 0)
                ->update(['isSeen' => true]);
        }
    }

    public function render()
    {
        return view('livewire.admin.ess.request-status.chatbox');
    }
}
