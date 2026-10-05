<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Services\WishlistManager;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('RHÁYỌ̀OGE — Quiet luxury for her')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.store.home', [
            'featured' => Product::query()->published()->where('featured', true)->take(4)->get(),
            'savedIds' => app(WishlistManager::class)->ids(),
        ])->layout('layouts::app', ['home' => true]);
    }
}
