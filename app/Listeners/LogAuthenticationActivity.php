<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LogAuthenticationActivity
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event)
    {
        $this->logActivity($event->user, 'logged in');
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event)
    {
        $this->logActivity($event->user, 'logged out');
    }

    /**
     * Log activity to storage/logs/trails/
     */
    protected function logActivity($user, $action)
    {
        $role = $this->getRole(get_class($user));

        $directory = storage_path('logs/trails/' . strtolower($role) . '/');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }

        $filename = now()->format('m-d-y') . '.log';

        if ($role == 'Admin') {
            $user_id = $user->id;
            $name = $user->name;
        } else if ($role == 'Employee') {
            $user_id = $user->id;
            $name = $user->personal->firstname . ' ' . $user->personal->lastname;
        }

        $logEntry = sprintf(
            "[%s] %s %s (%s) %s\n",
            now()->toDateTimeString(),
            $role,
            $user_id,
            $name,
            $action,
        );

        File::append($directory . $filename, $logEntry);

        File::append($directory . $filename, $logEntry);
    }

    private function getRole($model) {
        switch ($model) {
            case 'App\Models\User':
                return 'Admin';
            case 'App\Models\EmployeeAccount':
                return 'Employee';
            default:
                return 'Unknown';
        }
    }
}
