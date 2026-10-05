<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Affiliates & Partners</p>
            <h1>Business Executives</h1>
        </div>
        <button class="btn btn-primary" type="button" wire:click="openCreate">+ New Executive</button>
    </header>

    <!-- Overview Stats -->
    <div class="admin-stats" style="margin-bottom:1.5rem;">
        <article class="stat-card">
            <span class="stat-label">Total Executives</span>
            <strong class="stat-val">{{ number_format($totalExecutivesCount) }}</strong>
            <span class="stat-sub">Active marketing partners</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Referral Sales</span>
            <strong class="stat-val">{{ \App\Models\Product::naira($totalSalesSum) }}</strong>
            <span class="stat-sub">Gross partner volume</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Commissions Earned</span>
            <strong class="stat-val">{{ \App\Models\Product::naira($totalEarnedSum) }}</strong>
            <span class="stat-sub">Total partner rewards</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Pending Payout</span>
            <strong class="stat-val" style="color:var(--terracotta,#b85d38);">{{ \App\Models\Product::naira($totalPendingSum) }}</strong>
            <span class="stat-sub">Awaiting settlement</span>
        </article>
    </div>

    <!-- Toolbar -->
    <div class="admin-toolbar" style="margin-bottom:1.25rem;">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, or code..." aria-label="Search executives">
        <select wire:model.live="statusFilter" aria-label="Filter by status">
            <option value="all">All statuses</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    @if ($executives->isEmpty())
        <div class="admin-form-section" style="text-align:center;padding:3rem 1.5rem;">
            <p class="eyebrow" style="margin-bottom:0.5rem;">No Executives Found</p>
            <p style="color:var(--muted);margin-bottom:1.5rem;">Create your first Business Executive partner to start generating unique product links and tracking commissions.</p>
            <button class="btn btn-primary" type="button" wire:click="openCreate">Create Business Executive</button>
        </div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Executive</th>
                        <th>Referral Code</th>
                        <th>Default Rate</th>
                        <th>Sales Activity</th>
                        <th>Commissions</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($executives as $exec)
                        <tr>
                            <td>
                                <div>
                                    <strong style="color:var(--brown-deep);">{{ $exec->name }}</strong>
                                    <span class="admin-mute">{{ $exec->email }}</span>
                                    @if ($exec->phone)
                                        <span class="admin-mute" style="font-size:0.75rem;">{{ $exec->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code style="font-family:monospace;background:var(--sand);padding:0.2rem 0.5rem;font-size:0.8rem;border:1px solid var(--line);">
                                    {{ $exec->code }}
                                </code>
                            </td>
                            <td>
                                <span>{{ $exec->default_commission_rate }}%</span>
                            </td>
                            <td>
                                <strong>{{ $exec->orders_count }}</strong> <span class="admin-mute" style="display:inline;">orders</span>
                                <span class="admin-mute" style="font-family:monospace;">{{ \App\Models\Product::naira($exec->totalSalesAmount()) }}</span>
                            </td>
                            <td>
                                <strong style="color:var(--brown-deep);font-family:monospace;">{{ \App\Models\Product::naira($exec->totalCommissionEarned()) }}</strong>
                                @if ($exec->pendingCommission() > 0)
                                    <span class="admin-mute" style="color:#925816;font-size:0.75rem;">
                                        Pending: {{ \App\Models\Product::naira($exec->pendingCommission()) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($exec->status === 'active')
                                    <span class="admin-stock-badge in-stock">Active</span>
                                @else
                                    <span class="admin-stock-badge out-of-stock">Suspended</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div class="admin-row-actions">
                                    <button
                                        type="button"
                                        wire:click="viewExecutive({{ $exec->id }})"
                                        style="font-weight:600;color:var(--brown);"
                                    >
                                        Details & Payout
                                    </button>

                                    <button
                                        type="button"
                                        x-data
                                        @click="navigator.clipboard.writeText('{{ $exec->magicUrl() }}'); alert('Magic link copied to clipboard!\n\n{{ $exec->magicUrl() }}')"
                                        title="Copy Magic Access Link"
                                    >
                                        Copy Magic Link
                                    </button>

                                    <a href="{{ $exec->magicUrl() }}" target="_blank" rel="noopener">Portal ↗</a>

                                    <button
                                        type="button"
                                        wire:click="sendMagicLink({{ $exec->id }})"
                                        title="Email fresh magic login link"
                                    >
                                        Send Magic Link
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $exec->id }})"
                                        style="{{ $exec->status === 'active' ? 'color:var(--muted);' : 'color:#27522b;' }}"
                                    >
                                        {{ $exec->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($executives->hasPages())
            <div class="admin-pager">
                <span>Page {{ $executives->currentPage() }} of {{ $executives->lastPage() }}</span>
                <div style="display:flex;gap:0.75rem;">
                    @if ($executives->onFirstPage())
                        <span style="opacity:0.4;cursor:not-allowed;">Previous</span>
                    @else
                        <button type="button" wire:click="previousPage">Previous</button>
                    @endif

                    @if ($executives->hasMorePages())
                        <button type="button" wire:click="nextPage">Next</button>
                    @else
                        <span style="opacity:0.4;cursor:not-allowed;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Create Executive Modal -->
    @if ($showCreateModal)
        <div class="admin-modal-overlay" @click.self="$wire.closeModals()">
            <div class="admin-modal" @click.stop style="max-width:540px;width:95%;border-radius:12px;background:#ffffff;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(28,17,12,0.22);padding:2.25rem;">
                <div class="admin-modal-head" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f0eee9;padding-bottom:1.15rem;margin-bottom:1.35rem;">
                    <div>
                        <p class="eyebrow" style="color:var(--terracotta,#b85d38);margin-bottom:0.15rem;">Affiliate Partnership</p>
                        <h2 style="font-family:var(--font-serif);font-size:1.75rem;margin:0;color:#1c1917;font-weight:500;">New Business Executive</h2>
                    </div>
                    <button type="button" wire:click="closeModals" style="background:none;border:none;font-size:1.75rem;cursor:pointer;color:#78716c;line-height:1;padding:0.25rem;" title="Close modal">&times;</button>
                </div>

                <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;padding:0.85rem 1rem;border-radius:6px;margin-bottom:1.35rem;">
                    <p class="note" style="margin:0;font-size:0.82rem;line-height:1.5;color:var(--muted);">
                        Onboard an affiliate partner. They will receive their dedicated invite link, unique social tracking code, and commission portal access.
                    </p>
                </div>

                <form wire:submit="saveExecutive" class="admin-form" style="gap:1.2rem;">
                    <div>
                        <label for="be-name">Full Name</label>
                        <input
                            id="be-name"
                            type="text"
                            wire:model="name"
                            placeholder="e.g. Oluwadamilola Adebayo"
                            class="admin-input-boxed"
                            required
                            autofocus
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="be-email">Email Address</label>
                        <input
                            id="be-email"
                            type="email"
                            wire:model="email"
                            placeholder="e.g. damilola@example.com"
                            class="admin-input-boxed"
                            required
                        >
                        <span class="note" style="font-size:0.75rem;margin-top:0.3rem;display:block;color:var(--muted);">
                            The executive will use this email to log into their affiliate performance portal.
                        </span>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="be-phone">Phone Number <small style="font-weight:normal;opacity:0.7;text-transform:none;">(or WhatsApp, optional)</small></label>
                        <input
                            id="be-phone"
                            type="text"
                            wire:model="phone"
                            placeholder="+234 801 234 5678"
                            class="admin-input-boxed"
                        >
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;padding:0.75rem 0.95rem;border-radius:6px;">
                        <label class="admin-check" style="margin:0;font-size:0.85rem;">
                            <input type="checkbox" wire:model="send_invite">
                            <span>Send instant onboarding email with portal access link</span>
                        </label>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1.25rem;border-top:1px solid #f0eee9;padding-top:1.15rem;">
                        <button type="button" class="btn btn-ghost" wire:click="closeModals">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="min-width:160px;">Onboard Executive</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail & Payout Modal -->
    @if ($showDetailModal && $selectedExecutive)
        <div class="admin-modal-overlay" @click.self="$wire.closeModals()">
            <div class="admin-modal" @click.stop style="max-width:840px;width:95%;max-height:90vh;overflow-y:auto;background:#ffffff;border-radius:12px;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(28,17,12,0.22);padding:2.25rem;">
                <div class="admin-modal-head" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f0eee9;padding-bottom:1.15rem;margin-bottom:1.35rem;">
                    <div>
                        <p class="eyebrow" style="color:var(--terracotta,#b85d38);margin-bottom:0.15rem;">Executive Performance</p>
                        <h2 style="font-family:var(--font-serif);font-size:1.75rem;margin:0;color:#1c1917;font-weight:500;">{{ $selectedExecutive->name }}</h2>
                        <span class="admin-mute" style="font-size:0.8rem;color:#78716c;margin-top:0.25rem;display:block;">
                            {{ $selectedExecutive->email }} &bull; Referral Code: <code style="background:#faf8f5;border:1px solid #e7e5e4;padding:0.15rem 0.45rem;border-radius:4px;font-size:0.85rem;color:#1c1917;">{{ $selectedExecutive->code }}</code>
                        </span>
                    </div>
                    <button type="button" wire:click="closeModals" style="background:none;border:none;font-size:1.75rem;cursor:pointer;color:#78716c;line-height:1;padding:0.25rem;" title="Close modal">&times;</button>
                </div>

                <!-- Stats summary -->
                <div class="admin-stats" style="grid-template-columns:repeat(3, 1fr);margin-bottom:1.5rem;">
                    <article class="stat-card">
                        <span class="stat-label">Total Commission</span>
                        <strong class="stat-val">{{ \App\Models\Product::naira($selectedExecutive->totalCommissionEarned()) }}</strong>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Paid to Date</span>
                        <strong class="stat-val" style="color:#2e5932;">{{ \App\Models\Product::naira($selectedExecutive->paidCommission()) }}</strong>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Pending Payout</span>
                        <strong class="stat-val" style="color:#b85d38;">{{ \App\Models\Product::naira($selectedExecutive->pendingCommission()) }}</strong>
                    </article>
                </div>

                <!-- Settlement & Bank Details -->
                <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;padding:1.15rem;margin-bottom:1.5rem;border-radius:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
                        <div>
                            <p class="admin-label" style="margin:0;font-weight:600;color:var(--brown);">Banking & Settlement Information</p>
                            @if ($selectedExecutive->bank_account_number)
                                <p style="margin:0.25rem 0 0;font-size:0.9rem;">
                                    <strong>{{ $selectedExecutive->bank_name }}</strong> &bull;
                                    Account: <code>{{ $selectedExecutive->bank_account_number }}</code> ({{ $selectedExecutive->bank_account_name }})
                                </p>
                            @else
                                <p class="note" style="margin:0.25rem 0 0;font-size:0.8rem;color:var(--muted);">No bank account added by executive yet.</p>
                            @endif
                        </div>
                        @if ($selectedExecutive->pendingCommission() > 0)
                            <button
                                type="button"
                                class="btn btn-primary"
                                style="font-size:0.75rem;padding:0.45rem 1rem;"
                                wire:click="markAllPendingPaid({{ $selectedExecutive->id }})"
                                wire:confirm="Mark all pending commissions ({{ \App\Models\Product::naira($selectedExecutive->pendingCommission()) }}) as paid?"
                            >
                                Mark All Pending Paid
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Commission History -->
                <h3 style="font-size:1.1rem;margin-bottom:0.75rem;font-family:var(--font-serif);font-weight:500;">Referral Commission Log</h3>
                @if ($selectedExecutive->commissions->isEmpty())
                    <p class="admin-empty" style="padding:1.5rem;background:#faf8f5;border-radius:8px;border:1px dashed #d7d1c5;">No sales or commissions recorded for this executive yet.</p>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table" style="font-size:0.85rem;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Order #</th>
                                    <th>Sale Amount</th>
                                    <th>Commission</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedExecutive->commissions as $comm)
                                    <tr>
                                        <td style="color:var(--muted);white-space:nowrap;">{{ $comm->created_at->format('d M Y, H:i') }}</td>
                                        <td><strong style="color:var(--brown-deep);">{{ $comm->product_name }}</strong></td>
                                        <td style="font-family:monospace;">#{{ $comm->order_id }}</td>
                                        <td style="font-weight:500;">{{ $comm->formattedSale() }}</td>
                                        <td>
                                            <strong style="color:var(--brown-deep);font-family:monospace;">{{ $comm->formattedCommission() }}</strong>
                                            <span class="admin-mute" style="font-family:monospace;font-size:0.7rem;">({{ $comm->commission_rate }})</span>
                                        </td>
                                        <td>
                                            @if ($comm->status === 'paid')
                                                <span class="admin-stock-badge in-stock">Paid</span>
                                            @else
                                                <span class="admin-stock-badge low-stock">Pending</span>
                                            @endif
                                        </td>
                                        <td style="text-align:right;">
                                            @if ($comm->status !== 'paid')
                                                <button
                                                    type="button"
                                                    class="btn btn-ghost"
                                                    style="padding:0.35rem 0.75rem;font-size:0.75rem;"
                                                    wire:click="markCommissionPaid({{ $comm->id }})"
                                                >
                                                    Mark Paid
                                                </button>
                                            @else
                                                <span style="font-size:0.75rem;color:var(--muted);">Paid {{ $comm->paid_at?->format('d M') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.5rem;border-top:1px solid #f0eee9;padding-top:1rem;flex-wrap:wrap;gap:1rem;">
                    <div style="display:flex;gap:0.75rem;">
                        <button
                            type="button"
                            class="btn btn-ghost"
                            style="font-size:0.8rem;padding:0.45rem 0.9rem;"
                            x-data
                            @click="navigator.clipboard.writeText('{{ $selectedExecutive->magicUrl() }}'); alert('Magic access link copied to clipboard!\n\n{{ $selectedExecutive->magicUrl() }}')"
                        >
                            Copy Magic Link
                        </button>
                        <button
                            type="button"
                            class="btn btn-ghost"
                            style="font-size:0.8rem;padding:0.45rem 0.9rem;"
                            wire:click="sendMagicLink({{ $selectedExecutive->id }})"
                        >
                            Email Magic Link
                        </button>
                    </div>
                    <button type="button" class="btn btn-primary" wire:click="closeModals">Done</button>
                </div>
            </div>
        </div>
    @endif
</div>
