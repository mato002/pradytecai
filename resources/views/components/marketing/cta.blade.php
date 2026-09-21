@props([
    'title' => 'Ready to Transform Your Operations?',
    'text' => "Let's discuss the right technology for your business.",
    'primaryHref' => '/contact',
    'primaryLabel' => 'Get Demo',
    'secondaryHref' => '/contact',
    'secondaryLabel' => 'Contact Us',
])

<section class="mkt-cta" aria-labelledby="page-cta-heading">
    <div class="mkt-container mkt-cta__inner">
        <h2 id="page-cta-heading" class="mkt-cta__title">{{ $title }}</h2>
        <p class="mkt-cta__text">{{ $text }}</p>
        <div class="mkt-cta__actions">
            <a href="{{ $primaryHref }}" class="mkt-btn mkt-btn--light">{{ $primaryLabel }}</a>
            <a href="{{ $secondaryHref }}" class="mkt-btn mkt-btn--ghost">{{ $secondaryLabel }}</a>
        </div>
    </div>
</section>
