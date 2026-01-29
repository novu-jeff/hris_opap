<?php

namespace App\Livewire\Admin\Ess\Announcements;

use App\Models\EmployeeAccount;
use App\Models\EmployeeAnnouncementAttachments;
use App\Models\EmployeeAnnouncements;
use App\Models\EmployeeLeave;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Add extends Component
{

    use WithFileUploads;

    public $banner;
    public $title;
    public $content;
    public $record_id;
    public $user_id;
    public $preview_banner;
    public array $attachments;

    protected $listeners = ['ckeditor', 'save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        if(!is_null($this->record_id)) {
            $record = EmployeeAnnouncements::where('id', $this->record_id)
                ->first();
        
            if(!$record) {
                return redirect()
                    ->route('ess.announcements.index');
            }

            $this->banner = $record->banner;
            $this->title = $record->title;
            $this->content = $record->content;
             
            $this->preview_banner = $record->banner === 'default.jpg' ? asset('img/announcement.jpg') : Storage::url('public/announcements/' . $record->banner);
            $this->attachments = $record->attachments->toArray() ?? [];
            
        }
   
    }

    public function updated($propertyName) {
        

        if ($propertyName == 'banner') {

            if (isset($this->banner)) {
                
                $file = $this->banner;

                if ($file instanceof \Illuminate\Http\UploadedFile) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, ['jpg', 'jpeg', 'gif', 'png'])) {
                        $filename = $file->store('public/temp'); 
                        $url = Storage::url($filename); 
    
                        return $this->preview_banner = $url;
                    } 

                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops!', 
                        'isRemoveRowDT' => false,
                        'message' => 'Banner must be an image.'
                    ]);
                  

                } else {    
                    $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops!', 
                        'isRemoveRowDT' => false,
                        'message' => 'Error: Invalid File'
                    ]);
                }
            }
        }
        
    }

    public function addRecord() {
        $this->attachments[] = [
            'name' => '',
            'file' => ''
        ];
    }

    public function removeRecord(int $key) {
        unset($this->attachments[$key]);
    }


    public function rules() {
        $rules = [
            'title' => 'required',
            'content' => 'required',
        ];
    
        // Validate banner if it is an uploaded file
        if ($this->banner instanceof \Illuminate\Http\UploadedFile) {
            $rules['banner'] = 'required|image|mimes:jpg,jpeg,png,gif';
        }
    
        // Check if attachments is an array and make 'file' required for each attachment
        foreach ($this->attachments as $key => $attachment) {
            if ($this->banner instanceof \Illuminate\Http\UploadedFile || is_null($this->record_id)) {
                $rules["attachments.{$key}.name"] = 'required';
                $rules["attachments.{$key}.file"] = 'required|file|mimes:jpg,jpeg,png,gif,docx,doc,xls,xlsx,pdf';
            }
        }
        
    
        return $rules;
    }
    

    public function messages() {
        return [
            'attachments.*.name.required' => 'Attachment name is required.',
            'attachments.*.file.required' => 'Attachment file is required.',
            'attachments.{$key}.file.file' => 'The uploaded attachment must be a valid file.',
            'attachments.{$key}.file.mimes' => 'Only JPG, JPEG, PNG, GIF, DOCX, DOC, XLS, XLSX and PDF files are allowed.',
        ];
    }

    public function ckeditor($data) {
        $this->content = $data;
    }

    public function save(bool $isNotify = true) {
        
        $this->validate();

        if($isNotify) {
            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'save';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);
        } else {

            try {

                $banner = $this->banner;
                $model = EmployeeAnnouncements::class;

                $bannerFilename = 'default.jpg';

                if ($banner instanceof \Illuminate\Http\UploadedFile) {
                    $extension = $banner->getClientOriginalExtension();
                    $bannerFilename = strtolower('announcement_' . time() . '.' . $extension);
                    $banner->storeAs('announcements', $bannerFilename, 'public');
                }
                
                $existingRecord = EmployeeAnnouncements::where('id', $this->record_id)->first();
                if ($existingRecord && $existingRecord->banner && $existingRecord->banner !== 'default.jpg') {
                    Storage::disk('public')->delete('announcements/' . $existingRecord->banner);
                }
                
                $model = EmployeeAnnouncements::updateOrCreate([
                    'id' => $this->record_id,
                ], [
                    'banner' => $bannerFilename,
                    'title' => $this->title,
                    'content' => $this->content,
                ]);
                
                $action = $existingRecord ? 'updated' : 'added';
                
                $users = EmployeeAccount::pluck('employee_no')->toArray();
                EmployeeAccount::whereIn('employee_no', $users)->get()->each(function ($userModel) use ($model, $action) {
                    $message = '"' . ucwords($model->title) . '" was ' . $action . ' to announcements.';
                    $redirect = route('employee.announcements.view', ['id' => $model->id]);
                    $userModel->notify(new Notifications('info', $message, $redirect, 'employee'));
                });
                

                if(!is_null($this->record_id)) {
                    EmployeeAnnouncementAttachments::where('announcement_id', $this->record_id)
                        ->delete();
                }

                foreach ($this->attachments as $attachment) {

                    $file = $attachment['file'];

                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $filename = 'attachment_' . time() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
                    } else {
                        $filename = $file;
                    }

                
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $file->storeAs('announcements', strtolower($filename), 'public');
                    }
                
                    $existingAttachment = EmployeeAnnouncementAttachments::where('announcement_id', $model->id)->first();
                    if ($existingAttachment && $existingAttachment->file) {
                        Storage::disk('public')->delete('announcements/' . $existingAttachment->file);
                    }

                    $attachmentModel = EmployeeAnnouncementAttachments::updateOrCreate([
                        'announcement_id' => $model->id,
                        'file' => $filename,
                    ], [
                        'file' => $filename,
                        'name' => $attachment['name'] ?? '',
                    ]);
                
                    $attachmentAction = $existingAttachment ? 'updated' : 'added';
                
                    EmployeeAccount::whereIn('employee_no', $users)->get()->each(function ($userModel) use ($attachmentModel, $attachmentAction) {
                        $message = '"' . $attachmentModel->name . '" was ' . $attachmentAction . ' to announcements.';
                        $redirect = route('employee.announcements.view', ['id' => $attachmentModel->announcement_id]);
                        $userModel->notify(new Notifications('info', $message, $redirect, 'employee'));
                    });
                }
                
                

                if(is_null($this->record_id)) {

                    $this->resetExcept('user_id');

                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Announcement was added successfully.'
                    ]);

                } else {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'success',
                        'title' => 'Yey!', 
                        'message' => 'Announcement has been updated.'
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
        return view('livewire.admin.ess.announcements.add');
    }
}
