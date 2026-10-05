<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Messages — Dharmie')]
class Messages extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.messages', [
            'messages' => ContactMessage::query()
                ->latest()
                ->paginate(12),
        ]);
    }
}
