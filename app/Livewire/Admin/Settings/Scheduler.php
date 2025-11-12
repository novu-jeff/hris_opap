<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Scheduler as SchedulerModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Scheduler extends Component
{

    public array $fields;

    public function mount() {
        $data = SchedulerModel::all();

        foreach($data as $field) {
            $this->fields[$field->schedule_name] = $field->interval;
        }
    }

    protected function rules() {
        return [
            'fields.clear_notification' => 'required|in:1,2,3,10,12,14,24,48,72,730,1460,2190',
            'fields.change_password' => 'required|in:730,1460,2190,2920,3650,4380,52560',
            'fields.reset_leave_credits' => 'required|in:730,52560'
        ];
    }

    protected function messages() {
        return [
                'fields.clear_notification.required' => 'The notification reset time is required.',
                'fields.clear_notification.in' => 'The selected value is invalid. Please select a valid option.',
                
                'fields.change_password.required' => 'The password change schedule is required.',
                'fields.change_password.in' => 'The selected value is invalid. Please select a valid option.',
                
                'fields.reset_leave_credits.required' => 'The leave credits reset schedule is required.',
                'fields.reset_leave_credits.in' => 'The selected value is invalid. Please select a valid option.',
        ];
        
    }

    public function save() {
        
        if (Gate::denies('write scheduler')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {

            foreach($this->fields as $field => $value) {
                $record = SchedulerModel::updateOrCreate([
                    'schedule_name' => $field,
                ], [
                    'schedule_name' => $field,
                    'interval' => $value,
                ]);

                if(is_null($record->latest_activity)) {
                    $record->latest_activity = Carbon::now();
                    $record->save();
                }

            }
            

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Scheduler was updated successfully.'
            ]);

            
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.settings.scheduler');
    }
}
