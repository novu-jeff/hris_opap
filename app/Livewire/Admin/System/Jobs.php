<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Jobs extends Component
{
    use WithPagination;

    public $batch_id;

    protected $paginationTheme = 'bootstrap';

    public function updatingBatchId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $batches = DB::table('job_batches')
            ->when(!empty($this->batch_id), fn($query) => $query->where('id', $this->batch_id))
            ->orderByDesc('created_at')
            ->paginate(6) 
            ->through(function ($batch) {
                $options = @unserialize($batch->options);
                $batch->action_by = is_array($options) && isset($options['actionBy']['name'])
                    ? $options['actionBy']['name']
                    : 'N/A';
                $batch->created_at = Carbon::parse($batch->created_at)->toDayDateTimeString();
                $batch->finished_at = $batch->finished_at ? Carbon::parse($batch->finished_at)->toDayDateTimeString() : null;
                $batch->has_failures = $batch->failed_jobs > 0;
                return $batch;
            });

        return view('livewire.admin.system.jobs', [
            'batches' => $batches
        ]);
    }
}
