@props(['product', 'saved' => false, 'delay' => 0])

<article class="product-card" style="animation-delay: {{ $delay }}s">
    <a class="product-media" href="{{ route('product', $product) }}" wire:navigate>
        <img
            src="{{ $product->image }}"
            alt="{{ $product->name }}"
            onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
        >
        <div class="product-actions">
            <button
                class="save-btn {{ $saved ? 'saved' : '' }}"
                type="button"
                wire:click.prevent.stop="$dispatch('toggle-saved', { productId: {{ $product->id }} })"
                aria-label="Save {{ $product->name }}"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 15s-6-3.8-6-8a3.5 3.5 0 0 1 6-2.4A3.5 3.5 0 0 1 15 7c0 4.2-6 8-6 8z"/></svg>
            </button>
            <span class="quick-btn wide">View piece</span>
        </div>
    </a>
    <p class="category-tag">{{ $product->category }}</p>
    <div class="product-meta">
        <h3><a href="{{ route('product', $product) }}" wire:navigate>{{ $product->name }}</a></h3>
        <div class="price-wrap">
            @if ($product->hasDiscount())
                <del class="price-original">{{ $product->formattedOriginalPrice() }}</del>
            @endif
            <span class="price">{{ $product->formattedPrice() }}</span>
        </div>
    </div>
    @if ($product->quantity <= 0)
        <button
            class="btn btn-primary btn-full card-add"
            type="button"
            disabled
            style="opacity:0.5;cursor:not-allowed;"
        >
            Out of stock
        </button>
    @else
        <button
            class="btn btn-primary btn-full card-add"
            type="button"
            wire:click.stop="$dispatch('add-to-cart', { productId: {{ $product->id }} })"
        >
            Add to bag
        </button>
    @endif
</article>
