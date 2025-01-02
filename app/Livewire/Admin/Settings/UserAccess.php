<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;

class UserAccess extends Component
{
    public function render()
    {
        return view('livewire.admin.settings.user-access')
            ->layout('layouts.admin', [
                'title' => 'User Access'
            ]);
    }
}
