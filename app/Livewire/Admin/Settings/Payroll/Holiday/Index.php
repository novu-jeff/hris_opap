<?php

namespace App\Livewire\Admin\Settings\Payroll\Holiday;

use App\Models\Holiday;
use Livewire\Component;

class Index extends Component
{

    public $selected_id;
    public object $records;
    protected $listeners = ['remove'];

    public function mount() {
        $this->records = Holiday::where('isActive', true)->get();
    }

    public function remove(bool $isNotify = true, int $id = null) {
        logger()->info('Remove method called with ID:', ['id' => $id]);
        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'The action cannot be undone or reverted!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = Holiday::find($this->selected_id);
            
            if($record) {

                $record->update([
                    'isActive' => false
                ]);

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Holiday: ' . strtoupper($record->name) . ' deleted successfully' 
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
        return view('livewire.admin.settings.payroll.holiday.index');
    }
}
