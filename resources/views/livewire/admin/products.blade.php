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
        @if ($search !== '' || $category !== 'All' || $status !== 'all')
            <button
                type="button"
                class="btn btn-ghost"
                wire:click="$set('search', ''); $set('category', 'All'); $set('status', 'all');"
                style="font-size:0.75rem;padding:0.45rem 0.85rem;"
            >
                Reset
            </button>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="admin-empty" style="padding:3.5rem 1.5rem;text-align:center;">
            <p class="eyebrow" style="margin-bottom:0.5rem;">No Pieces Found</p>
            <p style="color:var(--muted);margin-bottom:1.5rem;">No pieces match your search or filter criteria. You can clear filters or add a new piece.</p>
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}" wire:navigate>Add First Piece</a>
        </div>
    @else
        <!-- Minimalistic Products Table -->
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width:240px;">Piece</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="text-align:right;min-width:140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr wire:key="product-row-{{ $product->id }}">
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <button
                                        type="button"
                                        wire:click="inspectProduct({{ $product->id }})"
                                        style="cursor:pointer;border:1px solid #ebe7df;border-radius:4px;padding:0;line-height:0;overflow:hidden;flex-shrink:0;background:#f9f8f6;width:40px;height:52px;"
                                        title="Quick inspect piece"
                                    >
                                        <img
                                            src="{{ $product->image }}"
                                            alt=""
                                            style="width:100%;height:100%;object-fit:cover;"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                        >
                                    </button>
                                    <div style="min-width:0;">
                                        <button
                                            type="button"
                                            wire:click="inspectProduct({{ $product->id }})"
                                            style="font-weight:600;color:var(--brown-deep);background:none;border:none;padding:0;cursor:pointer;text-align:left;font-size:0.88rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                        >
                                            {{ $product->name }}
                                        </button>
                                        <span class="admin-mute" style="font-family:monospace;font-size:0.72rem;display:block;">{{ $product->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="admin-stock-badge" style="background:#f4f2ee;color:#44403c;font-size:0.72rem;">{{ $product->category }}</span>
                            </td>
                            <td>
                                <strong style="color:var(--brown-deep);font-size:0.88rem;font-family:monospace;">{{ $product->formattedPrice() }}</strong>
                                @if ($product->hasDiscount())
                                    <span class="admin-badge-discount" style="display:inline-block;margin-left:0.35rem;font-size:0.68rem;padding:0.15rem 0.4rem;">
                                        -{{ $product->discountPercent() }}%
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($product->quantity > 5)
                                    <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">
                                        ● {{ $product->quantity }} in stock
                                    </span>
                                @elseif ($product->quantity > 0)
                                    <span class="admin-stock-badge low-stock" style="font-size:0.7rem;">
                                        ● {{ $product->quantity }} left
                                    </span>
                                @else
                                    <span class="admin-stock-badge out-of-stock" style="font-size:0.7rem;">
                                        ● Out of stock
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($product->isPublished())
                                    <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">Live</span>
                                @else
                                    <span class="admin-stock-badge" style="background:#f4f2ee;color:#78716c;font-size:0.7rem;">Draft</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:0.4rem;align-items:center;justify-content:flex-end;">
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        wire:click="inspectProduct({{ $product->id }})"
                                        style="font-size:0.75rem;padding:0.35rem 0.8rem;font-weight:600;"
                                    >
                                        Details
                                    </button>
                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        wire:navigate
                                        class="btn btn-ghost"
                                        style="font-size:0.75rem;padding:0.35rem 0.75rem;"
                                    >
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="admin-pager" style="margin-top:1.5rem;">
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
                        <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.25rem;flex-wrap:wrap;">
                            <span class="eyebrow" style="color:var(--terracotta,#b85d38);margin:0;">Piece Overview</span>
                            @if ($inspectingProduct->isPublished())
                                <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">● Published & Live</span>
                            @else
                                <span class="admin-stock-badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:0.7rem;">Draft (Hidden)</span>
                            @endif
                            @if ($inspectingProduct->featured)
                                <span class="admin-stock-badge" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;font-size:0.7rem;">★ Featured</span>
                            @endif
                            @if ($inspectingProduct->newest)
                                <span class="admin-stock-badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:0.7rem;">✦ New Arrival</span>
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
                        <div style="aspect-ratio:3/4;background:var(--ivory);border:1px solid var(--line);border-radius:4px;overflow:hidden;position:relative;">
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
                                        <div style="aspect-ratio:3/4;border:1px solid var(--line);border-radius:3px;overflow:hidden;background:var(--ivory);">
                                            <img src="{{ $angle }}" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Curation Switches -->
                        <div style="margin-top:1.25rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <button
                                type="button"
                                class="btn btn-ghost"
                                wire:click="toggleFeatured({{ $inspectingProduct->id }})"
                                style="font-size:0.75rem;padding:0.45rem 0.75rem;flex:1;justify-content:center;{{ $inspectingProduct->featured ? 'background:#ecfdf5;color:#065f46;border-color:#a7f3d0;' : '' }}"
                            >
                                {{ $inspectingProduct->featured ? '★ Featured' : '☆ Feature' }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost"
                                wire:click="toggleNewest({{ $inspectingProduct->id }})"
                                style="font-size:0.75rem;padding:0.45rem 0.75rem;flex:1;justify-content:center;{{ $inspectingProduct->newest ? 'background:#eff6ff;color:#1e40af;border-color:#bfdbfe;' : '' }}"
                            >
                                {{ $inspectingProduct->newest ? '✦ New Arrival' : '✧ Mark New' }}
                            </button>
                        </div>

                        <div style="margin-top:0.75rem;">
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
                        <div style="background:var(--ivory);border:1px solid var(--line);padding:1.15rem;border-radius:4px;">
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
                        <div style="background:#fff;border:1px solid var(--line);padding:1.15rem;border-radius:4px;">
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
                        <div style="background:var(--ivory);border:1px solid var(--line);padding:1.15rem;border-radius:4px;">
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
                                        <div style="background:#fff;border:1px solid var(--line);border-radius:3px;padding:0.45rem 0.6rem;text-align:center;">
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
                            <p style="font-size:0.82rem;line-height:1.6;color:var(--charcoal);margin:0;background:#fff;border:1px solid var(--line);padding:0.85rem;border-radius:4px;">
                                {{ $inspectingProduct->description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.75rem;border-top:1px solid var(--line);padding-top:1.25rem;">
                    <button
                        type="button"
                        wire:click="delete({{ $inspectingProduct->id }}); closeInspectModal();"
                        wire:confirm="Remove '{{ $inspectingProduct->name }}' from the collection?"
                        class="btn-delete"
                        style="font-size:0.78rem;background:none;border:1px solid #fecaca;color:#dc2626;padding:0.45rem 0.85rem;border-radius:4px;cursor:pointer;"
                    >
                        Delete Piece
                    </button>

                    <div style="display:flex;gap:0.75rem;align-items:center;">
                        <button type="button" class="btn btn-ghost" wire:click="closeInspectModal">Close</button>
                        <a href="{{ route('admin.products.edit', $inspectingProduct) }}" wire:navigate class="btn btn-primary">
                            Edit This Piece
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
