<?php

namespace App\Providers;

use App\Observers\ModelActivityObserver;
use App\Services\DailyTimeRecordService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Temporary queue lockdown: only allow payroll jobs.
     */
    private const ALLOWED_QUEUE_JOB_CLASSES = [
        'App\Jobs\PayrollJob',
        'App\Jobs\EmployeeUpload',
        'App\Jobs\ChangeEmployeeNoJob',
        'App\Jobs\GeneratePayslipsJob',
        'App\Jobs\GenerateSinglePayslipJob',
        'App\Jobs\TimelogUploadProcess',
    ];

    /**
     * Block obvious payload indicators of queue poisoning.
     */
    private const BLOCKED_QUEUE_PAYLOAD_MARKERS = [
        'batosay1337',
        '$_env',
        'getenv(',
        'php_uname',
        'shell_exec(',
        'passthru(',
        'system(',
        'printenv',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DailyTimeRecordService::class, function ($app) {
            return new DailyTimeRecordService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::before(function (JobProcessing $event): void {
            $payload = $event->job->payload();
            $jobClass = $payload['data']['commandName'] ?? $payload['displayName'] ?? 'unknown';
            $rawPayload = json_encode($payload);

            $hasBlockedMarker = false;
            if (is_string($rawPayload) && $rawPayload !== '') {
                $lowerPayload = strtolower($rawPayload);
                foreach (self::BLOCKED_QUEUE_PAYLOAD_MARKERS as $marker) {
                    if (str_contains($lowerPayload, $marker)) {
                        $hasBlockedMarker = true;
                        break;
                    }
                }
            }

            $isAllowed = in_array($jobClass, self::ALLOWED_QUEUE_JOB_CLASSES, true);
            if ($isAllowed && !$hasBlockedMarker) {
                return;
            }

            Log::warning('Blocked non-payroll queued job during temporary lockdown.', [
                'queue_job_class' => $jobClass,
                'queue_job_id' => method_exists($event->job, 'getJobId') ? $event->job->getJobId() : null,
            ]);

            $event->job->delete();
            throw new RuntimeException('Blocked queued job by security lockdown.');
        });

        view()->share('product', config('app.product'));
        $provider = env('APP_PROVIDER', 'novulutions');
        view()->share('provider', config('meta')[$provider] ?? config('meta')['novulutions']);

        
        $except = [
            'EmployeeTimelogs'
        ];

        $modelsPath = app_path('Models');
        if (File::exists($modelsPath)) {
            foreach (File::files($modelsPath) as $file) {
                $modelName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                if (in_array($modelName, $except)) {
                    continue;
                }

                $model = 'App\\Models\\' . $modelName;
                if (class_exists($model) && is_subclass_of($model, Model::class)) {
                    $model::observe(ModelActivityObserver::class);
                }
            }
        }
    }
}
