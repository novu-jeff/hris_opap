<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestStatusController extends Controller
{
    public $user;
    public $message;
    public $attachments = [];
    public $preview_attachments;

    public function __construct() {
        $this->user = Auth::user()->load('personal')->personal;
    }

    public function loadRecords() {
        $sent = Message::with('attachments')
            ->where('from_id', $this->user->employee_id)
            ->where('from_role', 'employee')
            ->where('to_id', 0)
            ->where('to_role', 'admin')
            ->get();

        $received = Message::with('attachments')
            ->where('from_id', 0)
            ->where('from_role', 'admin')
            ->where('to_id', $this->user->employee_id)
            ->where('to_role', 'employee')
            ->get();

        $mergedMessages = $sent->merge($received);
        $sortedMessages = $mergedMessages->sortBy('id')->values();

        return response()->json([
            'status' => 'success',
            'records' => $sortedMessages
        ]);
    }

    public function isFirstTime() {
        $record = Message::where('from_id', $this->user->employee_id)
            ->orWhere('to_id', $this->user->employee_id)
            ->count();

        if ($record <= 0) {
            $name = ucwords($this->user->firstname . ' ' . $this->user->lastname);
            $messages = [
                'Hello ' . $name,
                'I\'m Juan Dela Cruz from the HR department. I just wanted to check in and see if there\'s anything we can assist you with. If you have any questions or need support, feel free to reach out. We\'re here to help!'
            ];

            foreach ($messages as $message) {
                Message::create([
                    'from_id' => 0,
                    'from_role' => 'admin',
                    'to_id' => $this->user->employee_id,
                    'to_role' => 'employee',
                    'message' => $message,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function makeSeen() {
        Message::where('from_id', 0)
            ->where('to_id', $this->user->employee_id)
            ->update([
                'isSeen' => true,
                'seen_timestamp' => Carbon::now()
            ]);

        return response()->json(['status' => 'success']);
    }

    public function updatedAttachments(Request $request) {
        $this->attachments = $request->file('attachments', []);
        $this->preview_attachments = [];

        foreach ($this->attachments as $attachment) {
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
        }

        return response()->json([
            'status' => 'success',
            'preview_attachments' => $this->preview_attachments
        ]);
    }

    public function sendMessage(Request $request) {
        $validated = $request->validate([
            'message' => 'required_without:attachments',
            'attachments' => 'required_without:message|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        $message = Message::create([
            'from_id' => $this->user->employee_id,
            'from_role' => 'employee',
            'to_id' => 0,
            'to_role' => 'admin',
            'message' => $request->message ?? null,
        ]);

        foreach ($validated['attachments'] ?? [] as $index => $attachment) {
            $extension = $attachment->getClientOriginalExtension();
            $original_filename = $attachment->getClientOriginalName();
            $new_filename = time() . '_' . $message->id . '_' . $index . '.' . $extension;

            $attachment->storeAs('messages', strtolower($new_filename), 'public');

            MessageAttachments::create([
                'message_id' => $message->id,
                'original' => $original_filename,
                'attachment' => $new_filename
            ]);
        }

        $this->loadRecords();

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully.'
        ]);
    }

    public function download($messageId, $attachmentId) {
        $record = MessageAttachments::where('message_id', $messageId)
            ->where('id', $attachmentId)
            ->first();

        if (is_null($record)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment does not exist'
            ], 404);
        }

        $path = 'messages/' . $record->attachment;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment does not exist'
            ], 404);
        }

        return response()->download(Storage::disk('public')->path($path), $record->original);
    }
}
