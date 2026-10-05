<?php

namespace App\Livewire\Store;

use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Contact — RHÁYỌ̀OGE')]
class Contact extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $message = '';

    public bool $sent = false;

    public function send(): void
    {
        $this->validate();

        ContactMessage::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'message']);
        $this->sent = true;
        $this->dispatch('toast', message: 'Message received · we will write back shortly');
    }

    public function render()
    {
        return view('livewire.store.contact');
    }
}
