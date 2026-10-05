<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Note</p>
            <h1>{{ $message->name }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.messages') }}" wire:navigate>All notes</a>
    </header>

    <section class="admin-panel admin-letter">
        <p class="admin-mute">{{ $message->email }} · {{ $message->created_at->format('d M Y, H:i') }}</p>
        <p class="admin-letter-body">{{ $message->message }}</p>
        <div class="admin-letter-actions">
            <a class="btn btn-primary" href="mailto:{{ $message->email }}">Reply by email</a>
            <button class="btn btn-ghost" type="button" wire:click="delete" wire:confirm="Dismiss this note?">Dismiss</button>
        </div>
    </section>
</div>
