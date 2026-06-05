<?php

namespace App\Livewire\Admin\Ess\TimeAdjustments;

use App\Models\EmployeeAccount;
use App\Models\EmployeeTimelogs;
use App\Models\EmployeeTimeAdjustments;
use App\Notifications\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


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

    public function approved(bool $isNotify = true)
{
    if ($isNotify) {

        $this->dispatch('showConfirmation', [
            'title' => 'Are you sure to continue?',
            'message' => 'Please be informed that you are about to approve this request timelog application <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>.',
            'action' => 'approved'
        ]);

        return;
    }

    $record = EmployeeTimeAdjustments::with('employee.personal')
        ->where('id', $this->selected_id)
        ->where('status', 'pending')
        ->first();

    if (!$record) {
        return redirect()->route('ess.time-adjustments.index');
    }

    $employeeNo = $record->employee->employee_no;
    $date = Carbon::parse($record->date)->format('Y-m-d');

    // ✅ Normalize requested timestamps
    // ================================
// STEP 1: Prepare logs
// ================================
$newLogs = collect([
    ['time' => $record->clock_in,  'type' => 0],
    ['time' => $record->break_out, 'type' => 1],
    ['time' => $record->break_in,  'type' => 0],
    ['time' => $record->clock_out, 'type' => 1],
])
->filter(fn ($t) => !empty($t['time']))
->map(function ($t) use ($date) {
    return [
        'timestamp' => Carbon::parse($t['time'])->format("{$date} H:i:s"),
        'type' => $t['type'],
    ];
})
->sortBy('timestamp')
->values();

$deleted = DB::connection('mysql')
->table('timelogs')
->where('employee_id', $employeeNo)
->whereDate('timestamp', $date)
->delete();

Log::debug('deleted_rows', ['count' => $deleted]);


// ================================
// STEP 2: Resolve attendance IDs
// ================================
$attendanceIds = array_filter([
    EmployeeTimelogs::getBsdNo($employeeNo),
    $employeeNo
], fn ($v) => is_numeric($v));


// ================================
// STEP 3: Check if attendance exists (WHOLE DAY)
// ================================
$attendanceExists = false;
$existingAttendanceLogs = collect();

if (!empty($attendanceIds)) {

    $existingAttendanceLogs = DB::connection('mysql2')
        ->table('attendances')
        ->whereIn('employee_id', $attendanceIds)
        ->whereDate('timestamp', $date)
        ->orderBy('timestamp')
        ->get()
        ->values();

    $attendanceExists = $existingAttendanceLogs->isNotEmpty();
}


// ================================
// ✅ CASE 1: ATTENDANCE EXISTS
// ================================
if ($attendanceExists) {

    $deleted = DB::connection('mysql2')
->table('attendances')
->where('employee_id', $attendanceIds[0])
->whereDate('timestamp', $date)
->delete();

Log::debug('deleted_attendance rows', ['count' => $deleted, 'employee_no' => $attendanceIds[0]]);

    foreach ($newLogs as $index => $newLog) {

        $newTime = Carbon::parse($newLog['timestamp']);

        // Try sequence match first
        //$existing = $existingAttendanceLogs[$index] ?? null;

        // fallback: closest match + same type
       // if (!$existing) {
           /* $existing = $existingAttendanceLogs->first(function ($log) use ($newTime, $newLog) {
                return $log->status1 == $newLog['type'] &&
                    abs(Carbon::parse($log->timestamp)->diffInMinutes($newTime)) <= 120;
            });*/
      //  }


            Log::debug('insert', [
                'id' => $attendanceIds[0],
                'timestamp' => $newLog['timestamp'],
            ]);
            DB::connection('mysql2')
                ->table('attendances')
                ->insert([
                    'employee_id' => $attendanceIds[0],
                    'sn' => 'RUU5242500021',
                    'table' => 'ATTLOG',
                    'stamp' => '9999',
                    'timestamp' => $newLog['timestamp'],
                    'status1' => $newLog['type'],
                    'isWeb' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
       
    }

    // 🚫 IMPORTANT: STOP HERE (no timelogs)
   // return;
}else{


    // ================================
    // ❌ CASE 2: NO ATTENDANCE → USE TIMELOGS
    // ================================
    foreach ($newLogs as $newLog) {

        $newTime = Carbon::parse($newLog['timestamp']);

        /*$existingTimelog = DB::connection('mysql')
            ->table('timelogs')
            ->where('employee_id', $employeeNo)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp')
            ->get()
            ->first(function ($log) use ($newTime, $newLog) {
                return abs(Carbon::parse($log->timestamp)->diffInMinutes($newTime)) <= 120;
            });*/
           

            /*$existingTimelog = DB::connection('mysql')
            ->table('timelogs')
            ->where('employee_id', $employeeNo)
            ->where('timestamp', $newLog['timestamp'])
            ->first(); */
            
           
            // ➕ CREATE
            Log::debug('insert', [
                'id' => $employeeNo,
                'timestamp' => $newLog['timestamp'],
            ]);
            DB::connection('mysql')
                ->table('timelogs')
                ->insert([
                    'employee_id' => $employeeNo,
                    'timestamp' => $newLog['timestamp'],
                    'status' => $newLog['type'],
                    'isWeb' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
       
    }
}
Log::debug('computeDailyOvertime', [
    'employeeNo' => $employeeNo,
    'date' => $date,
]);
app(\App\Services\ClockInOutService::class)
->computeDailyOvertime(
    $employeeNo,
    $date . ' 23:59:59'
);

    // ✅ Finalize approval
    $record->update([
        'status' => 'approved',
    ]);

   

    // ✅ Notify
    $user = EmployeeAccount::where('employee_no', $employeeNo)->first();
    $user?->notify(new Notifications(
        'success',
        'You\'re request timelog application <strong>#' . format_id($record->id, 6) . '</strong> was <strong>APPROVED</strong>.',
        route('employee.time-adjustments'),
        'employee'
    ));

    // ✅ UI feedback
    $this->dispatch('alert', [
        'id' => $this->selected_id,
        'showAlert' => true,
        'status' => 'success',
        'title' => 'Success',
        'isRemoveRowDT' => true,
        'message' => 'Application has been approved'
    ]);
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

        $model = EmployeeTimeAdjustments::with([
            'attachments',
            'employee.personal'
        ])
            ->where('status', $status)
            ->where('isDeleted', false);

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('employee.personal', function ($q) {
                    $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.ess.time-adjustments.index', [
            'records' => $records
        ]);
    }
}