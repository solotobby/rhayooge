<div>
    <section class="hero">
        <div class="hero-content">
            <p class="eyebrow">New season · Lagos</p>
            <h1>Dressed in warmth.</h1>
            <p>A house of feminine apparel — earth, silk, and ease. Pieces made to be lived in, remembered, and passed on.</p>
            <div class="hero-actions">
                <a class="btn btn-accent" href="{{ route('shop') }}" wire:navigate>Shop the collection</a>
                <a class="btn btn-on-dark" href="{{ route('about') }}" wire:navigate>Our story</a>
            </div>
        </div>
        <span class="hero-scroll" aria-hidden="true">Scroll</span>
    </section>

    <div class="marquee" aria-hidden="true">
        <div class="marquee-track">
            <span>Considered cuts</span><span>RHÁYỌ̀OGE</span><span>Dharmie</span><span>Earth pigments</span>
            <span>Feminine ease</span><span>Quiet luxury</span><span>Natural fibre</span><span>Made to linger</span>
            <span>Considered cuts</span><span>RHÁYỌ̀OGE</span><span>Dharmie</span><span>Earth pigments</span>
            <span>Feminine ease</span><span>Quiet luxury</span><span>Natural fibre</span><span>Made to linger</span>
        </div>
    </div>

    <section class="home-intro">
        <div class="container">
            <p class="eyebrow">The house</p>
            <p class="home-intro-line">Quiet luxury, cut for the woman who already knows her presence.</p>
        </div>
    </section>

    <section class="section lookbook">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Lookbook</p>
                    <h2>Shop by mood</h2>
                </div>
                <a class="btn btn-ghost" href="{{ route('shop') }}" wire:navigate>The collection</a>
            </div>
            <div class="collection-grid">
                <a class="collection-card" href="{{ route('shop', ['category' => 'Dresses']) }}" wire:navigate>
                    <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=1200&q=80" alt="Woman in a terracotta wrap set" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div class="copy">
                        <span class="eyebrow">Dresses & sets</span>
                        <strong>The gathering</strong>
                    </div>
                </a>
                <a class="collection-card" href="{{ route('shop', ['category' => 'Outerwear']) }}" wire:navigate>
                    <img src="https://images.unsplash.com/photo-1520975661595-6453be3f7070?auto=format&fit=crop&w=900&q=80" alt="Camel trench coat" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div class="copy">
                        <span class="eyebrow">Outerwear</span>
                        <strong>Heritage</strong>
                    </div>
                </a>
                <a class="collection-card" href="{{ route('shop', ['category' => 'Accessories']) }}" wire:navigate>
                    <img src="https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=80" alt="Gold hoop earrings" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div class="copy">
                        <span class="eyebrow">Finishing</span>
                        <strong>Gold notes</strong>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="section featured-band">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow">This week</p>
                    <h2>Featured pieces</h2>
                </div>
                <a class="btn btn-ghost" href="{{ route('shop') }}" wire:navigate>Shop all</a>
            </div>
            <div class="product-grid featured">
                @foreach ($featured as $index => $product)
                    <x-product-card :product="$product" :saved="in_array($product->id, $savedIds, true)" :delay="$index * 0.06" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="atelier-band">
        <div class="container atelier-band-grid">
            <div>
                <p class="eyebrow">The house</p>
                <h2>Elegance with a pulse.</h2>
                <p>RHÁYỌ̀OGE is named in the cadence of home. We design for women who want presence without performance — warm palettes, precise cuts, and clothes that feel as considered as they look.</p>
                <a class="btn btn-light" href="{{ route('about') }}" wire:navigate>About us</a>
            </div>
            <figure class="atelier-portrait">
                <img src="{{ asset('assets/brand-guide.png') }}" alt="RHÁYỌ̀OGE brand imagery">
            </figure>
        </div>
    </section>

    <section class="visit-strip">
        <div class="container visit-strip-grid">
            <div>
                <p class="eyebrow">Visit</p>
                <p>Victoria Island, Lagos · Mon–Sat, 10am–7pm</p>
            </div>
            <a class="btn btn-ghost" href="{{ route('contact') }}" wire:navigate>Book a fitting</a>
        </div>
    </section>
</div>
