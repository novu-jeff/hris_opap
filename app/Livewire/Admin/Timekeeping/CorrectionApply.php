<?php

namespace App\Livewire\Admin\Timekeeping;

use App\Models\EmployeeClockInOut;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CorrectionApply extends Component
{

    public $id;
    public $clock_in_am;
    public $clock_out_am;
    public $clock_in_pm;
    public $clock_out_pm;

    protected $listeners = ['save'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        if(is_null($this->id)) {
            return redirect()->route('timekeeping.index');
        }

        $records = EmployeeClockInOut::where('id', $this->id)
            ->first();

        $this->clock_in_am = $records->clock_in_am ? Carbon::parse($records->clock_in_am)->format('H:i') : null;
        $this->clock_out_am = $records->clock_out_am ? Carbon::parse($records->clock_out_am)->format('H:i') : null;
        $this->clock_in_pm = $records->clock_in_pm ? Carbon::parse($records->clock_in_pm)->format('H:i') : null;
        $this->clock_out_pm = $records->clock_out_pm ? Carbon::parse($records->clock_out_pm)->format('H:i') : null;

    }

    protected function rules() {
        return [
            'clock_in_am' => 'required',
            'clock_out_am' => 'required',
            'clock_in_pm' => 'required',
            'clock_out_pm' => 'required',
        ];
    }

    protected function messages()
{
    return [
        'clock_out_am.required' => 'The Break In time is required.',
        'clock_in_pm.required' => 'The Break Out time is required.',
        'clock_in_am.required' => 'The Clock In AM time is required.',
        'clock_out_pm.required' => 'The Clock Out PM time is required.',
    ];
}

    public function save(bool $isNotify = true) {

        $this->validate();

        if ($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'The action cannot be undone or reverted!',
                'action' => 'save'
            ]);
        } else {


            DB::beginTransaction();

            try {


                EmployeeClockInOut::where('id', $this->id)
                    ->update([
                        'clock_in_am' => $this->clock_in_am,
                        'clock_out_am' => $this->clock_out_am,
                        'clock_in_pm' => $this->clock_in_am,
                        'clock_out_pm' => $this->clock_out_pm
                    ]);

                DB::commit();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'showAlert' => true,
                    'message' => 'Correction has been applied to clock log ID #' . $this->id . ''
                ]);

            } catch (\Exception $e) {

                DB::rollBack();

                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.timekeeping.correction-apply');
    }
}
