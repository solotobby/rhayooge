<?php

namespace App\Livewire\Store;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('About us — RHÁYỌ̀OGE')]
class About extends Component
{
    public function render()
    {
        return view('livewire.store.about');
    }
}
