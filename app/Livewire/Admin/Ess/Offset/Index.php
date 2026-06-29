<?php

namespace App\Livewire\Admin\Ess\Offset;

use App\Models\EmployeeAccount;
use App\Models\EmployeeOffsetCredit;
use App\Models\EmployeeOffsetRequest;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $status = 'pending';
    public $view_records;
    public $selected_id;
    public $entries = 10;
    public $search = '';

    protected $listeners = [
        'approved',
        'disapproved',
        'remove'
    ];

    public function view($id)
    {
        $this->selected_id = $id;

        $this->view_records = EmployeeOffsetRequest::with([
            'attachments',
            'employee.personal',
        ])->find($id);

        if ($this->view_records) {

            $this->dispatch('showModal', [
                'modal' => 'showModal'
            ]);

        }
    }

    public function approved($notify = true)
    {
        if ($notify) {

            $this->dispatch('showConfirmation',[
                'title'=>'Approve Application?',
                'message'=>'Are you sure you want to approve this Offset application?',
                'action'=>'approved'
            ]);

            return;
        }

        $record = EmployeeOffsetRequest::where('id',$this->selected_id)
            ->where('status','pending')
            ->first();

        if(!$record){
            return;
        }

        $record->status='approved';
       // $record->action_by_id=Auth::id();
        $record->save();

        // FIFO deduction
        $remaining = $record->hours_requested;

        $credits = EmployeeOffsetCredit::where('employee_no',$record->employee_no)
            ->where('remaining_hours','>',0)
            ->orderBy('earned_date')
            ->get();

        foreach($credits as $credit){

            if($remaining<=0){
                break;
            }

            $deduct=min($credit->remaining_hours,$remaining);

            $credit->used_hours += $deduct;
            $credit->remaining_hours -= $deduct;
            $credit->save();

            $remaining -= $deduct;
        }

        $user = EmployeeAccount::where('employee_no',$record->employee_no)->first();

        $user?->notify(
            new Notifications(
                'success',
                'Your Offset application <strong>#'.format_id($record->id,6).'</strong> has been <strong>APPROVED</strong>.',
                route('employee.offset.index'),
                'employee'
            )
        );

        $this->dispatch('alert',[
            'showAlert'=>true,
            'status'=>'success',
            'title'=>'Success',
            'message'=>'Offset application approved.'
        ]);
    }

    public function disapproved($notify=true)
    {
        if($notify){

            $this->dispatch('showConfirmation',[
                'title'=>'Disapprove Application?',
                'message'=>'Are you sure?',
                'action'=>'disapproved'
            ]);

            return;
        }

        $record = EmployeeOffsetRequest::where('id',$this->selected_id)
            ->where('status','pending')
            ->first();

        if(!$record){
            return;
        }

        $record->status='disapproved';
       // $record->action_by_id=Auth::id();
        $record->save();

        EmployeeAccount::where('employee_no',$record->employee_no)
            ->first()?->notify(
                new Notifications(
                    'error',
                    'Your Offset application <strong>#'.format_id($record->id,6).'</strong> has been <strong>DISAPPROVED</strong>.',
                    route('employee.offset.index'),
                    'employee'
                )
            );

        $this->dispatch('alert',[
            'showAlert'=>true,
            'status'=>'success',
            'title'=>'Success',
            'message'=>'Offset application disapproved.'
        ]);
    }

    public function remove($notify=true,$id=null)
    {
        if($notify){

            $this->selected_id=$id;

            $this->dispatch('showConfirmation',[
                'title'=>'Remove Application?',
                'message'=>'Are you sure?',
                'action'=>'remove'
            ]);

            return;
        }

        $record = EmployeeOffsetRequest::find($this->selected_id);

        if(!$record){
            return;
        }

        $record->isDeleted=true;
      //  $record->action_by_id=Auth::id();
        $record->save();

        EmployeeAccount::where('employee_no',$record->employee_no)
            ->first()?->notify(
                new Notifications(
                    'error',
                    'Your Offset application has been removed.',
                    route('employee.offset.index'),
                    'employee'
                )
            );

        $this->dispatch('alert',[
            'showAlert'=>true,
            'status'=>'success',
            'title'=>'Success',
            'message'=>'Offset application removed.'
        ]);
    }

    public function render()
    {
       
        $status = $this->status == 'granted'
            ? 'approved'
            : $this->status;

        $query = EmployeeOffsetRequest::with([
            'employee.personal'
        ])->where('status', $status);

        if($status!='all'){
            $query->where('status',$status);
        }

        if($this->search){

            $query->where(function($q){

                $q->where('employee_no','like',"%{$this->search}%")
                  ->orWhereHas('employee.personal',function($sub){

                        $sub->whereRaw(
                            "concat(firstname,' ',lastname) like ?",
                            ["%{$this->search}%"]
                        );

                  });

            });

        }

        return view('livewire.admin.ess.offset.index',[
            'records'=>$query
                ->latest()
                ->paginate($this->entries)
        ]);
    }
}