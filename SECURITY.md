# Security: Queue poisoning and .env exposure

## What happened (Batosay1337 and .env in queue:work)

When running `php artisan queue:work`, the terminal showed:

- Shell command output (`id`, `uname -a`)
- The string **Batosay1337**
- A full dump of environment variables (`.env`-style secrets)

### Who is Batosay1337?

**Batosay1337** is not a user in your app or codebase. It is the **attacker’s handle/signature**. The malicious jobs that were pushed to the queue contained code that:

1. Ran shell commands (`id`, `uname`) and printed the output.
2. Echoed the string `Batosay1337`.
3. Dumped the server environment (e.g. `getenv()` / `$_ENV`), which is why `.env` values appeared in the queue worker output.

So the **.env was not “in the queue” as stored data** — it was **printed by malicious code** when the worker executed those jobs.

### How did malicious jobs get into the queue?

Malicious jobs were inserted into the `jobs` table so that when the worker ran, it would unserialize and execute them. Possible ways that could have happened:

- Direct or indirect **database access** (compromised DB credentials, SQL injection).
- A **past vulnerability** that allowed pushing arbitrary job payloads.
- **Server or app compromise** (e.g. stolen credentials, exposed admin).

Your application code does **not** expose `.env` or dump the environment by design; the leak came only from that **malicious job code** running inside the worker process.

---

## Mitigations in place

1. **Queue job allowlist**  
   Only these job classes are allowed to run; all others are purged before execution:
   - `App\Jobs\PayrollJob`
   - `App\Notifications\Notifications`
   - `Illuminate\Notifications\Notification` / `SendQueuedNotification` / `SendQueuedNotifications`  
   Malicious jobs are deleted by `queue:purge-untrusted` and never executed.

2. **Scheduled purge**  
   `php artisan queue:purge-untrusted --force` runs every 5 minutes (see `app/Console/Kernel.php`) so untrusted jobs are removed regularly.

3. **Unauthenticated API route removed**  
   The unauthenticated `POST api/test` (MonitoringController) route was commented out to reduce abuse surface. Re-enable only with proper auth or a secret token if you need it.

4. **Security check command**  
   Run:
   ```bash
   php artisan security:check
   ```
   This checks: queue allowlist, unauthenticated API mutations, APP_DEBUG, dangerous code patterns, and that pending jobs are in the allowlist.

---

## What you should do

- **Rotate all secrets** that may have been in the environment (DB passwords, `APP_KEY`, API keys, etc.) — treat them as exposed.
- **Run the purge manually** when needed:  
  `php artisan queue:purge-untrusted --force`
- **Run the security check** after changes:  
  `php artisan security:check`
- **Harden the server**: limit DB and SSH access, keep Laravel and dependencies updated, and investigate how the `jobs` table was written to (logs, DB audit, access control).
