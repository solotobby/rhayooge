<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Message — Dharmie')]
class MessageShow extends Component
{
    public ContactMessage $message;

    public function mount(ContactMessage $message): void
    {
        $this->message = $message;
        $this->message->markRead();
        $this->message->refresh();
    }

    public function delete()
    {
        $this->message->delete();
        session()->flash('admin_toast', 'Note dismissed.');

        return $this->redirectRoute('admin.messages', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.message-show');
    }
}
