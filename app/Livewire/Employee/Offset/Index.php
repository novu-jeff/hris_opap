<?php

namespace App\Livewire\Employee\Offset;

use App\Models\EmployeeOffsetRequest;
use App\Models\EmployeeOffsetCredit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'remove',
        'cancel'
    ];

    public $selected_id;
    public $user_id;

    public $entries = 10;
    public $status = 'all';

    public $earnedHours = 0;
    public $usedHours = 0;
    public $remainingHours = 0;

    public function mount()
    {
        $this->user_id = Auth::user()->employee_no;

        if (!$this->user_id) {
            return redirect()->route('employee.offset.index');
        }

        $this->loadCredits();
    }

    public function loadCredits()
    {
        $credits = EmployeeOffsetCredit::where('employee_no', $this->user_id);

        $this->earnedHours = $credits->sum('earned_hours');
        $this->usedHours = $credits->sum('used_hours');
        $this->remainingHours = $this->earnedHours - $this->usedHours;
    }

    public function remove(bool $isNotify = true, ?int $id = null)
    {
        if ($isNotify) {

            $this->selected_id = $id;

            $this->dispatch('showConfirmation', [
                'title' => 'Delete Application?',
                'message' => 'You are about to permanently delete Offset Application <b>#'
                    . strtoupper(format_id($id, 6))
                    . '</b>.',
                'action' => 'remove'
            ]);

            return;
        }

        $record = EmployeeOffsetRequest::find($this->selected_id);

        if (!$record) {

            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Record not found.'
            ]);

        }

        $record->isDeleted = true;
        $record->save();

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Success',
            'message' => 'Offset application deleted successfully.'
        ]);
    }

    public function cancel(bool $isNotify = true, ?int $id = null)
    {
        if ($isNotify) {

            $this->selected_id = $id;

            $this->dispatch('showConfirmation', [
                'title' => 'Cancel Application?',
                'message' => 'You are about to cancel Offset Application <b>#'
                    . strtoupper(format_id($id,6))
                    . '</b>.',
                'action' => 'cancel'
            ]);

            return;
        }

        $record = EmployeeOffsetRequest::find($this->selected_id);

        if (!$record) {

            return $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Record not found.'
            ]);

        }

        $record->status = 'cancelled';
        $record->save();

        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Success',
            'message' => 'Offset application cancelled successfully.'
        ]);
    }

    public function download($id)
    {
        //
        // PDF / DOCX generation
        // Implement after Offset Form is finalized.
    }

    public function render()
    {
        $query = EmployeeOffsetRequest::query()
            ->where('employee_no', $this->user_id)
            ->where('isDeleted', false);

        if ($this->status != 'all') {

            $status = $this->status == 'granted'
                ? 'approved'
                : $this->status;

            $query->where('status', $status);
        }

        return view('livewire.employee.offset.index', [

            'records' => $query
                ->latest()
                ->paginate($this->entries),

            'earnedHours' => $this->earnedHours,

            'usedHours' => $this->usedHours,

            'remainingHours' => $this->remainingHours,

        ]);
    }
}