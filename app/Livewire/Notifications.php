<?php

namespace App\Livewire;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public $isOpened = false;
    public $isShowSearch = false;
    public $search_param = '';
    public $chunkSize = 5;
    public $loadedNotifications = 0;
    public $firstLoadUnreadCount = null;
    public $user;

    public $notifications = [
        'unread' => 0,
        'data' => []
    ];

    protected $listeners = ['loadNotifications', 'notify'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadNotifications();
    }

    public function toggleSearch()
    {
        $this->isShowSearch = !$this->isShowSearch;

        if (!$this->isShowSearch) {
            $this->search_param = '';
        }
    }

    public function toggle()
    {
        $this->isOpened = !$this->isOpened;
        $this->loadNotifications();
    }

    public function read(string $id, string $redirect)
    {
        Notification::where('id', $id)->update([
            'read_at' => Carbon::now()
        ]);

        return $this->redirect($redirect);
    }

    public function loadNotifications()
    {
        $query = Notification::query();

        // Role-based filtering
        if ($this->user->roles[0]->name === 'employee') {
            $query->where('notifiable_id', $this->user->id)
                  ->whereJsonContains('data->audience', 'employee');
        } else {
            $query->whereJsonContains('data->audience', 'admin');
        }

        // Apply search filter if provided
        if (!empty($this->search_param)) {
            $query->where(function ($q) {
                $q->whereRaw("LOWER(JSON_UNQUOTE(data->'$.title')) LIKE ?", ['%' . strtolower($this->search_param) . '%'])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(data->'$.message')) LIKE ?", ['%' . strtolower($this->search_param) . '%']);
            });
        }
        

        $allNotifications = $query->latest()->get();

        $existingIds = collect($this->notifications['data'])->pluck('id')->toArray();

        $newNotifications = $allNotifications->filter(
            fn($notification) => !in_array($notification->id, $existingIds)
        );

        // Update unread count
        $this->notifications['unread'] += $newNotifications->filter(
            fn($notification) => is_null($notification->read_at)
        )->count();

        // Merge into existing notifications
        $this->notifications['data'] = collect($newNotifications->toArray())
            ->merge($this->notifications['data'])
            ->toArray();

        $this->loadedNotifications += $newNotifications->count();

        // Handle notification broadcast if new unread appear
        if (is_null($this->firstLoadUnreadCount)) {
            $this->firstLoadUnreadCount = $this->notifications['unread'];
        } elseif ($this->notifications['unread'] > $this->firstLoadUnreadCount) {
            $this->firstLoadUnreadCount = $this->notifications['unread'];
            $this->dispatch('notify');
        }
    }

    public function search()
    {
        // Clear existing data to allow search results to appear freshly
        $this->notifications = ['unread' => 0, 'data' => []];
        $this->loadedNotifications = 0;
        $this->loadNotifications($this->search_param);
    }

    public function markAsRead()
    {
        $query = Notification::query();

        if ($this->user->roles[0]->name === 'employee') {
            $query->where('notifiable_id', $this->user->id)
                  ->whereJsonContains('data->audience', 'employee');
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
