<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Logistics & Delivery</p>
            <h1>Locations & Rates.</h1>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <button class="btn btn-primary" type="button" wire:click="openCreateModal">
                + Add Location
            </button>
        </div>
    </header>

    <div class="admin-toolbar">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search location (e.g. Ikeja, Lekki, Abuja)..." aria-label="Search locations">
    </div>

    @if ($locations->isEmpty())
        <div class="admin-empty" style="padding:3.5rem 1.5rem;text-align:center;">
            <p class="eyebrow" style="margin-bottom:0.5rem;">No Delivery Locations</p>
            <p style="color:var(--muted);margin-bottom:1.5rem;">No locations found matching your search. Create one to configure delivery fees for clients during checkout.</p>
            <button class="btn btn-primary" type="button" wire:click="openCreateModal">Add First Location</button>
        </div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Order</th>
                        <th>Location / Coverage Area</th>
                        <th>Delivery Fee</th>
                        <th>Estimated Transit</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $loc)
                        <tr>
                            <td style="color:var(--muted);font-family:monospace;font-size:0.85rem;">
                                #{{ $loc->sort_order }}
                            </td>
                            <td>
                                <strong style="color:var(--brown-deep);font-size:0.95rem;display:block;">{{ $loc->name }}</strong>
                            </td>
                            <td>
                                <strong style="font-family:monospace;color:var(--brown);font-size:1.05rem;">
                                    {{ $loc->formattedFee() }}
                                </strong>
                            </td>
                            <td>
                                <span style="color:var(--charcoal);font-size:0.85rem;">
                                    {{ $loc->estimated_days ?: '—' }}
                                </span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $loc->id }})"
                                    class="admin-stock-badge {{ $loc->is_active ? 'in-stock' : 'out-of-stock' }}"
                                    style="border:none;cursor:pointer;background:{{ $loc->is_active ? 'rgba(46, 89, 50, 0.12)' : 'rgba(184, 93, 56, 0.12)' }};color:{{ $loc->is_active ? '#2e5932' : '#b85d38' }};"
                                    title="Click to toggle status"
                                >
                                    {{ $loc->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:0.5rem;align-items:center;">
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        style="font-size:0.78rem;padding:0.35rem 0.75rem;"
                                        wire:click="openEditModal({{ $loc->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        style="font-size:0.78rem;padding:0.35rem 0.75rem;color:#b85d38;"
                                        wire:click="delete({{ $loc->id }})"
                                        wire:confirm="Are you sure you want to delete this shipping location?"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($locations->hasPages())
            <div class="admin-pager">
                <span>Page {{ $locations->currentPage() }} of {{ $locations->lastPage() }}</span>
                <div style="display:flex;gap:0.75rem;">
                    @if ($locations->onFirstPage())
                        <span style="opacity:0.4;cursor:not-allowed;">Previous</span>
                    @else
                        <button type="button" wire:click="previousPage">Previous</button>
                    @endif

                    @if ($locations->hasMorePages())
                        <button type="button" wire:click="nextPage">Next</button>
                    @else
                        <span style="opacity:0.4;cursor:not-allowed;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="admin-modal-overlay" wire:click.self="closeModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(20,18,16,0.6);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;z-index:9999;padding:1rem;">
            <div class="admin-modal" style="max-width:540px;width:100%;background:#ffffff;border-radius:12px;box-shadow:0 25px 60px -15px rgba(0,0,0,0.3);padding:2rem;border:1px solid #ebe7df;">
                <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f0eee9;padding-bottom:1rem;margin-bottom:1.5rem;">
                    <div>
                        <p class="eyebrow" style="margin:0;color:var(--terracotta,#b85d38);">Logistics Management</p>
                        <h2 style="font-family:var(--font-serif);font-size:1.45rem;margin:0.25rem 0 0;color:#1c1917;">
                            {{ $editingId ? 'Edit Delivery Location' : 'New Delivery Location' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="closeModal" style="background:none;border:none;font-size:1.6rem;cursor:pointer;color:#78716c;line-height:1;padding:0.25rem;" title="Close">&times;</button>
                </div>

                <form wire:submit="save">
                    <div style="display:flex;flex-direction:column;gap:1.15rem;">
                        <div>
                            <label style="display:block;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;margin-bottom:0.4rem;color:#44403c;">
                                Location Name / Coverage Area <span style="color:#b85d38;">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Ikeja & Environs, Lagos Island, Abuja FCT"
                                style="width:100%;padding:0.75rem 0.9rem;border:1px solid #d6d3d1;border-radius:6px;font-size:0.95rem;"
                                required
                            >
                            @error('name') <span style="color:#b85d38;font-size:0.75rem;margin-top:0.25rem;display:block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div>
                                <label style="display:block;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;margin-bottom:0.4rem;color:#44403c;">
                                    Delivery Fee (₦) <span style="color:#b85d38;">*</span>
                                </label>
                                <input
                                    type="number"
                                    wire:model="fee"
                                    min="0"
                                    step="100"
                                    placeholder="5000"
                                    style="width:100%;padding:0.75rem 0.9rem;border:1px solid #d6d3d1;border-radius:6px;font-size:0.95rem;font-family:monospace;"
                                    required
                                >
                                @error('fee') <span style="color:#b85d38;font-size:0.75rem;margin-top:0.25rem;display:block;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display:block;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;margin-bottom:0.4rem;color:#44403c;">
                                    Estimated Transit
                                </label>
                                <input
                                    type="text"
                                    wire:model="estimated_days"
                                    placeholder="e.g. 1–2 business days"
                                    style="width:100%;padding:0.75rem 0.9rem;border:1px solid #d6d3d1;border-radius:6px;font-size:0.95rem;"
                                >
                                @error('estimated_days') <span style="color:#b85d38;font-size:0.75rem;margin-top:0.25rem;display:block;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:center;">
                            <div>
                                <label style="display:block;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;margin-bottom:0.4rem;color:#44403c;">
                                    Display Order
                                </label>
                                <input
                                    type="number"
                                    wire:model="sort_order"
                                    min="0"
                                    style="width:100%;padding:0.75rem 0.9rem;border:1px solid #d6d3d1;border-radius:6px;font-size:0.95rem;font-family:monospace;"
                                >
                                @error('sort_order') <span style="color:#b85d38;font-size:0.75rem;margin-top:0.25rem;display:block;">{{ $message }}</span> @enderror
                            </div>

                            <div style="padding-top:1.4rem;">
                                <label style="display:inline-flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.9rem;color:#292524;">
                                    <input type="checkbox" wire:model="is_active" style="width:18px;height:18px;accent-color:#2e5932;">
                                    <span>Active (Available at Checkout)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:2rem;border-top:1px solid #f0eee9;padding-top:1.25rem;">
                        <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $editingId ? 'Save Changes' : 'Create Location' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
