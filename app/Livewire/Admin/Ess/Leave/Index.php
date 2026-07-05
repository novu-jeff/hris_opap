<?php

namespace App\Livewire\Admin\Ess\Leave;

use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Models\EmployeeAccount;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveCard;
use App\Models\EmployeeInformation;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $status;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'disapproved', 'approved', 'revertToPending'];
    public $accepts_autwopay;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

    public $disapproval_remarks = '';

    public function view(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        if(!is_null($this->view_records)) {
            return $this->dispatch('showModal', [
                'modal' => 'showModal',
            ]);
        }
    }

    public function loadRecords(int $id) {
        $view_records = EmployeeLeave::with([
            'dates',
            'employment',
            'employee.personal',
            'employee.section',
            'leave_type',
            'attachments',
        ])
            ->where('id', $id)
            ->first();

        $duration = $view_records->duration ?? 'wholeday'; 
        $daysCovered = count($view_records->dates ?? []);

        if ($duration == 'wholeday') {
            $leaveEquiv = number_format(round($daysCovered * 1, 3), 2);
        } else {
            $leaveEquiv = number_format(round($daysCovered / 2 * 1, 3), 2);
        }

        $view_records->leave_equivalent = $leaveEquiv;
        $this->view_records = $view_records;

    }

    public function disapproved(bool $isNotify = true) {
        
        $this->loadRecords($this->selected_id);

        if ($isNotify) {

            if (blank(trim($this->disapproval_remarks))) {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Remarks Required',
                    'message' => 'Please provide the reason for disapproval.'
                ]);
            }

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to disapprove this leave application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';

            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => 'disapproved'
            ]);

        } else {

            $record = EmployeeLeave::where('id', $this->selected_id)
            ->where('status', 'pending')
            ->first();

            $record->update([
                'status' => 'disapproved',
                'remarks' => $this->disapproval_remarks,
                'action_by_id' => Auth::id(),
            ]);

            $this->reset('disapproval_remarks');

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success',
                'isRemoveRowDT' => true,
                'message' => 'Application has been disapproved'
            ]);

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications(
                'error',
                'Your leave application <strong>#' . format_id($record->id, 6) .
                '</strong> was <strong>DISAPPROVED</strong>.<br><br>
                <strong>Reason:</strong><br>' . e($record->remarks),
                route('employee.leave'),
                'employee'
            ));
        }
    }

    public function approved(bool $isNotify = true) {

        $this->loadRecords($this->selected_id);

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to approve this leave application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'approved';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeLeave::with('dates', 'employment')->where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();

            if(is_null($record)) {
                return redirect()->route('ess.leave');
            }

            // Update leave credits model
            $leaveCreditsModel = LeaveCredits::class;
            $leaveTypeModel = LeaveType::find($record->leave_id);

            $daysCovered = count($record->dates ?? []);

            // if no credits left

            if($record->leave_id == 1 || $record->leave_id == 2 || $record->leave_id == 3) {

                $leaveType = LeaveType::where('id', $record->leave_id)
                    ->first();
                $leaveTypes = strtolower($leaveType->code);

                $leaveTotalCredits = EmployeeLeaveCard::where('employee_no', $record->employee_no)
                    ->where('year', Carbon::now()->year)
                    ->orderBy('year', 'asc')
                    ->get()
                    ->last();

                if($record->leave_id == 1 || $record->leave_id == 2) {
                    $leaveTotalCredits = $leaveTotalCredits ? $leaveTotalCredits->{$leaveTypes . '_bal'} ?? '' : 0;
                } else {
                    $leaveTotalCredits = $leaveTotalCredits ? $leaveTotalCredits->vl_bal ?? '' : 0;
                }

                $duration = $record->duration ?? 'wholeday'; 


                if ($duration == 'wholeday') {
                    $leaveEquiv = number_format(round($daysCovered * 1, 3), 2);
                } else {
                    $leaveEquiv = number_format(round($daysCovered / 2 * 1.0, 3), 2);
                }

                if(empty($leaveTotalCredits)) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'Unfortunately, this employee\'s leave balance is not yet set.'
                    ]);
                }

                if(!$this->accepts_autwopay) {
                    if($leaveTotalCredits == 0 || $leaveEquiv > $leaveTotalCredits) {
                        $this->accepts_autwopay = true;
                        return $this->dispatch('showConfirmation', [
                            'title' => 'Please be Informed',
                            'message' => '
                                Unfortunately, this employee\'s leave credits are insufficient. He/she is requesting '.$daysCovered.' day(s) of leave, but only have '.$leaveTotalCredits.' remaining. This may still proceed, but please note that this will be considered as Absence Without Pay (AUT w/o pay).
                            ',
                            'action' => 'approved'
                        ]);
                    }
                }

            } else {

                $leaveCredits = $leaveCreditsModel::where('leave_type_id', $record->leave_id)
                    ->where('employee_no', $record->employee_no)
                    ->first();

                if(is_null($leaveCredits) || $leaveCredits->credits == 0) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'Unfortunately, this employee have no credits left for <b>' . $leaveTypeModel->name . '</b>.'
                    ]);
                }

                if($daysCovered > $leaveCredits->credits) {
                    return $this->dispatch('alert', [
                        'showAlert' => true,
                        'status' => 'error',
                        'title' => 'Oops',
                        'message' => 'Unfortunately, this employee have insufficient leave credits. Applying for '.$daysCovered.' day(s), but only have ' . $leaveCredits->credits . ' remaining leave credits.'
                    ]);
                }
            }

            $leaveCardService = new LeaveCardService;
            $leaveCardService->init($record->employee_no, 'leave_approval', $record);

            unset($record->daysCovered);

            $record->update([
                'action_by_id' => Auth::user()->id,
                'status' => 'approved'
            ]);

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success',
                'isRemoveRowDT' => true,
                'message' => 'Application has been approved'
            ]);

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications('success', 'Your leave application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>APPROVED</strong>. Click this notification to view more details.', route('employee.leave'), 'employee'));

            return;
        }

    }

    public function approveDisapproved()
    {
        $record = EmployeeLeave::with('dates', 'employment')
            ->where('id', $this->selected_id)
            ->where('status', 'disapproved')
            ->first();

        if (!$record) {
            return;
        }

        // clear disapproval remarks
        $record->remarks = null;

        // change status
        $record->status = 'approved';
        $record->action_by_id = Auth::id();
        $record->save();

        // deduct leave credits
        $leaveCardService = new LeaveCardService;
        $leaveCardService->init($record->employee_no, 'leave_approval', $record);

        $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'success',
            'title' => 'Success',
            'message' => 'Leave application has been approved.'
        ]);

        $this->loadRecords($record->id);

        $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();

        $user?->notify(new Notifications(
            'success',
            'Your leave application <strong>#'.format_id($record->id,6).'</strong> has been <strong>APPROVED</strong>.',
            route('employee.leave'),
            'employee'
        ));
    }

    

    public function revertToPending(bool $isNotify = true)
    {
        if ($isNotify) {

            $this->dispatch('showConfirmation', [
                'title'   => 'Revert Application?',
                'message' => 'This leave application will be returned to Pending for re-evaluation.',
                'action'  => 'revertToPending'
            ]);

            return;
        }

        $record = EmployeeLeave::with('dates')->find($this->selected_id);

        if (!$record) {
            return;
        }

        // Only process approved applications
        if ($record->status === 'approved') {

            $leaveType = LeaveType::find($record->leave_id);

            // VL / SL require Leave Card reversal
            if ($leaveType && $leaveType->isCummulative) {

                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status'    => 'warning',
                    'title'     => 'Not Allowed',
                    'message'   => 'Approved Vacation Leave and Sick Leave cannot yet be reverted because the Leave Card has already been updated.'
                ]);
            }

            // Restore credits for non-cumulative leave types (WL, ML, PL, etc.)
            $leaveCredit = LeaveCredits::where('employee_no', $record->employee_no)
                ->where('leave_type_id', $record->leave_id)
                ->first();

            if ($leaveCredit) {

                $daysCovered = $record->dates->count();

                $leaveEquivalent = $record->duration === 'wholeday'
                    ? $daysCovered
                    : ($daysCovered / 2);

                $leaveCredit->credits += $leaveEquivalent;
                $leaveCredit->as_of = now()->format('Y-m');
                $leaveCredit->save();
            }
        }

        $record->update([
            'status'       => 'pending',
            'remarks'      => null,
            'action_by_id' => Auth::id(),
        ]);

        $this->dispatch('alert', [
            'showAlert' => true,
            'status'    => 'success',
            'title'     => 'Success',
            'message'   => 'Leave application has been reverted to Pending.'
        ]);

        $this->loadRecords($record->id);
    }

    public function render()
    {

        if($this->status == 'granted') {
            $status = 'approved';
        } else {
            $status = $this->status;
        }

        $model = EmployeeLeave::with([
            'employment',
            'employee.personal',
            'employee.section',
            'leave_type',
            'dates',
        ])
            ->where('status', $status)
            ->where('isDeleted', false);

        if ($this->search) {

            $this->resetPage();

            $records = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('employee', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.ess.leave.index', [
            'records' => $records
        ]);
    }
}
