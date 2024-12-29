<?php

namespace App\Livewire\Admin\Job\Posts;

use App\Models\JobPosts;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 5;
    public $search = '';

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this job post. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = JobPosts::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Job Post for ' . strtoupper($record->position) . ' deleted successfully' 
                ]);
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }
    }

    public function render()
    {

        $model = JobPosts::query();

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where('position', 'like', '%' . $this->search . '%')
                ->orWhere('company_name', 'like', '%' . $this->search . '%')
                ->orWhere('location', 'like', '%' . $this->search . '%')
                ->orWhere('setup', 'like', '%' . $this->search . '%')
                ->orWhere('type', 'like', '%' . $this->search . '%')
                ->orWhere('min_salary', 'like', '%' . $this->search . '%')
                ->orWhere('max_salary', 'like', '%' . $this->search . '%');
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.job.posts.index', [
            'records' => $records
        ]);
    }
}
