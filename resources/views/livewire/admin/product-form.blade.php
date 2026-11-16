<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Collection Studio</p>
            <h1>{{ $productId ? 'Edit piece' : 'New piece' }}</h1>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            @if ($productId)
                <a
                    class="btn btn-ghost"
                    href="{{ route('product', ['product' => $productId]) }}"
                    target="_blank"
                    rel="noopener"
                    style="font-size:0.75rem;padding:0.45rem 0.95rem;"
                >
                    Storefront View ↗
                </a>
            @endif
            <a class="btn btn-ghost" href="{{ route('admin.products') }}" wire:navigate style="font-size:0.75rem;padding:0.45rem 0.95rem;">
                &larr; Back to collection
            </a>
        </div>
    </header>

    <form
        class="admin-form"
        wire:submit="save"
        x-data
        @form-error.window="setTimeout(() => { const el = document.getElementById('error-summary') || document.querySelector('.form-error'); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 120)"
    >
        <div class="admin-form-main" style="max-width:960px;margin:0 auto;box-shadow:0 4px 24px rgba(28, 17, 12, 0.04);border:1px solid #ebe7df;border-radius:14px;padding:2.5rem;background:#ffffff;">

            @if ($errors->any())
                <div id="error-summary" style="background:#fef2f2;border:1px solid #f87171;border-radius:10px;padding:1.25rem 1.5rem;margin-bottom:2rem;">
                    <div style="display:flex;align-items:center;gap:0.6rem;color:#991b1b;margin-bottom:0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <strong style="font-size:0.95rem;">Please correct the following before saving this piece:</strong>
                    </div>
                    <ul style="margin:0;padding-left:1.5rem;color:#7f1d1d;font-size:0.85rem;line-height:1.7;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECTION 1: PIECE IDENTITY -->
            <div>
                <div style="border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;">
                    <p class="eyebrow" style="margin-bottom:0.2rem;color:var(--terracotta,#b85d38);">Identity & Narrative</p>
                    <h2 style="font-family:var(--font-serif);font-size:1.6rem;margin:0;font-weight:500;color:#1c1917;">Piece Details</h2>
                </div>

                <div style="display:grid;gap:1.35rem;">
                    <div>
                        <label for="p-name">Piece Title</label>
                        <input
                            id="p-name"
                            type="text"
                            wire:model.live.debounce.400ms="name"
                            placeholder="e.g. Silk Wrapped Evening Gown"
                            autofocus
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.35rem;">
                            <label for="p-slug" style="margin-bottom:0;">Store Slug (URL Handle)</label>
                            <button
                                type="button"
                                class="admin-btn-action"
                                wire:click="generateSlug"
                                style="font-size:0.68rem;padding:0.2rem 0.6rem;cursor:pointer;background:none;border:none;color:var(--terracotta);text-decoration:underline;"
                            >
                                Auto-generate from title
                            </button>
                        </div>
                        <input
                            id="p-slug"
                            type="text"
                            wire:model.live.debounce.300ms="slug"
                            placeholder="e.g. silk-wrapped-evening-gown"
                        >
                        <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;padding:0.45rem 0.85rem;border-radius:6px;margin-top:0.4rem;font-size:0.78rem;color:var(--muted);">
                            Storefront URL: <span style="font-family:monospace;color:#1c1917;">{{ url('/shop') }}/<strong>{{ $slug ?: '...' }}</strong></span>
                        </div>
                        @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-form-split">
                        <div>
                            <label for="p-category">Atelier Category</label>
                            <select id="p-category" wire:model="category">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="p-status">Store Visibility</label>
                            <select id="p-status" wire:model="status">
                                <option value="published">● Live (Visible in public store & executive hub)</option>
                                <option value="draft">○ Draft (Hidden from store, internal testing)</option>
                            </select>
                            @error('status') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="p-desc">Story & Fabric Description</label>
                        <textarea
                            id="p-desc"
                            wire:model="description"
                            rows="5"
                            placeholder="Describe the silhouette, fabric composition, tailoring accents, and mood of the piece..."
                        ></textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Editorial Curation Badges -->
                    <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;border-radius:8px;padding:1.1rem 1.35rem;">
                        <span style="font-size:0.72rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:0.65rem;font-weight:600;">Editorial Curation Flags</span>
                        <div class="admin-check-row">
                            <label class="admin-check">
                                <input type="checkbox" wire:model="featured">
                                <span>Feature on Homepage Spotlight</span>
                            </label>
                            <label class="admin-check">
                                <input type="checkbox" wire:model="newest">
                                <span>Mark with 'New Arrival' Ribbon</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PRICING & DISCOUNTS -->
            <div style="border-top:1px solid #f0eee9;padding-top:2rem;">
                <div style="border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;">
                    <p class="eyebrow" style="margin-bottom:0.2rem;color:var(--terracotta,#b85d38);">Valuation & Promotions</p>
                    <h2 style="font-family:var(--font-serif);font-size:1.6rem;margin:0;font-weight:500;color:#1c1917;">Pricing & Discounts</h2>
                </div>

                <div style="display:grid;gap:1.35rem;">
                    <div class="admin-form-split">
                        <div>
                            <label for="p-price">
                                Retail Selling Price (₦) <span style="color:#b85d38;">*</span>
                                <small style="opacity:0.7;font-weight:normal;text-transform:none;">(The actual checkout price customers pay)</small>
                            </label>
                            <input
                                id="p-price"
                                type="number"
                                min="100"
                                step="100"
                                wire:model.live.debounce.300ms="price"
                                placeholder="e.g. 55000"
                            >
                            @error('price') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="p-original-price">
                                Original Reference Price (₦)
                                <small style="opacity:0.7;font-weight:normal;text-transform:none;">(Optional strike-through compare-at price)</small>
                            </label>
                            <input
                                id="p-original-price"
                                type="number"
                                min="100"
                                step="100"
                                wire:model.live.debounce.300ms="original_price"
                                placeholder="Leave empty if regular price"
                            >
                            @error('original_price') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Discount Presets Bar -->
                    <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.15rem 1.35rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.65rem;">
                            <span style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--muted);">
                                Promotional Discount
                            </span>
                            @if ((int)$discount_percent > 0)
                                <span style="font-size:0.75rem;color:#2e5932;font-weight:600;">
                                    {{ $discount_percent }}% OFF Applied
                                </span>
                            @else
                                <span style="font-size:0.75rem;color:var(--muted);">
                                    Standard Retail (No Discount)
                                </span>
                            @endif
                        </div>

                        <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                            <span style="font-size:0.75rem;color:var(--muted);">Quick apply:</span>
                            @foreach ([0 => 'No discount', 10 => '10% OFF', 15 => '15% OFF', 20 => '20% OFF', 25 => '25% OFF', 30 => '30% OFF', 50 => '50% OFF'] as $pct => $label)
                                <button
                                    type="button"
                                    class="chip {{ (int)$discount_percent === $pct ? 'active' : '' }}"
                                    wire:click="setDiscount({{ $pct }})"
                                    style="font-size:0.72rem;padding:0.3rem 0.65rem;border-radius:6px;"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        @error('discount_percent') <p class="form-error" style="margin-top:0.4rem;">{{ $message }}</p> @enderror
                    </div>

                    <!-- Live Calculated Selling Price & Customer Savings Card -->
                    <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;border-radius:10px;padding:1.35rem 1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.25rem;">
                        <div>
                            <span style="font-size:0.68rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);display:block;font-weight:600;">Customer Retail Checkout Price</span>
                            <div style="display:flex;align-items:baseline;gap:0.75rem;margin-top:0.35rem;">
                                @if ((int)$discount_percent > 0 && (int)$original_price > (int)$price)
                                    <del style="opacity:0.55;font-size:1.15rem;color:var(--charcoal);">
                                        ₦{{ number_format((int)$original_price) }}
                                    </del>
                                @endif
                                <strong style="font-family:var(--font-serif);font-size:2.2rem;color:var(--brown,#221f1e);font-weight:500;">
                                    ₦{{ number_format((int)$price) }}
                                </strong>
                                @if ((int)$discount_percent > 0 && (int)$original_price > (int)$price)
                                    <span class="admin-badge-discount" style="font-size:0.8rem;padding:0.25rem 0.65rem;border-radius:4px;font-weight:600;">
                                        -{{ $discount_percent }}% OFF
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ((int)$discount_percent > 0 && (int)$original_price > (int)$price)
                            <div style="text-align:right;">
                                <span style="font-size:0.72rem;color:var(--muted);display:block;">Customer Promotional Savings</span>
                                <strong style="font-size:1.1rem;color:#2e5932;display:block;margin-top:0.15rem;">
                                    ₦{{ number_format((int)$original_price - (int)$price) }}
                                </strong>
                            </div>
                        @else
                            <div style="text-align:right;">
                                <span style="font-size:0.75rem;color:var(--muted);">Regular Collection Pricing</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECTION 3: BUSINESS EXECUTIVE COMMISSION -->
            <div style="border-top:1px solid #f0eee9;padding-top:2rem;">
                <div style="border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;">
                    <p class="eyebrow" style="margin-bottom:0.2rem;color:var(--terracotta,#b85d38);">Affiliate Network</p>
                    <h2 style="font-family:var(--font-serif);font-size:1.6rem;margin:0;font-weight:500;color:#1c1917;">Business Executive (BE) Commission</h2>
                </div>

                <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.35rem 1.5rem;display:grid;gap:1.25rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
                        <p class="note" style="margin:0;font-size:0.85rem;color:var(--muted);">
                            Define how much partner executives earn whenever a sale originates through their dedicated product links.
                        </p>
                        @if ((int)$price > 0)
                            <div style="background:#e4ebe4;border:1px solid #c8d8c8;border-radius:6px;padding:0.35rem 0.75rem;display:inline-flex;align-items:center;gap:0.4rem;">
                                <span style="font-size:0.72rem;color:#2e5932;font-weight:600;letter-spacing:0.04em;">EXECUTIVE REWARD:</span>
                                <strong style="color:#2e5932;font-size:0.95rem;">₦{{ number_format($this->calculatedCommission) }} / sale</strong>
                            </div>
                        @endif
                    </div>

                    <div class="admin-form-split">
                        <div>
                            <label for="p-comm-type">Commission Structure</label>
                            <select id="p-comm-type" wire:model.live="commission_type">
                                <option value="percent">Percentage of retail selling price (%)</option>
                                <option value="fixed">Fixed reward amount (₦ per order)</option>
                            </select>
                            @error('commission_type') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="p-comm-rate">
                                {{ $commission_type === 'fixed' ? 'Fixed Reward Amount (₦)' : 'Commission Rate (%)' }}
                            </label>
                            <input
                                id="p-comm-rate"
                                type="number"
                                min="1"
                                step="{{ $commission_type === 'fixed' ? '500' : '1' }}"
                                wire:model.live="commission_rate"
                                placeholder="{{ $commission_type === 'fixed' ? '5000' : '10' }}"
                            >
                            @error('commission_rate') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: SIZES & STOCK INVENTORY -->
            <div style="border-top:1px solid #f0eee9;padding-top:2rem;">
                <div style="border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <p class="eyebrow" style="margin-bottom:0.2rem;color:var(--terracotta,#b85d38);">Atelier Sizing & Inventory</p>
                        <h2 style="font-family:var(--font-serif);font-size:1.6rem;margin:0;font-weight:500;color:#1c1917;">Sizes & Stock by Size</h2>
                    </div>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <span style="font-size:0.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">Total Stock:</span>
                        <strong style="font-size:1.1rem;color:var(--brown);font-weight:600;">{{ $quantity }} units</strong>
                    </div>
                </div>

                <div style="display:grid;gap:1.35rem;">
                    <!-- Sizing Mode selector -->
                    <div>
                        <label style="font-size:0.72rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:0.5rem;">Sizing Mode</label>
                        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                            @foreach ($sizePresets as $modeKey => $preset)
                                <button
                                    type="button"
                                    class="chip {{ $sizeMode === $modeKey ? 'active' : '' }}"
                                    wire:click="setSizeMode('{{ $modeKey }}')"
                                    style="padding:0.45rem 1rem;font-size:0.8rem;"
                                >
                                    {{ $preset['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Available Sizes in Mode -->
                    @if (! empty($sizePresets[$sizeMode]['options']))
                        <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;border-radius:8px;padding:1.15rem 1.35rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.65rem;flex-wrap:wrap;gap:0.5rem;">
                                <span style="font-size:0.72rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);font-weight:600;">
                                    Available Sizes in {{ $sizePresets[$sizeMode]['label'] }} (Click to toggle)
                                </span>
                                <div style="display:flex;gap:0.45rem;">
                                    <button type="button" class="admin-btn-action" wire:click="selectPresetSizes" style="font-size:0.68rem;padding:0.2rem 0.55rem;cursor:pointer;">Select All</button>
                                    <button type="button" class="admin-btn-action" wire:click="clearSizes" style="font-size:0.68rem;padding:0.2rem 0.55rem;cursor:pointer;">Clear All</button>
                                </div>
                            </div>

                            <div class="admin-size-row">
                                @foreach ($sizePresets[$sizeMode]['options'] as $size)
                                    <button
                                        type="button"
                                        class="chip {{ in_array($size, $selectedSizes, true) ? 'active' : '' }}"
                                        wire:click="toggleSize('{{ $size }}')"
                                        style="min-width:44px;text-align:center;padding:0.4rem 0.85rem;"
                                    >
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add custom size -->
                    <div style="display:flex;gap:0.6rem;max-width:380px;align-items:center;">
                        <input
                            id="custom-size-input"
                            type="text"
                            wire:model="customSizeInput"
                            wire:keydown.enter.prevent="addCustomSize"
                            placeholder="Add specific size (e.g. 14, UK 10, Free Size)"
                            style="margin:0;"
                        >
                        <button type="button" class="btn btn-ghost" wire:click="addCustomSize" style="padding:0.65rem 1rem;font-size:0.75rem;white-space:nowrap;">
                            + Add Size
                        </button>
                    </div>

                    <!-- Per-Size Stock Allocation Table -->
                    @if (! empty($selectedSizes))
                        <div class="mt-4">
                            <span class="admin-label" style="display:block;margin-bottom:0.75rem;">
                                Allocate Inventory Stock Per Size
                            </span>

                            <div class="admin-table-wrap">
                                <table class="admin-table" style="min-width: 480px;">
                                    <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Units in Stock</th>
                                            <th>Availability Status</th>
                                            <th style="text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedSizes as $sz)
                                            @php
                                                $stockUnits = (int)($sizeStocks[$sz] ?? 0);
                                            @endphp
                                            <tr>
                                                <td style="font-weight: 500;">
                                                    <span style="display:inline-block;padding:0.2rem 0.6rem;background:#2c1e16;color:#faf7f2;border-radius:4px;font-size:0.75rem;font-weight:600;letter-spacing:0.04em;">
                                                        {{ $sz }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="display:flex;align-items:center;gap:0.5rem;max-width:140px;">
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="1"
                                                            wire:model.live="sizeStocks.{{ $sz }}"
                                                            class="admin-input"
                                                            style="width:80px;padding:0.35rem 0.5rem;text-align:center;font-variant-numeric:tabular-nums;"
                                                        >
                                                        <span style="font-size:0.8rem;color:var(--muted);">units</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($stockUnits > 5)
                                                        <span class="admin-status" style="border-color:#cde5d3;color:#2b6b3e;background:#ecf5ee;">
                                                            {{ $stockUnits }} available
                                                        </span>
                                                    @elseif ($stockUnits > 0)
                                                        <span class="admin-status" style="border-color:#f6e4bd;color:#976418;background:#fef8eb;">
                                                            {{ $stockUnits }} left
                                                        </span>
                                                    @else
                                                        <span class="admin-status" style="border-color:#f8d4cd;color:#aa3e2e;background:#fdf1ef;">
                                                            Out of stock
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="text-align: right;">
                                                    <button
                                                        type="button"
                                                        wire:click="toggleSize('{{ $sz }}')"
                                                        style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:1.1rem;line-height:1;padding:0.3rem 0.5rem;"
                                                        title="Remove size"
                                                    >
                                                        &times;
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div style="background:#faf8f5;border:1px dashed #d7d1c5;border-radius:8px;padding:2rem;text-align:center;color:var(--muted);">
                            <p style="margin:0;font-size:0.88rem;">No sizes selected yet. Select from the presets above or add custom sizes.</p>
                        </div>
                    @endif
                    @error('selectedSizes') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- SECTION 5: EDITORIAL IMAGERY & GALLERY -->
            <div style="border-top:1px solid #f0eee9;padding-top:2rem;">
                <div style="border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <p class="eyebrow" style="margin-bottom:0.2rem;color:var(--terracotta,#b85d38);">Visual Showcase</p>
                        <h2 style="font-family:var(--font-serif);font-size:1.6rem;margin:0;font-weight:500;color:#1c1917;">Product Pictures & Gallery</h2>
                    </div>
                    @php
                        $totalPics = (!empty($image) ? 1 : 0) + count($additionalImages);
                    @endphp
                    <span style="font-size:0.78rem;color:var(--muted);font-weight:500;">
                        {{ $totalPics }} {{ Str::plural('picture', $totalPics) }} uploaded
                    </span>
                </div>

                <div class="admin-media-grid" style="align-items:start;display:grid;grid-template-columns:1fr 310px;gap:2rem;">
                    <!-- Left: Upload Zone & Visual Gallery Previews Grid -->
                    <div style="display:flex;flex-direction:column;gap:1.5rem;">

                        <!-- Simple, Unified Upload Box -->
                        <div
                            x-data="{ isDropping: false, uploading: false, progress: 0 }"
                            x-on:livewire-upload-start="uploading = true; progress = 0"
                            x-on:livewire-upload-finish="uploading = false; progress = 100"
                            x-on:livewire-upload-error="uploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            style="position:relative;"
                        >
                            <label
                                for="productPhotosInput"
                                style="display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #d7d1c5;border-radius:12px;padding:2.25rem 1.5rem;text-align:center;background:#faf8f5;cursor:pointer;transition:all 0.2s;"
                                :style="isDropping ? 'border-color:var(--terracotta,#b85d38);background:#fefbf9;' : ''"
                                @dragover.prevent="isDropping = true"
                                @dragleave.prevent="isDropping = false"
                                @drop="isDropping = false"
                            >
                                <div style="width:52px;height:52px;border-radius:50%;background:#ffffff;border:1px solid #ebe7df;display:flex;align-items:center;justify-content:center;margin-bottom:0.85rem;box-shadow:0 2px 6px rgba(0,0,0,0.03);">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="color:var(--terracotta,#b85d38);">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                </div>
                                <strong style="display:block;font-size:0.95rem;color:#1c1917;margin-bottom:0.35rem;font-weight:600;">
                                    Click to choose pictures or drag & drop here
                                </strong>
                                <span style="font-size:0.78rem;color:var(--muted);display:block;line-height:1.4;">
                                    Upload single or multiple images · JPG, PNG, WEBP, AVIF · <strong>Up to 100MB each</strong>
                                </span>
                                <input
                                    id="productPhotosInput"
                                    type="file"
                                    wire:model="photos"
                                    multiple
                                    accept="image/*"
                                    style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;"
                                >
                            </label>

                            <!-- Real-time Upload Progress Bar -->
                            <div x-show="uploading" style="margin-top:0.85rem;background:#f5efe6;border:1px solid #ebe4da;padding:0.85rem 1.15rem;border-radius:8px;" x-cloak>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;font-size:0.78rem;">
                                    <span style="font-weight:600;color:#1c1917;">Uploading images to atelier library...</span>
                                    <span x-text="`${progress}%`" style="font-family:monospace;font-weight:600;color:var(--terracotta,#b85d38);"></span>
                                </div>
                                <div style="height:6px;width:100%;background:#e5ded3;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;background:var(--terracotta,#b85d38);transition:width 0.2s;" :style="`width: ${progress}%`"></div>
                                </div>
                            </div>
                        </div>

                        @error('photos.*') <p class="form-error">{{ $message }}</p> @enderror
                        @error('image') <p class="form-error">{{ $message }}</p> @enderror

                        <!-- All Uploaded Images Preview Grid with Featured Selector -->
                        @php
                            $allImgs = array_values(array_unique(array_filter(array_merge([$image], $additionalImages))));
                        @endphp

                        @if (! empty($allImgs))
                            <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:12px;padding:1.35rem;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                                    <div>
                                        <h3 style="font-size:0.95rem;font-weight:600;margin:0;color:#1c1917;">
                                            Uploaded Pictures ({{ count($allImgs) }})
                                        </h3>
                                        <p style="font-size:0.75rem;color:var(--muted);margin:0.2rem 0 0;">
                                            The starred picture is your <strong>Featured Image</strong> (lead cover). Click <strong>"★ Make Featured"</strong> on any picture to select it as the featured cover.
                                        </p>
                                    </div>
                                </div>

                                <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(130px, 1fr));gap:0.85rem;">
                                    @foreach ($allImgs as $imgUrl)
                                        @php
                                            $isFeatured = ($imgUrl === $image);
                                        @endphp
                                        <div
                                            style="position:relative;aspect-ratio:3/4;border-radius:8px;overflow:hidden;background:#faf8f5;transition:all 0.2s;{{ $isFeatured ? 'border: 2.5px solid var(--terracotta,#b85d38);box-shadow: 0 4px 14px rgba(184, 93, 56, 0.25);' : 'border: 1px solid #ebe7df;' }}"
                                            class="admin-photo-card"
                                        >
                                            <img
                                                src="{{ $imgUrl }}"
                                                alt="Piece image"
                                                style="width:100%;height:100%;object-fit:cover;"
                                                onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                            >

                                            <!-- Top Status Badge -->
                                            @if ($isFeatured)
                                                <div style="position:absolute;top:6px;left:6px;background:var(--terracotta,#b85d38);color:#ffffff;font-size:0.62rem;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;padding:0.2rem 0.5rem;border-radius:4px;display:flex;align-items:center;gap:0.25rem;box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                                                    ★ Featured
                                                </div>
                                            @endif

                                            <!-- Delete Button -->
                                            <button
                                                type="button"
                                                wire:click="removeImage('{{ addslashes($imgUrl) }}')"
                                                style="position:absolute;top:6px;right:6px;background:rgba(255,255,255,0.95);border:none;border-radius:50%;width:24px;height:24px;font-size:0.95rem;cursor:pointer;color:#93291e;line-height:1;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.15);"
                                                title="Remove this photo"
                                            >
                                                &times;
                                            </button>

                                            <!-- Bottom Action Bar / Overlay -->
                                            <div style="position:absolute;bottom:0;left:0;right:0;padding:0.45rem;background:linear-gradient(transparent, rgba(0,0,0,0.7));display:flex;justify-content:center;">
                                                @if ($isFeatured)
                                                    <span style="font-size:0.65rem;color:#ffffff;font-weight:600;letter-spacing:0.03em;">
                                                        Lead Cover
                                                    </span>
                                                @else
                                                    <button
                                                        type="button"
                                                        wire:click="setFeaturedImage('{{ addslashes($imgUrl) }}')"
                                                        style="background:#ffffff;color:#1c1917;border:none;border-radius:4px;font-size:0.65rem;font-weight:600;padding:0.25rem 0.6rem;cursor:pointer;letter-spacing:0.03em;box-shadow:0 2px 4px rgba(0,0,0,0.2);"
                                                    >
                                                        ★ Make Featured
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div style="background:#faf8f5;border:1px dashed #d7d1c5;border-radius:10px;padding:2rem;text-align:center;color:var(--muted);">
                                <p style="margin:0;font-size:0.85rem;">No pictures uploaded yet. Choose files above to preview and select your featured cover.</p>
                            </div>
                        @endif

                    </div>

                    <!-- Right Column: Live Featured Cover Showcase Preview -->
                    <div>
                        <label style="font-size:0.72rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:0.5rem;font-weight:600;">
                            Storefront Lead Cover Preview
                        </label>
                        <figure style="aspect-ratio:3/4;border:1px solid #ebe7df;border-radius:12px;overflow:hidden;background:var(--ivory,#faf8f5);display:flex;align-items:center;justify-content:center;position:relative;margin:0;box-shadow:0 6px 20px rgba(0,0,0,0.04);">
                            @if ($image)
                                <img
                                    src="{{ $image }}"
                                    alt="Lead Cover Preview"
                                    style="width:100%;height:100%;object-fit:cover;"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                >
                                <div style="position:absolute;top:10px;left:10px;background:rgba(28,17,12,0.85);color:#fff;font-size:0.68rem;letter-spacing:0.08em;text-transform:uppercase;padding:0.25rem 0.6rem;border-radius:4px;backdrop-filter:blur(4px);display:flex;align-items:center;gap:0.3rem;">
                                    ★ Featured Cover
                                </div>
                                <div style="position:absolute;bottom:0;left:0;right:0;padding:0.85rem 1rem;background:linear-gradient(transparent, rgba(28,17,12,0.8));color:#ffffff;">
                                    <span style="font-size:0.68rem;letter-spacing:0.1em;text-transform:uppercase;opacity:0.85;display:block;">Lead Storefront Photo</span>
                                    <strong style="font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                                        {{ $name ?: 'New Piece' }}
                                    </strong>
                                </div>
                            @else
                                <div style="text-align:center;padding:2rem;color:var(--muted);">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity:0.35;margin-bottom:0.75rem;color:var(--muted);">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    <p style="font-size:0.82rem;margin:0;color:var(--muted);font-weight:500;">No featured photo selected</p>
                                    <span style="font-size:0.72rem;color:var(--muted);opacity:0.8;display:block;margin-top:0.25rem;">
                                        Upload images on the left to set your featured cover
                                    </span>
                                </div>
                            @endif
                        </figure>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: STOREFRONT SIMULATION & ACTIONS -->
            <div style="border-top:1px solid #f0eee9;padding-top:2.5rem;margin-top:1.5rem;">
                @if ($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:0.9rem 1.25rem;color:#991b1b;font-size:0.85rem;display:flex;align-items:center;gap:0.65rem;margin-bottom:1.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span><strong>Cannot save piece:</strong> Please resolve the {{ $errors->count() }} {{ Str::plural('issue', $errors->count()) }} above ({{ $errors->first() }}).</span>
                    </div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.5rem;">
                    <div>
                        <span style="font-size:0.7rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);display:block;font-weight:600;">Status on Save</span>
                        <div style="display:flex;gap:0.75rem;margin-top:0.4rem;align-items:center;">
                            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;font-size:0.85rem;margin:0;">
                                <input type="radio" name="piece-status" value="published" wire:model="status" style="width:auto;margin:0;">
                                <span style="color:#2e5932;font-weight:500;">● Live in Store</span>
                            </label>
                            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;font-size:0.85rem;margin:0;">
                                <input type="radio" name="piece-status" value="draft" wire:model="status" style="width:auto;margin:0;">
                                <span style="color:var(--muted);font-weight:500;">○ Save as Draft</span>
                            </label>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:0.85rem;flex-wrap:wrap;">
                        <a class="btn btn-ghost" href="{{ route('admin.products') }}" wire:navigate style="padding:0.75rem 1.35rem;font-size:0.8rem;">
                            Cancel
                        </a>
                        <button
                            type="button"
                            class="btn btn-ghost"
                            wire:click="save('draft')"
                            style="padding:0.75rem 1.35rem;font-size:0.8rem;"
                        >
                            Save Draft
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            style="padding:0.75rem 2rem;font-size:0.85rem;min-width:210px;box-shadow:0 4px 14px rgba(28, 17, 12, 0.2);"
                        >
                            {{ $productId ? 'Update Piece' : 'Publish to Collection' }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
