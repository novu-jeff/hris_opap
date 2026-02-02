<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ModelActivityObserver
{
    /**
     * Handle the created event.
     */
    public function created(Model $model)
    {
        $this->logChange($model, 'created');
    }

    /**
     * Handle the updated event.
     */
    public function updated(Model $model)
    {
        $this->logChange($model, 'updated');
    }

    /**
     * Handle the deleted event.
     */
    public function deleted(Model $model)
    {
        $this->logChange($model, 'deleted');
    }

    /**
     * Log changes to storage/logs/trails/
     */
    protected function logChange(Model $model, string $action)
    {
        // Skip trail logging when running in console (e.g. queue worker) to avoid permission issues
        if (app()->runningInConsole()) {
            return;
        }

        $baseDirectory = storage_path('logs/trails/');
        $path = request()->path();

        if (str_contains($path, '/employee/')) {
            $directory = $baseDirectory . 'employee/';
        } elseif (str_contains($path, '/admin/')) {
            $directory = $baseDirectory . 'admin/';
        } else {
            $directory = $baseDirectory;
        }

        if (!File::exists($directory)) {
            try {
                File::makeDirectory($directory, 0775, true, true);
            } catch (\Throwable $e) {
                \Log::warning('ModelActivityObserver: could not create trails directory', [
                    'directory' => $directory,
                    'message' => $e->getMessage(),
                ]);
                return;
            }
        }

        $filename = now()->format('m-d-y') . '.log';
        $user = Auth::user() ?? null;

        if ($user) {
            $actioned_by = $user->id . ' - ' . $user->name;
        } else {
            $actioned_by = 'System';
        }

        $actioned_by = '[' . $actioned_by . ']';

        $logEntry = sprintf(
            "[%s] %s %s by %s: %s\n",
            now()->toDateTimeString(),
            get_class($model),
            strtoupper($action),
            $actioned_by,
            json_encode($model->getAttributes(), JSON_PRETTY_PRINT)
        );

        try {
            File::append($directory . $filename, $logEntry);
        } catch (\Throwable $e) {
            \Log::warning('ModelActivityObserver: could not write trail log', [
                'path' => $directory . $filename,
                'message' => $e->getMessage(),
            ]);
        }
    }
}