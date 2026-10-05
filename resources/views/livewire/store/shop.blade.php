<div class="shop-page">
    <header class="shop-hero">
        <div class="container">
            <p class="eyebrow">The collection</p>
            <h1>Shop</h1>
            <p class="shop-lead">Pieces cut to layer with one another. Filter by category and price, then take what lingers.</p>
        </div>
    </header>

    <div class="container">
        <div class="overlay-bg {{ $showFilters ? 'open' : '' }}" wire:click="$set('showFilters', false)"></div>

        <div class="cat-bar" role="tablist" aria-label="Categories">
            @foreach ($categories as $cat)
                <button
                    class="chip {{ $category === $cat ? 'active' : '' }}"
                    type="button"
                    wire:click="setCategory('{{ $cat }}')"
                >{{ $cat }}</button>
            @endforeach
        </div>

        <div class="shop-toolbar">
            <button class="btn btn-ghost filter-toggle" type="button" wire:click="$toggle('showFilters')">Price</button>
            <p class="note">{{ $products->count() }} of {{ $total }} piece{{ $total === 1 ? '' : 's' }}</p>
            <label class="sr-only" for="sort-select">Sort</label>
            <select id="sort-select" wire:model.live="sort">
                <option value="featured">Featured</option>
                <option value="newest">Newest</option>
                <option value="price-asc">Price · low to high</option>
                <option value="price-desc">Price · high to low</option>
            </select>
        </div>

        <div class="shop-layout">
            <aside class="filters drawer left {{ $showFilters ? 'open' : '' }}" id="filters">
                <div class="drawer-head">
                    <h2>Price</h2>
                    <button class="close-x" type="button" wire:click="$set('showFilters', false)">×</button>
                </div>
                <div class="filter-group">
                    <h3>Up to</h3>
                    <div class="price-values">
                        <span>₦0</span>
                        <span>{{ \App\Models\Product::naira($maxPrice) }}</span>
                    </div>
                    <input type="range" min="12000" max="90000" step="1000" wire:model.live="maxPrice" aria-label="Maximum price">
                    <p class="note" style="margin-top:0.8rem">Complimentary delivery above ₦50,000.</p>
                </div>
            </aside>

            <div>
                <div class="product-grid">
                    @forelse ($products as $index => $product)
                        <x-product-card :product="$product" :saved="in_array($product->id, $savedIds, true)" :delay="($index % 8) * 0.04" />
                    @empty
                        <div class="empty-state">No pieces in this range. Soften the filters to see more.</div>
                    @endforelse
                </div>

                @if ($hasMore)
                    <div class="load-more">
                        <button class="btn btn-ghost" type="button" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore">
                            <span wire:loading.remove wire:target="loadMore">Load more</span>
                            <span wire:loading wire:target="loadMore">Loading…</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
