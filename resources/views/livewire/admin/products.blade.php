<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Collection Management</p>
            <h1>The pieces.</h1>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}" wire:navigate>+ New piece</a>
        </div>
    </header>

    <div class="admin-toolbar">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search pieces by name or slug..." aria-label="Search products">
        <select wire:model.live="category" aria-label="Filter by category">
            @foreach ($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <select wire:model.live="status" aria-label="Filter by status">
            <option value="all">All statuses</option>
            <option value="published">Published (Live in Store)</option>
            <option value="draft">Drafts (Hidden)</option>
        </select>
    </div>

    @if ($products->isEmpty())
        <div class="admin-empty" style="padding:3.5rem 1.5rem;text-align:center;">
            <p class="eyebrow" style="margin-bottom:0.5rem;">No Pieces Found</p>
            <p style="color:var(--muted);margin-bottom:1.5rem;">No pieces match your search or filter criteria. You can clear filters or add a new piece.</p>
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}" wire:navigate>Add First Piece</a>
        </div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Piece</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Inventory</th>
                        <th>Status</th>
                        <th>Curation</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <div class="admin-product">
                                    <button
                                        type="button"
                                        wire:click="inspectProduct({{ $product->id }})"
                                        style="cursor:pointer;border:none;background:transparent;padding:0;line-height:0;"
                                        title="Quick inspect piece"
                                    >
                                        <img
                                            src="{{ $product->image }}"
                                            alt=""
                                            onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                        >
                                    </button>
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}" wire:navigate>
                                            <strong>{{ $product->name }}</strong>
                                        </a>
                                        <span class="admin-mute" style="font-family:monospace;font-size:0.75rem;">{{ $product->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="admin-stock-badge" style="background:var(--sand);color:var(--charcoal);">{{ $product->category }}</span>
                            </td>
                            <td>
                                <div>
                                    @if ($product->hasDiscount())
                                        <del style="color:var(--muted);font-size:0.78rem;">{{ $product->formattedOriginalPrice() }}</del>
                                    @endif
                                    <strong style="color:var(--brown-deep);">{{ $product->formattedPrice() }}</strong>
                                </div>
                                @if ($product->hasDiscount())
                                    <span class="admin-badge-discount" style="margin-top:0.2rem;">
                                        -{{ $product->discountPercent() }}% OFF
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    @if ($product->quantity > 5)
                                        <span class="admin-stock-badge in-stock">
                                            ● {{ $product->quantity }} in stock
                                        </span>
                                    @elseif ($product->quantity > 0)
                                        <span class="admin-stock-badge low-stock">
                                            ● {{ $product->quantity }} left
                                        </span>
                                    @else
                                        <span class="admin-stock-badge out-of-stock">
                                            ● Out of stock
                                        </span>
                                    @endif
                                </div>
                                @if (!empty($product->size_inventory))
                                    <div class="admin-mute" style="font-family:monospace;font-size:0.74rem;margin-top:0.25rem;">
                                        @foreach ($product->size_inventory as $sz => $cnt)
                                            <span>{{ $sz }}: {{ $cnt }}</span>{{ ! $loop->last ? ' · ' : '' }}
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    @if ($product->isPublished())
                                        <span class="admin-stock-badge in-stock">Live</span>
                                    @else
                                        <span class="admin-stock-badge" style="background:var(--sand);color:var(--muted);">Draft</span>
                                    @endif
                                    <button
                                        type="button"
                                        style="font-size:0.72rem;color:var(--brown);background:none;border:none;cursor:pointer;text-decoration:underline;text-underline-offset:2px;"
                                        wire:click="toggleStatus({{ $product->id }})"
                                        title="{{ $product->isPublished() ? 'Switch to draft' : 'Publish piece live' }}"
                                    >
                                        {{ $product->isPublished() ? 'Draft' : 'Publish' }}
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="admin-flags">
                                    <button
                                        type="button"
                                        class="{{ $product->featured ? 'on' : '' }}"
                                        wire:click="toggleFeatured({{ $product->id }})"
                                        title="Toggle homepage featured"
                                    >
                                        Featured
                                    </button>
                                    <button
                                        type="button"
                                        class="{{ $product->newest ? 'on' : '' }}"
                                        wire:click="toggleNewest({{ $product->id }})"
                                        title="Toggle new arrival ribbon"
                                    >
                                        New
                                    </button>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <div class="admin-row-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}" wire:navigate>Edit</a>
                                    <button type="button" wire:click="inspectProduct({{ $product->id }})">View</button>
                                    <a href="{{ route('product', $product) }}" target="_blank" rel="noopener">Store ↗</a>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="Remove '{{ $product->name }}' from the collection?"
                                        class="btn-delete"
                                        title="Delete piece"
                                    >Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="admin-pager">
                <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
                <div style="display:flex;gap:0.75rem;">
                    @if ($products->onFirstPage())
                        <span style="opacity:0.4;cursor:not-allowed;">Previous</span>
                    @else
                        <button type="button" wire:click="previousPage">Previous</button>
                    @endif

                    @if ($products->hasMorePages())
                        <button type="button" wire:click="nextPage">Next</button>
                    @else
                        <span style="opacity:0.4;cursor:not-allowed;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Admin Piece Inspection Modal -->
    @if ($showInspectModal && $inspectingProduct)
        <div class="admin-modal-overlay" @click.self="$wire.closeInspectModal()">
            <div class="admin-modal" @click.stop style="max-width:880px;width:95%;max-height:90vh;overflow-y:auto;background:#ffffff;border-radius:14px;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(0,0,0,0.2);padding:2.25rem;">
                <div class="admin-modal-head" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f0eee9;padding-bottom:1.25rem;margin-bottom:1.75rem;gap:1rem;">
                    <div>
                        <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.25rem;">
                            <span class="eyebrow" style="color:var(--terracotta,#b85d38);margin:0;">Piece Inspection</span>
                            @if ($inspectingProduct->isPublished())
                                <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">● Published & Live</span>
                            @else
                                <span class="admin-stock-badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:0.7rem;">Draft (Hidden)</span>
                            @endif
                        </div>
                        <h2 style="font-family:var(--font-serif);font-size:1.85rem;margin:0;color:#1c1917;font-weight:500;">{{ $inspectingProduct->name }}</h2>
                        <span class="admin-mute" style="font-size:0.8rem;color:#78716c;margin-top:0.25rem;display:block;">
                            Category: {{ $inspectingProduct->category }} &bull; Slug: <code style="background:#faf8f5;border:1px solid #e7e5e4;padding:0.15rem 0.45rem;border-radius:4px;color:#1c1917;">{{ $inspectingProduct->slug }}</code>
                        </span>
                    </div>

                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <button
                            type="button"
                            class="btn btn-ghost"
                            style="font-size:0.75rem;padding:0.45rem 0.85rem;"
                            wire:click="toggleStatus({{ $inspectingProduct->id }})"
                        >
                            {{ $inspectingProduct->isPublished() ? 'Change to Draft' : 'Publish Live' }}
                        </button>
                        <a
                            href="{{ route('admin.products.edit', $inspectingProduct) }}"
                            wire:navigate
                            class="btn btn-primary"
                            style="font-size:0.75rem;padding:0.45rem 0.95rem;"
                        >
                            Edit Piece
                        </a>
                        <button type="button" wire:click="closeInspectModal" style="background:none;border:none;font-size:1.6rem;cursor:pointer;color:#78716c;line-height:1;margin-left:0.5rem;padding:0.25rem;" title="Close modal">&times;</button>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1.35fr;gap:2rem;">
                    <!-- Left: Gallery & Visuals -->
                    <div>
                        <div style="aspect-ratio:3/4;background:var(--ivory);border:1px solid var(--line);border-radius:3px;overflow:hidden;position:relative;">
                            <img
                                src="{{ $inspectingProduct->image }}"
                                alt="{{ $inspectingProduct->name }}"
                                style="width:100%;height:100%;object-fit:cover;"
                                onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                            >
                            <span style="position:absolute;top:8px;left:8px;background:rgba(0,0,0,0.7);color:#fff;font-size:0.68rem;padding:0.2rem 0.55rem;border-radius:2px;letter-spacing:0.04em;">
                                Primary Cover
                            </span>
                        </div>

                        @php
                            $extraAngles = array_values(array_filter(array_slice($inspectingProduct->galleryImages(), 1)));
                        @endphp
                        @if (!empty($extraAngles))
                            <div style="margin-top:0.85rem;">
                                <p class="admin-label" style="font-size:0.68rem;margin-bottom:0.4rem;">Additional Angles ({{ count($extraAngles) }}):</p>
                                <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:0.5rem;">
                                    @foreach ($extraAngles as $angle)
                                        <div style="aspect-ratio:3/4;border:1px solid var(--line);border-radius:2px;overflow:hidden;background:var(--ivory);">
                                            <img src="{{ $angle }}" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div style="margin-top:1.25rem;">
                            <a
                                href="{{ route('product', $inspectingProduct) }}"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-ghost"
                                style="width:100%;justify-content:center;font-size:0.8rem;padding:0.6rem;"
                            >
                                Open Customer Store View ↗
                            </a>
                        </div>
                    </div>

                    <!-- Right: Specs, Pricing, Sizes, Commission -->
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        <!-- Pricing Card -->
                        <div style="background:var(--ivory);border:1px solid var(--line);padding:1.15rem;border-radius:3px;">
                            <p class="admin-label" style="font-size:0.7rem;color:var(--brown);margin-bottom:0.5rem;">Pricing & Valuation</p>
                            <div style="display:flex;align-items:baseline;gap:0.75rem;">
                                <span style="font-size:1.6rem;font-weight:600;color:var(--brown);">{{ $inspectingProduct->formattedPrice() }}</span>
                                @if ($inspectingProduct->hasDiscount())
                                    <del style="opacity:0.6;font-size:1.05rem;">{{ $inspectingProduct->formattedOriginalPrice() }}</del>
                                    <span class="admin-badge-discount" style="font-size:0.78rem;font-weight:600;padding:0.2rem 0.5rem;">
                                        Save {{ $inspectingProduct->discountPercent() }}%
                                    </span>
                                @endif
                            </div>
                            @if ($inspectingProduct->hasDiscount())
                                <p style="margin:0.4rem 0 0;font-size:0.78rem;color:var(--muted);">
                                    Customer saves <strong>{{ \App\Models\Product::naira($inspectingProduct->original_price - $inspectingProduct->price) }}</strong> on this promotion.
                                </p>
                            @endif
                        </div>

                        <!-- Business Executive Affiliate Commission Card -->
                        <div style="background:#fff;border:1px solid var(--line);padding:1.15rem;border-radius:3px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                                <p class="admin-label" style="font-size:0.7rem;color:var(--brown);margin:0;">Executive Commission</p>
                                <span class="admin-stock-badge in-stock" style="font-size:0.72rem;">
                                    Earns {{ \App\Models\Product::naira($inspectingProduct->executiveCommissionAmount()) }} / sale
                                </span>
                            </div>
                            <p style="font-size:0.82rem;color:var(--muted);margin:0;">
                                Rate: <strong>{{ $inspectingProduct->commission_rate }}{{ $inspectingProduct->commission_type === 'fixed' ? ' ₦' : '%' }}</strong>
                                ({{ $inspectingProduct->commission_type === 'fixed' ? 'Fixed fee per order' : 'Percentage of retail selling price' }})
                            </p>
                        </div>

                        <!-- Size & Inventory Breakdown -->
                        <div style="background:var(--ivory);border:1px solid var(--line);padding:1.15rem;border-radius:3px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                                <p class="admin-label" style="font-size:0.7rem;color:var(--brown);margin:0;">
                                    Sizes & Stock by Size (Mode: {{ ucfirst($inspectingProduct->size_mode ?? 'letter') }})
                                </p>
                                <span style="font-size:0.75rem;font-weight:600;color:var(--brown);">
                                    Total: {{ $inspectingProduct->quantity }} in stock
                                </span>
                            </div>

                            @if (!empty($inspectingProduct->size_inventory))
                                <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(80px, 1fr));gap:0.5rem;margin-top:0.5rem;">
                                    @foreach ($inspectingProduct->size_inventory as $size => $stock)
                                        <div style="background:#fff;border:1px solid var(--line);border-radius:2px;padding:0.45rem 0.6rem;text-align:center;">
                                            <strong style="display:block;font-size:0.9rem;color:var(--brown);">{{ $size }}</strong>
                                            <span style="font-size:0.72rem;color:{{ $stock > 0 ? '#2e5932' : '#991b1b' }};font-weight:500;">
                                                {{ $stock > 0 ? $stock . ' left' : 'Out' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif (!empty($inspectingProduct->sizes))
                                <div style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-top:0.4rem;">
                                    @foreach ($inspectingProduct->sizes as $sz)
                                        <span class="chip active" style="font-size:0.75rem;">{{ $sz }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p style="font-size:0.78rem;color:var(--muted);margin:0;">No specific sizing configured.</p>
                            @endif
                        </div>

                        <!-- Description & Details -->
                        <div>
                            <p class="admin-label" style="font-size:0.7rem;margin-bottom:0.35rem;">Story / Description</p>
                            <p style="font-size:0.82rem;line-height:1.6;color:var(--charcoal);margin:0;background:#fff;border:1px solid var(--line);padding:0.85rem;border-radius:2px;">
                                {{ $inspectingProduct->description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1.75rem;border-top:1px solid var(--line);padding-top:1.25rem;">
                    <button type="button" class="btn btn-ghost" wire:click="closeInspectModal">Close</button>
                    <a href="{{ route('admin.products.edit', $inspectingProduct) }}" wire:navigate class="btn btn-primary">
                        Edit This Piece
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
