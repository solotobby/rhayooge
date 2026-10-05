<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Clients</p>
            <h1>The book of names.</h1>
        </div>
    </header>

    <div class="admin-toolbar">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, or phone" aria-label="Search clients">
    </div>

    @if ($clients->isEmpty())
        <p class="admin-empty">No client profiles yet.</p>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th style="text-align:right;">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>
                                <div>
                                    <strong style="color:var(--brown-deep);">{{ $client->name }}</strong>
                                    <span class="admin-mute">{{ $client->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-family:monospace;color:var(--charcoal);">{{ $client->phone ?: '—' }}</span>
                            </td>
                            <td>
                                <span>{{ $client->orders_count }} {{ Str::plural('order', $client->orders_count) }}</span>
                            </td>
                            <td style="text-align:right;color:var(--muted);">
                                {{ $client->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="admin-pager">
                <span>Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }}</span>
                <div style="display:flex;gap:0.75rem;">
                    @if ($clients->onFirstPage())
                        <span style="opacity:0.4;cursor:not-allowed;">Previous</span>
                    @else
                        <button type="button" wire:click="previousPage">Previous</button>
                    @endif

                    @if ($clients->hasMorePages())
                        <button type="button" wire:click="nextPage">Next</button>
                    @else
                        <span style="opacity:0.4;cursor:not-allowed;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
