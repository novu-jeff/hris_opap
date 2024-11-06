<?php

namespace App\Livewire\Admin\Ess\Announcements;

use App\Models\EmployeeAnnouncements;
use App\Models\EmployeeLeave;
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
            $this->preview_banner = Storage::url('public/announcements/' . $record->banner);
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


    public function rules() {
        return [
            'title' => 'required',
            'content' => 'required',
        ];
    }

    public function message() {
        return [];
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

                if ($banner instanceof \Illuminate\Http\UploadedFile) {

                    $extension = $banner->getClientOriginalExtension(); 
                    $filename = strtolower('announcement'  . '_' . time() . '.' . $extension);
                    $banner->storeAs('announcements', strtolower($filename), 'public');

                    $existingRecord = EmployeeAnnouncements::where('id', $this->record_id)
                        ->first();

                    if ($existingRecord && $existingRecord->banner && $existingRecord !== 'default.jpg') {
                        $previousPath = 'announcements/' . $existingRecord->banner;
                        Storage::disk('public')->delete($previousPath); 
                    }


                    $model::updateOrCreate([
                        'id' => $this->record_id,
                    ], [
                        'banner' => $filename,
                        'title' => $this->title,
                        'content' => $this->content,
                    ]);

                } else {
                    $model::updateOrCreate([
                        'id' => $this->record_id,
                    ], [
                        'banner' => $banner,
                        'title' => $this->title,
                        'content' => $this->content,
                    ]);
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
