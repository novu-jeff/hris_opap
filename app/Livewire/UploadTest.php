<?php
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadTest extends Component
{
    use WithFileUploads;

    public $file;

    public function save()
    {
        $this->file->store('test');
    }

    public function render()
    {
        return view('livewire.upload-test');
    }
}
