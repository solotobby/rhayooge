<div class="contact-page">
    <header class="page-intro">
        <div class="container">
            <p class="eyebrow">Contact us</p>
            <h1>We would love to hear from you.</h1>
            <p class="shop-lead">Fittings, orders, or a simple hello — Dharmie reads every note.</p>
        </div>
    </header>

    <section class="section contact-shell">
        <div class="container contact-grid">
            @if ($sent)
                <div class="contact-thanks">
                    <p class="eyebrow">Received</p>
                    <h2>Thank you.</h2>
                    <p>A reply is on its way. Until then, the collection is open.</p>
                    <a class="btn btn-primary" href="{{ route('shop') }}" wire:navigate>Continue shopping</a>
                </div>
            @else
                <form class="form contact-form" wire:submit="send">
                    <div>
                        <label for="c-name">Name</label>
                        <input id="c-name" type="text" wire:model="name" autocomplete="name">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="c-email">Email</label>
                        <input id="c-email" type="email" wire:model="email" autocomplete="email">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="c-msg">Message</label>
                        <textarea id="c-msg" wire:model="message" placeholder="A fitting question, an order note, a hello…"></textarea>
                        @error('message') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <button class="btn btn-primary" type="submit">Send message</button>
                </form>
            @endif

            <aside class="contact-details">
                <p class="eyebrow">Dharmie</p>
                <h2>Victoria Island</h2>
                <p>12 Kofo Abayomi Street<br>Lagos, Nigeria</p>
                <p class="eyebrow">Hours</p>
                <p>Monday–Saturday, 10:00–19:00<br>Private fittings by appointment</p>
                <p class="eyebrow">Write</p>
                <p><a href="mailto:hello@rhayooge.com">hello@rhayooge.com</a><br><a href="tel:+2348000000000">+234 800 000 0000</a></p>
            </aside>
        </div>
    </section>
</div>
