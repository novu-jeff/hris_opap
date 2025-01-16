<?php

namespace App\Livewire;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Notifications extends Component
{
    public $isOpened = false;
    public $notifications = [
        'unread' => 0,  // To store unread notification count
        'data' => []    // To store the notification data
    ];
    public $chunkSize = 5;
    public $loadedNotifications = 0;
    public $firstLoadUnreadCount;

    public $user;

    public $listeners = ['loadNotifications', 'notify'];

    public function mount()
    {
        $user = Auth::user();

        $this->user = $user;
        $this->loadNotifications();
    }

    public function toggle()
    {
        if ($this->isOpened) {
            $this->isOpened = false;
            $this->loadNotifications();
            return;
        }

        $this->loadNotifications();
        $this->isOpened = true;
        return;
    }
    
    public function read(string $id, string $redirect) {
        Notification::where('id', $id)
            ->update([
                'read_at' => Carbon::now()
            ]);

        return $this->redirect($redirect);
        
    }

    public function loadNotifications()
    {    
        // Determine base query based on user role
        $query = Notification::query();

        if ($this->user->roles[0]->name === 'employee') {
            $query->where('notifiable_id', $this->user->id)
                  ->whereJsonContains('data->audience', 'employee'); // Filter JSON audience
        } else {
            $query->whereJsonContains('data->audience', 'admin');
        }
    
        // Fetch all notifications from the query
        $allNotifications = $query->latest()->get();
    
        // Ensure existing notifications are treated as a collection
        $existingNotificationIds = collect($this->notifications['data'])->pluck('id')->toArray();
    
        // Filter new notifications to avoid duplication
        $newNotifications = $allNotifications->filter(fn($notification) => !in_array($notification->id, $existingNotificationIds));
    
        // Update unread count
        $this->notifications['unread'] += $newNotifications->filter(fn($notification) => is_null($notification->read_at))->count();
    
        // Merge new notifications into the existing notifications array
        $this->notifications['data'] = collect($newNotifications->toArray())
            ->merge($this->notifications['data'])
            ->toArray();
    
        // Update the count of loaded notifications
        $this->loadedNotifications += $newNotifications->count();
        
        // Check if this is the first load
        if (!isset($this->firstLoadUnreadCount)) {
            // Store the initial unread count
            $this->firstLoadUnreadCount = $this->notifications['unread'];
        } else {
            // Check if the unread count has increased since the first load
            if ($this->notifications['unread'] > $this->firstLoadUnreadCount) {
            // Dispatch the 'notify' event if there are new unread notifications
                $this->firstLoadUnreadCount = $this->notifications['unread'];
                $this->dispatch('notify');
            }
        }
    }

    public function markAsRead() {

        $query = Notification::query();

        if ($this->user->roles[0]->name === 'employee') {
            $query->where('notifiable_id', $this->user->id)
                  ->whereJsonContains('data->audience', 'employee'); // Filter JSON audience
        } else {
            $query->whereJsonContains('data->audience', 'admin');
        }

        $query->update([
            'read_at' => Carbon::now()
        ]);

        $this->dispatch('refreshPage');
    }


    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => $this->notifications
        ]);
    }

}
