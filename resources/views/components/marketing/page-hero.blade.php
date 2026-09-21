@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<section class="mkt-page-hero">
    <div class="mkt-container mkt-page-hero__inner">
        @if(!empty($breadcrumbs))
            <x-breadcrumbs :items="$breadcrumbs" light="true" />
        @endif
        <div class="mkt-page-hero__copy">
            <h1 class="mkt-page-hero__title">{{ $title }}</h1>
            @if($subtitle)
                <p class="mkt-page-hero__subtitle">{{ $subtitle }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</section>
