<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Models\EmployeeLeaveCard;
use App\Models\LeaveType;
use App\Models\Tranche;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    protected $listeners = ['remove']; 

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public $showModal = false;

    public $code;
    public $name;
    public $credits = 0;
    public $isCummulative = false;

    public $editingId = null;

    // public function remove(bool $isNotify = true, int $id = null) {

    //     if (Gate::denies('write leave-types')) {
    //         $this->dispatch('alert', [
    //             'status' => 'error',
    //             'title' => 'Access Denied!', 
    //             'showAlert' => true,
    //             'message' => 'You do not have permission to perform this action.',
    //         ]);
    //         return;
    //     }

    //     if($isNotify) {

    //         $title = 'Are you sure to continue?';
    //         $message = 'Please be informed that you are about to delete this type of leave. Once this action is completed, it cannot be undone or reversed!';
    //         $action = 'remove';

    //         $this->selected_id = $id;
    //         $this->dispatch('showConfirmation', [
    //             'title' => $title,
    //             'message' => $message,
    //             'action' => $action
    //         ]);

    //     }  else {

    //         $record = LeaveType::find($this->selected_id);
                
    //         if($record) {
                
    //             $record->delete();

    //             $this->dispatch('alert', [
    //                 'status' => 'success',
    //                 'title' => 'Success!', 
    //                 'id' => $this->selected_id,
    //                 'isRemoveRowDT' => true,
    //                 'message' => 'Leave type ' . strtoupper($record->name) . ' deleted successfully' 
    //             ]);
    //         } else {
    //             return $this->dispatch('alert', [
    //                 'showAlert' => true,
    //                 'status' => 'error',
    //                 'title' => 'Oops!', 
    //                 'isRemoveRowDT' => false,
    //                 'message' => 'Error: ID does not exists' 
    //             ]);
    //         }
    //     }
    // }

    public function openModal()
    {
        $this->resetForm();
    
        $this->showModal = true;
    }

    protected function rules()
    {
        return [
            'code' => 'required|max:20|unique:leave_types,code,' . $this->editingId,
            'name' => 'required|max:100',
        ];
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
            'code',
            'name',
            'credits',
            'isCummulative',
        ]);

        $this->credits = 0;
        $this->isCummulative = false;

        $this->resetValidation();
    }

    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $this->editingId = $leaveType->id;
        $this->code = $leaveType->code;
        $this->name = $leaveType->name;
        $this->credits = $leaveType->credits;
        $this->isCummulative = $leaveType->isCummulative;

        $this->resetValidation();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {

            $leaveType = LeaveType::findOrFail($this->editingId);

            $leaveType->update([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'credits' => $this->credits,
                'isCummulative' => $this->isCummulative,
            ]);

            $message = 'Leave type updated successfully.';

        } else {

            LeaveType::create([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'credits' => $this->credits,
                'isCummulative' => $this->isCummulative,
            ]);

            $message = 'Leave type added successfully.';
        }

        $this->showModal = false;

        $this->resetForm();

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Success!',
            'message' => $message,
        ]);
    }

    public function render()
    {
        $records = LeaveType::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->entries);

        return view('livewire.admin.settings.hris.leave.index', [
            'records' => $records,
        ]);
    }
}
