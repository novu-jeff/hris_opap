<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurgeUntrustedQueueJobs extends Command
{
    /**
     * Only these job class prefixes are allowed. Jobs with other classes are deleted
     * to prevent malicious payloads from being unserialized when queue:work runs.
     *
     * @var array<string>
     */
    protected $allowedPrefixes = [
        'App\\',
        'Illuminate\\',
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
    protected $description = 'Remove queue jobs whose class is not in the allowed whitelist (stops malicious file creation from queue)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!Schema::hasTable('jobs')) {
            $this->warn('Table [jobs] does not exist.');
            return self::SUCCESS;
        }

        $rows = DB::table('jobs')->get(['id', 'queue', 'payload', 'created_at']);
        $toDelete = [];
        $allowed = [];

        foreach ($rows as $row) {
            $payload = json_decode($row->payload, true);
            $displayName = $payload['displayName'] ?? null;

            if ($displayName === null) {
                $toDelete[] = $row;
                continue;
            }

            $isAllowed = false;
            foreach ($this->allowedPrefixes as $prefix) {
                if (str_starts_with($displayName, $prefix)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
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
            $payload = json_decode($row->payload, true);
            $displayName = $payload['displayName'] ?? 'unknown';
            $this->line('  - ID ' . $row->id . ': ' . $displayName . ' (queue: ' . $row->queue . ')');
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run: no jobs deleted. Run without --dry-run to delete.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Delete these ' . count($toDelete) . ' job(s)?', true)) {
            return self::SUCCESS;
        }

        $ids = array_column($toDelete, 'id');
        DB::table('jobs')->whereIn('id', $ids)->delete();
        $this->info('Deleted ' . count($ids) . ' untrusted job(s). Remaining: ' . count($allowed));

        return self::SUCCESS;
    }
}
