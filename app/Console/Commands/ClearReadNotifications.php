<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class ClearReadNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear notifications that have been read (read_at is not null)';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $cutoffTime = \Carbon\Carbon::now()->subHours(12);
        $delete = Notification::whereNotNull('read_at')
            ->whereDate('read_at', '<=', $cutoffTime->format('Y-m-d H:i:s'))
            ->delete();

        $this->info("{$delete} read notifications cleared successfully.");
    }
}
