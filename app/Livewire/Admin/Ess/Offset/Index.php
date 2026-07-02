<?php

namespace App\Livewire\Admin\Ess\Offset;

use App\Models\EmployeeAccount;
use App\Models\EmployeeOffsetCredit;
use App\Models\EmployeeOffsetRequest;
use App\Models\EmployeeOffsetCreditUsage;
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

    public $isEdit = false;

    public $remarks = '';

    protected $listeners = [
        'approved',
        'disapproved',
        'changeToDisapproved',
        'remove',
    ];

    public function view($id, $edit = false)
    {
        $this->selected_id = $id;
        $this->isEdit = $edit;

        $this->view_records = EmployeeOffsetRequest::with([
            'attachments',
            'employee.personal',
        ])->findOrFail($id);

        $this->remarks = $this->view_records->remarks;

        $this->dispatch('showModal', [
            'modal' => 'showModal'
        ]);
    }

    public function changeToDisapproved($notify = true)
    {
        if ($notify) {
    
            $this->dispatch('showConfirmation', [
                'title'   => 'Change to Disapproved?',
                'message' => 'This will restore the employee\'s offset credits.',
                'action'  => 'changeToDisapproved'
            ]);
    
            return;
        }

        // ADD HERE
        $this->validate([
            'remarks' => 'required|string|max:1000',
        ]);
    
        $record = EmployeeOffsetRequest::findOrFail($this->selected_id);
    
        if ($record->status != 'approved') {
            return;
        }
    
        $usages = EmployeeOffsetCreditUsage::where(
            'employee_offset_request_id',
            $record->id
        )->get();
    
        foreach ($usages as $usage) {
    
            $credit = EmployeeOffsetCredit::find($usage->employee_offset_credit_id);
    
            if (!$credit) {
                continue;
            }
    
            $credit->used_hours -= $usage->hours_used;
            $credit->remaining_hours += $usage->hours_used;
            $credit->save();
    
            $usage->delete();
        }
    
        $record->status = 'disapproved';
        $record->office_order_no = null;
        $record->remarks = $this->remarks;
        $record->save();
    
        EmployeeAccount::where('employee_no', $record->employee_no)
            ->first()?->notify(
                new Notifications(
                    'warning',
                    'Your approved Offset application has been changed to DISAPPROVED.',
                    route('employee.offset.index'),
                    'employee'
                )
            );
    
        $this->dispatch('alert', [
            'showAlert' => true,
            'status'    => 'success',
            'title'     => 'Success',
            'message'   => 'Offset application changed to disapproved.',
            'redirect'  => '_reload'
        ]);
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
            ->whereIn('status', ['pending', 'disapproved'])
            ->first();

        if(!$record){
            return;
        }

        if (empty($record->office_order_no)) {
            $record->office_order_no = $this->generateOfficeOrderNo();
        }
        $record->status = 'approved';
        $record->remarks = null;
        $record->save();
       
        // Deduct credits only if they haven't already been deducted
        if (
            EmployeeOffsetCreditUsage::where(
                'employee_offset_request_id',
                $record->id
            )->doesntExist()
        ) {

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

                // Record which credit was consumed
                EmployeeOffsetCreditUsage::create([
                    'employee_offset_request_id' => $record->id,
                    'employee_offset_credit_id'  => $credit->id,
                    'hours_used'                 => $deduct,
                ]);

                $remaining -= $deduct;
            }
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
    private function generateOfficeOrderNo()
    {
        $year = now()->year;

        $last = EmployeeOffsetRequest::whereYear('created_at', $year)
            ->whereNotNull('office_order_no')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {

            preg_match('/(\d+)$/', $last->office_order_no, $matches);

            $next = isset($matches[1])
                ? ((int) $matches[1]) + 1
                : 1;

        }

        return sprintf('ATRO-%s-%06d', $year, $next);
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

        // ADD HERE
        $this->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $record = EmployeeOffsetRequest::where('id',$this->selected_id)
            ->where('status','pending')
            ->first();

        if(!$record){
            return;
        }

        $record->status='disapproved';
        $record->remarks = $this->remarks;
        $record->save();

        EmployeeAccount::where('employee_no',$record->employee_no)
            ->first()?->notify(
                new Notifications(
                    'error',
                    'Your Offset application <strong>#'
                    . format_id($record->id, 6)
                    . '</strong> has been <strong>DISAPPROVED</strong>.<br><br>
                    Reason: <strong>'
                    . e($this->remarks)
                    . '</strong>',
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