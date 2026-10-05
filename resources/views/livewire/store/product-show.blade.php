<div>
    <article class="pdp">
        <div class="container">
            <nav class="pdp-crumb" aria-label="Breadcrumb">
                <a href="{{ route('shop') }}" wire:navigate>Shop</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('shop', ['category' => $product->category]) }}" wire:navigate>{{ $product->category }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $product->name }}</span>
            </nav>

            <div class="pdp-grid">
                @php
                    $gallery = $product->galleryImages();
                @endphp

                <div
                    class="pdp-gallery-wrap"
                    x-data="{
                        active: 0,
                        images: {{ \Illuminate\Support\Js::from($gallery) }},
                        zoomed: false,
                        zoomX: 50,
                        zoomY: 50,
                        modalOpen: false,
                        isHovering: false,
                        next() {
                            if (this.images.length > 1) {
                                this.active = (this.active + 1) % this.images.length;
                            }
                        },
                        prev() {
                            if (this.images.length > 1) {
                                this.active = (this.active - 1 + this.images.length) % this.images.length;
                            }
                        },
                        set(index) {
                            this.active = index;
                        },
                        onMouseMove(e) {
                            const rect = e.currentTarget.getBoundingClientRect();
                            const x = ((e.clientX - rect.left) / rect.width) * 100;
                            const y = ((e.clientY - rect.top) / rect.height) * 100;
                            this.zoomX = Math.max(0, Math.min(100, x));
                            this.zoomY = Math.max(0, Math.min(100, y));
                            e.currentTarget.style.setProperty('--zoom-x', `${this.zoomX}%`);
                            e.currentTarget.style.setProperty('--zoom-y', `${this.zoomY}%`);
                        }
                    }"
                    @keydown.right.window="modalOpen ? next() : null"
                    @keydown.left.window="modalOpen ? prev() : null"
                    @keydown.escape.window="modalOpen = false"
                >
                    <!-- Thumbnails Column / Strip -->
                    @if (count($gallery) > 1)
                        <div class="pdp-thumbs-rail" aria-label="Product thumbnails">
                            @foreach ($gallery as $index => $imgUrl)
                                <button
                                    type="button"
                                    class="pdp-thumb-item"
                                    :class="{ 'active': active === {{ $index }} }"
                                    @click="set({{ $index }})"
                                    @mouseenter="set({{ $index }})"
                                    aria-label="View photo {{ $index + 1 }} of {{ count($gallery) }}"
                                >
                                    <img
                                        src="{{ $imgUrl }}"
                                        alt="{{ $product->name }} - angle {{ $index + 1 }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                    >
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Main Stage Viewport -->
                    <div
                        class="pdp-main-stage"
                        :class="{ 'is-hovering': isHovering }"
                        @mouseenter="isHovering = true"
                        @mouseleave="isHovering = false"
                        @mousemove="onMouseMove($event)"
                        @click="modalOpen = true"
                        title="Click to view full screen"
                    >
                        <!-- Floating Pill: Counter / Tag -->
                        <span class="pdp-gallery-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <span x-text="`${active + 1} / ${images.length}`">1 / {{ count($gallery) }}</span>
                            <span style="opacity:0.35;">·</span>
                            <span>Silk drape</span>
                        </span>

                        <!-- Fullscreen inspect button -->
                        <button
                            type="button"
                            class="pdp-inspect-btn"
                            @click.stop="modalOpen = true"
                            title="Open high resolution view"
                            aria-label="Open high resolution view"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                <line x1="11" y1="8" x2="11" y2="14"></line>
                                <line x1="8" y1="11" x2="14" y2="11"></line>
                            </svg>
                        </button>

                        <!-- Main Image Container with light breathing animation & interactive hover zoom -->
                        <div class="pdp-img-container">
                            <template x-for="(src, idx) in images" :key="idx">
                                <img
                                    x-show="active === idx"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    :src="src"
                                    alt="{{ $product->name }}"
                                    class="pdp-main-img"
                                    fetchpriority="high"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                >
                            </template>
                        </div>

                        @if (count($gallery) > 1)
                            <!-- Prev / Next arrows -->
                            <button
                                type="button"
                                class="pdp-nav-arrow pdp-nav-prev"
                                @click.stop="prev()"
                                aria-label="Previous image"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="pdp-nav-arrow pdp-nav-next"
                                @click.stop="next()"
                                aria-label="Next image"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        @endif

                        <span class="pdp-hint-pill">Hover to inspect fabric · Click to expand</span>
                    </div>

                    <!-- Fullscreen Lightbox Modal -->
                    <template x-teleport="body">
                        <div
                            class="pdp-lightbox"
                            x-show="modalOpen"
                            x-transition.opacity.duration.250ms
                            style="display: none;"
                            @click.self="modalOpen = false"
                        >
                            <button
                                type="button"
                                class="pdp-lightbox-close"
                                @click="modalOpen = false"
                                aria-label="Close fullscreen view"
                            >&times;</button>

                            <img
                                :src="images[active]"
                                alt="{{ $product->name }}"
                                class="pdp-lightbox-img"
                            >

                            <div class="pdp-lightbox-counter">
                                <span x-text="`${active + 1} of ${images.length}`"></span>
                                <span> · {{ $product->name }}</span>
                            </div>

                            <template x-if="images.length > 1">
                                <div>
                                    <button
                                        type="button"
                                        class="pdp-lightbox-nav pdp-lightbox-prev"
                                        @click.stop="prev()"
                                        aria-label="Previous image"
                                    >&#8249;</button>
                                    <button
                                        type="button"
                                        class="pdp-lightbox-nav pdp-lightbox-next"
                                        @click.stop="next()"
                                        aria-label="Next image"
                                    >&#8250;</button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="pdp-details">
                    <p class="eyebrow">{{ $product->category }}</p>
                    <h1>{{ $product->name }}</h1>
                    <div class="pdp-price-wrap" style="display:flex;align-items:baseline;gap:0.6rem;padding-bottom:1.2rem;margin-bottom:1.2rem;border-bottom:1px solid var(--line);flex-wrap:wrap;">
                        @if ($product->hasDiscount())
                            <del class="price-original" style="font-size:1.15rem;text-decoration:line-through;opacity:0.55;color:var(--muted);">{{ $product->formattedOriginalPrice() }}</del>
                            <span class="discount-badge" style="font-size:0.7rem;letter-spacing:0.08em;background:var(--terracotta,#b85d38);color:#fff;padding:0.2rem 0.5rem;font-weight:600;">-{{ $product->discountPercent() }}%</span>
                        @endif
                        <p class="pdp-price" style="margin:0;padding:0;border:none;">{{ $product->formattedPrice() }}</p>
                    </div>
                    <p class="pdp-copy">{{ $product->description }}</p>

                    <div class="pdp-size">
                        <div class="pdp-size-head">
                            <span class="eyebrow">Size</span>
                            <span class="note">{{ $selectedSize }}</span>
                        </div>
                        <div class="size-row">
                            @foreach ($product->sizes as $size)
                                <button
                                    class="size-btn {{ $selectedSize === $size ? 'active' : '' }}"
                                    type="button"
                                    wire:click="chooseSize('{{ $size }}')"
                                >{{ $size }}</button>
                            @endforeach
                        </div>
                    </div>

                    @if ($product->quantity <= 0)
                        <p class="stock-status out-of-stock" style="color:#93291e;font-weight:500;margin:1rem 0 0.5rem;">Currently out of stock</p>
                        <div class="pdp-actions">
                            <button class="btn btn-accent btn-full" type="button" disabled style="opacity:0.5;cursor:not-allowed;">Out of stock</button>
                            <button class="btn btn-ghost btn-full" type="button" wire:click="toggleSaved">
                                {{ $saved ? 'Saved for later' : 'Save for later' }}
                            </button>
                        </div>
                    @else
                        @if ($product->quantity <= 5)
                            <p class="stock-status low-stock" style="color:#b85d38;font-size:0.82rem;margin:0.8rem 0 0.3rem;">Only {{ $product->quantity }} piece{{ $product->quantity > 1 ? 's' : '' }} remaining</p>
                        @endif
                        <div class="pdp-actions">
                            <button class="btn btn-accent btn-full" type="button" wire:click="addToCart">Add to bag</button>
                            <button class="btn btn-ghost btn-full" type="button" wire:click="toggleSaved">
                                {{ $saved ? 'Saved for later' : 'Save for later' }}
                            </button>
                        </div>
                    @endif

                    <dl class="pdp-notes">
                        <div>
                            <dt>Dharmie</dt>
                            <dd>Cut in warm earth pigments. Designed to skim, not cling.</dd>
                        </div>
                        <div>
                            <dt>Delivery</dt>
                            <dd>Lagos in 2–4 days. Complimentary above ₦50,000.</dd>
                        </div>
                        <div>
                            <dt>Care</dt>
                            <dd>Cool hand wash or dry clean. Hang to keep the line.</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section pdp-related">
            <div class="container">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">The edit</p>
                        <h2>You may also like</h2>
                    </div>
                    <a class="btn btn-ghost" href="{{ route('shop') }}" wire:navigate>View all</a>
                </div>
                <div class="product-grid">
                    @foreach ($related as $index => $item)
                        <x-product-card
                            :product="$item"
                            :saved="in_array($item->id, app(\App\Services\WishlistManager::class)->ids(), true)"
                            :delay="$index * 0.05"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
