<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Notes</p>
            <h1>Dharmie reads.</h1>
        </div>
    </header>

    @if ($messages->isEmpty())
        <p class="admin-empty">No notes yet. Contact messages will gather here.</p>
    @else
        <div class="admin-notes">
            @foreach ($messages as $note)
                <a class="admin-note {{ $note->isUnread() ? 'unread' : '' }}" href="{{ route('admin.messages.show', $note) }}" wire:navigate>
                    <div>
                        <strong>{{ $note->name }}</strong>
                        <span class="admin-mute">{{ $note->email }} · {{ $note->created_at->format('d M Y') }}</span>
                        <p>{{ \Illuminate\Support\Str::limit($note->message, 140) }}</p>
                    </div>
                    @if ($note->isUnread())
                        <em>New</em>
                    @endif
                </a>
            @endforeach
        </div>

        @if ($messages->hasPages())
            <div class="admin-pager">
                @if ($messages->onFirstPage())
                    <span class="is-disabled">Previous</span>
                @else
                    <button type="button" wire:click="previousPage">Previous</button>
                @endif
                <span>Page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}</span>
                @if ($messages->hasMorePages())
                    <button type="button" wire:click="nextPage">Next</button>
                @else
                    <span class="is-disabled">Next</span>
                @endif
            </div>
        @endif
    @endif
</div>
