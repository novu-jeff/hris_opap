<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Security check command: queue poisoning, env exposure, and risky routes.
 *
 * Context (Batosay1337 & .env in queue:work output):
 * - "Batosay1337" is the attacker's handle/signature. It is NOT in your codebase.
 * - Malicious jobs were pushed to the jobs table (e.g. via SQLi, DB access, or
 *   a past vulnerability). When queue:work ran, it executed those jobs.
 * - Those jobs ran code that: ran shell commands (id, uname), echoed "Batosay1337",
 *   and dumped getenv() / $_ENV, which is why .env appeared in the terminal.
 * - The app never exposes .env by design; the leak was from malicious job code.
 *
 * Mitigation: Only allow specific job classes (PurgeUntrustedQueueJobs), run
 * queue:purge-untrusted on a schedule, and lock down how jobs are added.
 */
class SecurityCheck extends Command
{
    protected $signature = 'security:check
                            {--fix : Apply safe fixes (e.g. comment out dangerous routes)}';

    protected $description = 'Check queue security, env exposure, and risky API routes';

    public function handle(): int
    {
        $this->info('Running security checks...');
        $failed = 0;

        // 1. Queue: only allowed jobs
        if (!$this->checkQueueAllowlist()) {
            $failed++;
        }

        // 2. Unauthenticated risky API routes
        if (!$this->checkUnauthenticatedApiRoutes()) {
            $failed++;
        }

        // 3. APP_DEBUG in production
        if (!$this->checkAppDebug()) {
            $failed++;
        }

        // 4. Unserialize / dangerous patterns in app (quick scan)
        if (!$this->checkDangerousPatterns()) {
            $failed++;
        }

        // 5. Jobs table: any job not in allowlist
        if (!$this->checkPendingJobs()) {
            $failed++;
        }

        if ($failed === 0) {
            $this->newLine();
            $this->info('All checks passed.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn("{$failed} check(s) need attention. See above.");
        return self::FAILURE;
    }

    private function checkQueueAllowlist(): bool
    {
        $this->line('Checking queue job allowlist...');
        $purge = new \App\Console\Commands\PurgeUntrustedQueueJobs();
        $ref = new \ReflectionClass($purge);
        $prop = $ref->getProperty('allowedJobClasses');
        $prop->setAccessible(true);
        $allowed = $prop->getValue($purge);
        if ($allowed !== ['App\Jobs\PayrollJob']) {
            $this->warn('  [ ] Queue lockdown is active: allowedJobClasses must be only App\Jobs\PayrollJob.');
            return false;
        }
        $this->info('  [√] Queue allowlist lockdown is active (PayrollJob only).');
        return true;
    }

    private function checkUnauthenticatedApiRoutes(): bool
    {
        $this->line('Checking for unauthenticated risky API routes...');
        $risky = [];
        foreach (Route::getRoutes() as $route) {
            if (!$route->uri() || strpos($route->uri(), 'api/') !== 0 && strpos($route->uri(), 'api') !== 0) {
                continue;
            }
            $middleware = $route->middleware();
            $hasAuth = in_array('auth:sanctum', $middleware) || in_array('auth:api', $middleware);
            $methods = $route->methods();
            $isMutation = in_array('POST', $methods) || in_array('PUT', $methods) || in_array('PATCH', $methods) || in_array('DELETE', $methods);
            $uri = $route->uri();
            $allowedUnauth = ['login', 'check_if_logged_in', 'forgot-password', 'reset-password'];
            $isAllowedUnauth = collect($allowedUnauth)->contains(fn ($p) => str_contains($uri, $p));
            if ($isMutation && !$hasAuth && !$isAllowedUnauth) {
                $risky[] = implode(',', $methods) . ' ' . $uri;
            }
        }
        if (count($risky) > 0) {
            $this->warn('  [ ] Unauthenticated API mutation routes:');
            foreach ($risky as $r) {
                $this->line('      ' . $r);
            }
            $this->line('      Secure these with auth:sanctum or remove if not needed.');
            return false;
        }
        $this->info('  [√] No unauthenticated API mutation routes found.');
        return true;
    }

    private function checkAppDebug(): bool
    {
        $this->line('Checking APP_DEBUG...');
        $env = config('app.env');
        $debug = config('app.debug');
        if (($env === 'production' || $env === 'prod') && $debug === true) {
            $this->warn('  [ ] APP_DEBUG should be false in production.');
            return false;
        }
        $this->info('  [√] APP_DEBUG is appropriate for environment.');
        return true;
    }

    private function checkDangerousPatterns(): bool
    {
        $this->line('Checking for dangerous code patterns...');
        $base = app_path();
        $dangerous = ['unserialize(', 'eval(', 'passthru(', 'shell_exec(', 'system(', 'phpinfo('];
        $found = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \RecursiveDirectoryIterator::SKIP_DOTS));
        $excludeFile = basename(__FILE__);
        foreach ($it as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $path = $file->getPathname();
            if (basename($path) === $excludeFile) {
                continue;
            }
            $content = @file_get_contents($path);
            if ($content === false) {
                continue;
            }
            $relativePath = str_replace($base . '/', '', $path);
            foreach ($dangerous as $pattern) {
                if (stripos($content, $pattern) === false) {
                    continue;
                }
                // Unserialize of Laravel's job_batches.options (internal) is acceptable.
                if ($pattern === 'unserialize(' && $relativePath === 'Livewire/Admin/System/Jobs.php') {
                    continue;
                }
                $found[] = $relativePath . ' contains ' . $pattern;
            }
        }
        if (count($found) > 0) {
            $this->warn('  [ ] Possible dangerous patterns (review; some may be false positives):');
            foreach (array_slice($found, 0, 10) as $f) {
                $this->line('      ' . $f);
            }
            if (count($found) > 10) {
                $this->line('      ... and ' . (count($found) - 10) . ' more.');
            }
            return false;
        }
        $this->info('  [√] No dangerous patterns found in app code.');
        return true;
    }

    private function checkPendingJobs(): bool
    {
        $this->line('Checking pending jobs table...');
        if (!Schema::hasTable('jobs')) {
            $this->info('  [√] No jobs table.');
            return true;
        }
        $allowed = [
            'App\Jobs\PayrollJob',
        ];
        $blockedPayloadMarkers = [
            'batosay1337',
            '$_env',
            'getenv(',
            'php_uname',
            'shell_exec(',
            'passthru(',
            'system(',
            'printenv',
        ];
        $rows = DB::table('jobs')->get(['id', 'payload']);
        $untrusted = 0;
        foreach ($rows as $row) {
            $payload = is_string($row->payload) ? json_decode($row->payload, true) : $row->payload;
            $name = is_array($payload)
                ? ($payload['data']['commandName'] ?? $payload['displayName'] ?? null)
                : null;
            $rawPayload = is_string($row->payload) ? Str::lower($row->payload) : '';
            $hasBlockedMarker = collect($blockedPayloadMarkers)->contains(
                fn (string $marker) => $rawPayload !== '' && Str::contains($rawPayload, $marker)
            );
            if ($name === null || !in_array($name, $allowed, true) || $hasBlockedMarker) {
                $untrusted++;
            }
        }
        if ($untrusted > 0) {
            $this->warn("  [ ] {$untrusted} job(s) in queue are not in the allowlist. Run: php artisan queue:purge-untrusted --force");
            return false;
        }
        $this->info('  [√] All pending jobs are in the allowlist (' . count($rows) . ' total).');
        return true;
    }
}
