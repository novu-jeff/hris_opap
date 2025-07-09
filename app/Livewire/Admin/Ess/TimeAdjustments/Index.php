<?php

namespace App\Livewire\Admin\Ess\TimeAdjustments;

use App\Models\EmployeeAccount;
use App\Models\EmployeeClockInOut;
use App\Models\EmployeeTimeAdjustments;
use App\Models\EmployeeTimelogs;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
 
    use WithPagination;

    public $status;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'disapproved', 'approved'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';

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
        $this->view_records = EmployeeTimeAdjustments::with('personal', 'attachments')
            ->where('id', $id)
            ->first();
    }

    public function disapproved(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to disapprove this request timelog application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'disapproved';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeTimeAdjustments::where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();

            $record->status = 'disapproved';
            $record->action_by_id = Auth::user()->id;
            $record->save();

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success', 
                'isRemoveRowDT' => true,
                'message' => 'Application has been disapproved'
            ]);


            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications('error', 'You\'re request timelog application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>DISAPPROVED</strong>.', route('employee.leave'), 'employee'));
        }
    }

    public function approved(bool $isNotify = true) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to approve this request timelog application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'approved';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            $record = EmployeeTimeAdjustments::with('employee')->where('id', $this->selected_id)
                ->where('status', 'pending')
                ->first();
                
            if(is_null($record)) {
                return redirect()->route('ess.time-adjustments.index');
            }

            $record->action_by_id = Auth::user()->id;

            $clock_in_am = Carbon::parse($record->clock_in)->format('H:i:s');
            $clock_out_am = Carbon::parse($record->break_out)->format('H:i:s');
            $clock_in_pm = Carbon::parse($record->break_in)->format('H:i:s');
            $clock_out_pm = Carbon::parse($record->clock_out)->format('H:i:s');
            $date = Carbon::parse($record->date)->format('Y-m-d');

            $rawTimestamps = [
                'clock_in' => [
                    'timestamp' => $clock_in_am,
                    'type' => 0,
                ],
                'lunch_in' => [
                    'timestamp' => $clock_out_am,
                    'type' => 1,
                ],
                'lunch_out' => [
                    'timestamp' => $clock_in_pm,
                    'type' => 0,
                ],
                'clock_out' => [
                    'timestamp' => $clock_out_pm,
                    'type' => 1,
                ]
            ];
            
            $logs = collect($rawTimestamps)->map(function ($time) use ($date, $record) {
                $timestamp = "$date {$time['timestamp']}";
                return [
                    'isWeb' => true,
                    'sn' => 'RUU5242500021',
                    'table' => 'ATTLOG',
                    'stamp' => '9999',
                    'employee_id' => $record->employee->bsd_no,
                    'timestamp' => $timestamp,
                    'status1' => $time['type'],
                ];
            })->sortBy('timestamp')->values()->all();
            
            $existingLogs = EmployeeTimelogs::where('employee_id', $record->employee->bsd_no)
                ->where('timestamp', 'LIKE', "{$date}%")
                ->orderBy('timestamp', 'asc')
                ->get();
            
            foreach ($logs as $key => $log) {
                if (isset($existingLogs[$key])) {
                    $existingLogs[$key]->timestamp = $log['timestamp'];
                    $existingLogs[$key]->captured_image = '';
                    $existingLogs[$key]->captured_location = '';
                    $existingLogs[$key]->save();
                } else {
                    EmployeeTimelogs::create($log);
                }
            }            
            
            $record->update([
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
            $user?->notify(new Notifications('success', 'You\'re request timelog application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>APPROVED</strong>. Click this notification to view more details.', route('employee.time-adjustments'), 'employee'));

            return;

        }
    }

    public function remove(bool $isNotify = true, int $id = null) {

        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to delete this request timelog application <b>#' . strtoupper(format_id($id, 6)) . '</b>. Once this action is processed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = EmployeeTimeAdjustments::find($this->selected_id);
                
            if($record) {
                
                $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
                $user?->notify(new Notifications('error', 'You\'re request timelog application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>REMOVED</strong>. Click this notification to view more details.', route('employee.leave'), 'employee'));

                $record->isDeleted = true;
                $record->action_by_id = Auth::user()->id;
                $record->save();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'Request Tiemlog Application #' . strtoupper(format_id($record->id, 6)) . ' has deleted successfully.' 
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
       
        if($this->status == 'granted') {
            $status = 'approved';
        } else {
            $status = $this->status;
        }

        $model = EmployeeTimeAdjustments::with('attachments', 'employee')
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

        return view('livewire.admin.ess.time-adjustments.index', [
            'records' => $records
        ]);
    }
}