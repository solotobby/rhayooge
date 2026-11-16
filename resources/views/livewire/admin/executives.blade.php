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
        @if ($search !== '' || $statusFilter !== 'all')
            <button
                type="button"
                class="btn btn-ghost"
                wire:click="resetFilters"
                style="font-size:0.75rem;padding:0.45rem 0.85rem;"
            >
                Reset
            </button>
        @endif
    </div>

    @if ($executives->isEmpty())
        <div class="admin-empty" style="padding:3.5rem 1.5rem;text-align:center;">
            <p class="eyebrow" style="margin-bottom:0.5rem;">No Executives Found</p>
            <p style="color:var(--muted);margin-bottom:1.5rem;">Create your first Business Executive partner to start generating unique referral links and tracking commissions.</p>
            <button class="btn btn-primary" type="button" wire:click="openCreate">Create Business Executive</button>
        </div>
    @else
        <!-- Minimalistic Executives Table -->
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width:220px;">Executive</th>
                        <th>Referral Code</th>
                        <th>Sales Activity</th>
                        <th>Commissions</th>
                        <th>Status</th>
                        <th style="text-align:right;min-width:140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($executives as $exec)
                        <tr wire:key="exec-row-{{ $exec->id }}">
                            <td>
                                <div>
                                    <button
                                        type="button"
                                        wire:click="viewExecutive({{ $exec->id }})"
                                        style="font-weight:600;color:var(--brown-deep);background:none;border:none;padding:0;cursor:pointer;text-align:left;font-size:0.88rem;display:block;"
                                    >
                                        {{ $exec->name }}
                                    </button>
                                    <span class="admin-mute" style="font-size:0.75rem;">{{ $exec->email }}</span>
                                    @if ($exec->phone)
                                        <span class="admin-mute" style="font-size:0.72rem;display:block;">{{ $exec->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code style="font-family:monospace;background:#f4f2ee;padding:0.2rem 0.5rem;font-size:0.78rem;border:1px solid #ebe7df;border-radius:4px;color:#44403c;">
                                    {{ $exec->code }}
                                </code>
                            </td>
                            <td>
                                <strong style="font-size:0.85rem;">{{ $exec->orders_count }}</strong>
                                <span class="admin-mute" style="display:inline;font-size:0.78rem;">{{ Str::plural('order', $exec->orders_count) }}</span>
                                <span class="admin-mute" style="font-family:monospace;display:block;font-size:0.75rem;">
                                    {{ \App\Models\Product::naira($exec->totalSalesAmount()) }}
                                </span>
                            </td>
                            <td>
                                <strong style="color:var(--brown-deep);font-family:monospace;font-size:0.88rem;">
                                    {{ \App\Models\Product::naira($exec->totalCommissionEarned()) }}
                                </strong>
                                @if ($exec->pendingCommission() > 0)
                                    <span style="display:block;color:#b85d38;font-size:0.72rem;font-weight:500;">
                                        Pending: {{ \App\Models\Product::naira($exec->pendingCommission()) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($exec->status === 'active')
                                    <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">Active</span>
                                @else
                                    <span class="admin-stock-badge out-of-stock" style="font-size:0.7rem;">Suspended</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:0.4rem;align-items:center;justify-content:flex-end;">
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        wire:click="viewExecutive({{ $exec->id }})"
                                        style="font-size:0.75rem;padding:0.35rem 0.8rem;font-weight:600;"
                                    >
                                        Details
                                    </button>
                                    <a
                                        href="{{ $exec->magicUrl() }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-ghost"
                                        style="font-size:0.75rem;padding:0.35rem 0.75rem;"
                                        title="Open Partner Portal"
                                    >
                                        Portal ↗
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($executives->hasPages())
            <div class="admin-pager" style="margin-top:1.5rem;">
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
            <div class="admin-modal" @click.stop style="max-width:540px;width:95%;border-radius:14px;background:#ffffff;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(28,17,12,0.22);padding:2.25rem;">
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

    <!-- Comprehensive Executive Detail & Payout Modal -->
    @if ($showDetailModal && $selectedExecutive)
        <div class="admin-modal-overlay" @click.self="$wire.closeModals()">
            <div class="admin-modal" @click.stop style="max-width:880px;width:95%;max-height:90vh;overflow-y:auto;background:#ffffff;border-radius:14px;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(28,17,12,0.22);padding:2.25rem;">
                <div class="admin-modal-head" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f0eee9;padding-bottom:1.25rem;margin-bottom:1.5rem;gap:1rem;">
                    <div>
                        <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.25rem;flex-wrap:wrap;">
                            <span class="eyebrow" style="color:var(--terracotta,#b85d38);margin:0;">Executive Profile</span>
                            @if ($selectedExecutive->status === 'active')
                                <span class="admin-stock-badge in-stock" style="font-size:0.7rem;">● Active Partner</span>
                            @else
                                <span class="admin-stock-badge out-of-stock" style="font-size:0.7rem;">● Suspended</span>
                            @endif
                        </div>
                        <h2 style="font-family:var(--font-serif);font-size:1.85rem;margin:0;color:#1c1917;font-weight:500;">{{ $selectedExecutive->name }}</h2>
                        <span class="admin-mute" style="font-size:0.8rem;color:#78716c;margin-top:0.25rem;display:block;">
                            {{ $selectedExecutive->email }}
                            @if ($selectedExecutive->phone) &bull; {{ $selectedExecutive->phone }} @endif
                            &bull; Referral Code: <code style="background:#faf8f5;border:1px solid #e7e5e4;padding:0.15rem 0.45rem;border-radius:4px;font-size:0.82rem;color:#1c1917;">{{ $selectedExecutive->code }}</code>
                            &bull; Default Commission: <strong>{{ $selectedExecutive->default_commission_rate }}%</strong>
                        </span>
                    </div>

                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <button
                            type="button"
                            class="btn btn-ghost"
                            style="font-size:0.75rem;padding:0.45rem 0.85rem;{{ $selectedExecutive->status === 'active' ? 'color:#b85d38;' : 'color:#2e5932;' }}"
                            wire:click="toggleStatus({{ $selectedExecutive->id }})"
                        >
                            {{ $selectedExecutive->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                        <button type="button" wire:click="closeModals" style="background:none;border:none;font-size:1.6rem;cursor:pointer;color:#78716c;line-height:1;margin-left:0.5rem;padding:0.25rem;" title="Close modal">&times;</button>
                    </div>
                </div>

                <!-- Partner Links & Portals Access Banner -->
                <div style="background:var(--ivory,#faf8f5);border:1px solid #ebe7df;padding:1.15rem;border-radius:8px;margin-bottom:1.5rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.85rem;">
                        <div>
                            <span class="admin-label" style="font-size:0.7rem;color:var(--brown);margin:0;display:block;">Customer Storefront Referral Link</span>
                            <code style="font-size:0.82rem;color:var(--charcoal);word-break:break-all;">{{ $selectedExecutive->storeReferralUrl() }}</code>
                        </div>
                        <button
                            type="button"
                            class="btn btn-ghost"
                            style="font-size:0.75rem;padding:0.35rem 0.75rem;"
                            x-data
                            @click="navigator.clipboard.writeText('{{ $selectedExecutive->storeReferralUrl() }}'); alert('Storefront referral link copied to clipboard!')"
                        >
                            Copy Referral Link
                        </button>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;border-top:1px dashed #e5e2db;padding-top:0.85rem;">
                        <div>
                            <span class="admin-label" style="font-size:0.7rem;color:var(--brown);margin:0;display:block;">Executive Private Portal</span>
                            <span style="font-size:0.8rem;color:var(--muted);">Personal dashboard for tracking earnings and generating links</span>
                        </div>
                        <div style="display:flex;gap:0.5rem;align-items:center;">
                            <button
                                type="button"
                                class="btn btn-ghost"
                                style="font-size:0.75rem;padding:0.35rem 0.75rem;"
                                x-data
                                @click="navigator.clipboard.writeText('{{ $selectedExecutive->magicUrl() }}'); alert('Magic portal access link copied!')"
                            >
                                Copy Magic Link
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost"
                                style="font-size:0.75rem;padding:0.35rem 0.75rem;"
                                wire:click="sendMagicLink({{ $selectedExecutive->id }})"
                            >
                                Email Link
                            </button>
                            <a
                                href="{{ $selectedExecutive->magicUrl() }}"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-primary"
                                style="font-size:0.75rem;padding:0.35rem 0.85rem;"
                            >
                                Open Portal ↗
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats summary -->
                <div class="admin-stats" style="grid-template-columns:repeat(4, 1fr);margin-bottom:1.5rem;">
                    <article class="stat-card">
                        <span class="stat-label">Referral Orders</span>
                        <strong class="stat-val">{{ $selectedExecutive->orders_count ?? $selectedExecutive->orders()->count() }}</strong>
                        <span class="stat-sub">{{ \App\Models\Product::naira($selectedExecutive->totalSalesAmount()) }} volume</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Total Earned</span>
                        <strong class="stat-val">{{ \App\Models\Product::naira($selectedExecutive->totalCommissionEarned()) }}</strong>
                        <span class="stat-sub">Lifetime commission</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Paid to Date</span>
                        <strong class="stat-val" style="color:#2e5932;">{{ \App\Models\Product::naira($selectedExecutive->paidCommission()) }}</strong>
                        <span class="stat-sub">Settled payouts</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Pending Payout</span>
                        <strong class="stat-val" style="color:#b85d38;">{{ \App\Models\Product::naira($selectedExecutive->pendingCommission()) }}</strong>
                        <span class="stat-sub">Awaiting transfer</span>
                    </article>
                </div>

                <!-- Settlement & Bank Details -->
                <div style="background:#ffffff;border:1px solid #ebe7df;padding:1.15rem;margin-bottom:1.5rem;border-radius:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
                        <div>
                            <p class="admin-label" style="margin:0;font-size:0.7rem;color:var(--brown);">Banking & Settlement Information</p>
                            @if ($selectedExecutive->bank_account_number)
                                <p style="margin:0.25rem 0 0;font-size:0.9rem;">
                                    <strong>{{ $selectedExecutive->bank_name }}</strong> &bull;
                                    Account: <code style="font-family:monospace;font-weight:600;">{{ $selectedExecutive->bank_account_number }}</code> ({{ $selectedExecutive->bank_account_name }})
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
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                    <h3 style="font-size:1.1rem;margin:0;font-family:var(--font-serif);font-weight:500;">Referral Commission Log</h3>
                    <span class="admin-mute" style="font-size:0.78rem;">{{ $selectedExecutive->commissions->count() }} {{ Str::plural('record', $selectedExecutive->commissions->count()) }}</span>
                </div>

                @if ($selectedExecutive->commissions->isEmpty())
                    <p class="admin-empty" style="padding:1.75rem;background:#faf8f5;border-radius:8px;border:1px dashed #d7d1c5;text-align:center;">No sales or commissions recorded for this executive yet.</p>
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
                                    <tr wire:key="comm-row-{{ $comm->id }}">
                                        <td style="color:var(--muted);white-space:nowrap;font-size:0.78rem;">{{ $comm->created_at->format('d M Y, H:i') }}</td>
                                        <td><strong style="color:var(--brown-deep);">{{ $comm->product_name }}</strong></td>
                                        <td style="font-family:monospace;font-size:0.8rem;">#{{ $comm->order_id }}</td>
                                        <td style="font-weight:500;font-family:monospace;">{{ $comm->formattedSale() }}</td>
                                        <td>
                                            <strong style="color:var(--brown-deep);font-family:monospace;">{{ $comm->formattedCommission() }}</strong>
                                            <span class="admin-mute" style="font-family:monospace;font-size:0.7rem;">({{ $comm->commission_rate }})</span>
                                        </td>
                                        <td>
                                            @if ($comm->status === 'paid')
                                                <span class="admin-stock-badge in-stock" style="font-size:0.68rem;">Paid</span>
                                            @else
                                                <span class="admin-stock-badge low-stock" style="font-size:0.68rem;">Pending</span>
                                            @endif
                                        </td>
                                        <td style="text-align:right;">
                                            @if ($comm->status !== 'paid')
                                                <button
                                                    type="button"
                                                    class="btn btn-ghost"
                                                    style="padding:0.3rem 0.65rem;font-size:0.72rem;"
                                                    wire:click="markCommissionPaid({{ $comm->id }})"
                                                >
                                                    Mark Paid
                                                </button>
                                            @else
                                                <span style="font-size:0.72rem;color:var(--muted);">Paid {{ $comm->paid_at?->format('d M') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.75rem;border-top:1px solid #f0eee9;padding-top:1.25rem;">
                    <div>
                        @if ($selectedExecutive->orders()->count() === 0 && $selectedExecutive->commissions()->count() === 0)
                            <button
                                type="button"
                                wire:click="deleteExecutive({{ $selectedExecutive->id }})"
                                wire:confirm="Permanently remove {{ $selectedExecutive->name }} from business executives?"
                                class="btn-delete"
                                style="font-size:0.78rem;background:none;border:1px solid #fecaca;color:#dc2626;padding:0.45rem 0.85rem;border-radius:4px;cursor:pointer;"
                            >
                                Delete Executive
                            </button>
                        @endif
                    </div>
                    <button type="button" class="btn btn-primary" wire:click="closeModals">Done</button>
                </div>
            </div>
        </div>
    @endif
</div>
