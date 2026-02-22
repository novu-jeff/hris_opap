<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PurgeUntrustedQueueJobs extends Command
{
    /**
     * Temporary lockdown: only payroll jobs are allowed to run.
     * All other queued jobs are deleted until incident response is complete.
     *
     * @var array<string>
     */
    protected $allowedJobClasses = [
        'App\Jobs\PayrollJob',
    ];

    /**
     * Raw payload markers that indicate likely command injection / env exfiltration.
     *
     * @var array<string>
     */
    protected $blockedPayloadMarkers = [
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:purge-untrusted
                            {--dry-run : List jobs that would be deleted without deleting}
                            {--force : Skip confirmation when deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove queue jobs that are not PayrollJob (temporary security lockdown)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if (!Schema::hasTable('jobs')) {
                $this->warn('Table [jobs] does not exist.');
                return self::SUCCESS;
            }

            $rows = DB::table('jobs')->get(['id', 'queue', 'payload', 'created_at']);
            $toDelete = [];
            $allowed = [];

            foreach ($rows as $row) {
                $payload = is_string($row->payload) ? json_decode($row->payload, true) : $row->payload;
                if (!is_array($payload)) {
                    $toDelete[] = $row;
                    continue;
                }
                $jobClass = $this->extractJobClass($payload);
                if ($jobClass === null || $jobClass === '') {
                    $toDelete[] = $row;
                    continue;
                }

                $isAllowed = in_array($jobClass, $this->allowedJobClasses, true);
                $hasBlockedMarker = $this->payloadContainsBlockedMarker($row->payload);

                if (!$isAllowed || $hasBlockedMarker) {
                    $toDelete[] = $row;
                } else {
                    $allowed[] = $row;
                }
            }

            if (empty($toDelete)) {
                $this->info('No untrusted jobs found. Total jobs: ' . count($allowed));
                return self::SUCCESS;
            }

            $this->warn('Found ' . count($toDelete) . ' job(s) with untrusted class (will be removed):');
            foreach ($toDelete as $row) {
                $payload = is_string($row->payload) ? json_decode($row->payload, true) : $row->payload;
                $jobClass = is_array($payload) ? $this->extractJobClass($payload) : null;
                $this->line('  - ID ' . $row->id . ': ' . ($jobClass ?? 'unknown') . ' (queue: ' . $row->queue . ')');
            }

            if ($this->option('dry-run')) {
                $this->info('Dry run: no jobs deleted. Run without --dry-run to delete.');
                return self::SUCCESS;
            }

            if (!$this->option('force') && !$this->confirm('Delete these ' . count($toDelete) . ' job(s)?', true)) {
                return self::SUCCESS;
            }

            $ids = array_map(fn ($row) => $row->id, $toDelete);
            DB::table('jobs')->whereIn('id', $ids)->delete();
            $this->info('Deleted ' . count($ids) . ' untrusted job(s). Remaining: ' . count($allowed));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Purge failed: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }
    }

    private function extractJobClass(array $payload): ?string
    {
        // Prefer commandName because displayName can be spoofed in poisoned payloads.
        return $payload['data']['commandName']
            ?? $payload['displayName']
            ?? null;
    }

    private function payloadContainsBlockedMarker(mixed $rawPayload): bool
    {
        if (!is_string($rawPayload) || $rawPayload === '') {
            return false;
        }

        $lowerPayload = Str::lower($rawPayload);
        foreach ($this->blockedPayloadMarkers as $marker) {
            if (Str::contains($lowerPayload, $marker)) {
                return true;
            }
        }

        return false;
    }
}
