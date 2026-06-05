@extends('master.front')

@section('title', $article->title)

@section('meta')
    <meta name="description" content="{{ $article->meta_description }}">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->meta_description }}">
    @if($article->hero_image)
        <meta property="og:image" content="{{ url('/core/public/storage/images/' . $article->hero_image) }}">
    @endif
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="page-title">
    <div class="container">
        <ul class="breadcrumbs">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="separator"></li>
            <li><a href="{{ route('education.index') }}">Education</a></li>
            <li class="separator"></li>
            <li>{{ Str::limit($article->title, 50) }}</li>
        </ul>
    </div>
</div>

<div class="container edu-article-layout">
    <div class="row">

        {{-- Main Article --}}
        <main class="col-lg-8">

            {{-- Article Header --}}
            <header class="article-header">
                <span class="article-category">{{ $article->category }}</span>
                <h1 class="article-title">{{ $article->title }}</h1>
                @if($article->meta_description)
                    <p class="article-excerpt">{{ $article->meta_description }}</p>
                @endif
                <div class="article-meta">
                    <span class="article-meta__date">{{ $article->updated_at->format('d M Y') }}</span>
                </div>
            </header>

            {{-- Hero Image --}}
            @if($article->hero_image)
                <div class="article-cover">
                    <img src="{{ url('/core/public/storage/images/' . $article->hero_image) }}"
                         alt="{{ $article->title }}"
                         class="article-cover__img">
                </div>
            @endif

            {{-- Article Body --}}
            <div class="article-body">
                {!! $article->body !!}
            </div>

            {{-- Article Footer --}}
            <div class="article-footer">
                <a href="{{ route('education.index') }}" class="btn-luxury">&larr; Back to Education Centre</a>
                <a href="{{ route('diamonds.index') }}" class="btn-luxury btn-luxury-gold">Search Diamonds &rsaquo;</a>
            </div>

        </main>

        {{-- Sidebar --}}
        <aside class="col-lg-4">

            {{-- 4Cs Quick Nav --}}
            <div class="edu-sidebar-widget">
                <h4 class="edu-sidebar-widget__title">The 4Cs</h4>
                <ul class="edu-sidebar-nav">
                    <li><a href="{{ route('education.guides.show', ['slug' => 'diamond-cut']) }}">{{ __('Cut — pillar page') }}</a></li>
                    <li><a href="{{ route('education.guides.show', ['slug' => 'diamond-colour']) }}">{{ __('Colour — pillar page') }}</a></li>
                    <li><a href="{{ route('education.guides.show', ['slug' => 'diamond-clarity']) }}">{{ __('Clarity — pillar page') }}</a></li>
                    <li><a href="{{ route('education.guides.show', ['slug' => 'diamond-carat']) }}">{{ __('Carat — pillar page') }}</a></li>
                    <li class="small text-muted pt-2"><a href="{{ route('education.index') }}#the-4cs">{{ __('Overview on one page ↓ hub') }}</a></li>
                </ul>
            </div>

            {{-- Related Articles --}}
            @if($related->count())
            <div class="edu-sidebar-widget">
                <h4 class="edu-sidebar-widget__title">Further Reading</h4>
                <div class="edu-related-list">
                    @foreach($related as $rel)
                    <a href="{{ route('education.show', $rel->slug) }}" class="edu-related-card">
                        @if($rel->hero_image)
                            <div class="edu-related-card__img-wrap">
                                <img class="lazy edu-related-card__img"
                                     data-src="{{ url('/core/public/storage/images/' . $rel->hero_image) }}"
                                     alt="{{ $rel->title }}">
                            </div>
                        @else
                            <div class="edu-related-card__img-wrap edu-related-card__img-wrap--placeholder">
                                <span>{{ strtoupper(substr($rel->category, 0, 1)) }}</span>
                            </div>
                        @endif
                        <div class="edu-related-card__body">
                            <span class="edu-related-card__cat">{{ $rel->category }}</span>
                            <p class="edu-related-card__title">{{ $rel->title }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Diamond Search CTA --}}
            <div class="edu-sidebar-cta">
                <p class="edu-sidebar-cta__label">Ready?</p>
                <h4 class="edu-sidebar-cta__title">Find Your Diamond</h4>
                <p class="edu-sidebar-cta__text">Filter 200,000+ GIA &amp; IGI certified stones by Cut, Colour, Clarity and Carat.</p>
                <a href="{{ route('diamonds.index') }}" class="btn-luxury w-100 text-center">Search Diamonds</a>
            </div>

        </aside>

    </div>
</div>

@endsection

@section('styleplugins')
<style>
/* ── Layout ──────────────────────────────────────── */
.edu-article-layout {
    padding-top: 48px;
    padding-bottom: 80px;
}

/* ── Article Header ──────────────────────────────── */
.article-header {
    margin-bottom: 32px;
}
.article-category {
    display: inline-block;
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 12px;
}
.article-title {
    font-family: var(--luxury-serif);
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 400;
    line-height: 1.2;
    color: var(--luxury-black);
    margin-bottom: 16px;
}
.article-excerpt {
    font-size: 16px;
    font-weight: 300;
    line-height: 1.75;
    color: var(--luxury-charcoal);
    margin-bottom: 20px;
}
.article-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: var(--luxury-gray);
    letter-spacing: 0.04em;
    padding-bottom: 20px;
    border-bottom: 1px solid #D4C5A9;
}
.article-meta__read {
    display: flex;
    align-items: center;
    gap: 5px;
}
.article-meta__sep { color: #D4C5A9; }

/* ── Cover Image ─────────────────────────────────── */
.article-cover {
    margin-bottom: 40px;
    overflow: hidden;
}
.article-cover__img {
    width: 100%;
    max-height: 440px;
    object-fit: cover;
    display: block;
}

/* ── Article Body ────────────────────────────────── */
.article-body {
    font-size: 15px;
    line-height: 1.85;
    color: var(--luxury-charcoal);
    margin-bottom: 48px;
}
.article-body h2 {
    font-family: var(--luxury-serif);
    font-size: 26px;
    font-weight: 400;
    margin: 40px 0 16px;
    color: var(--luxury-black);
}
.article-body h3 {
    font-family: var(--luxury-serif);
    font-size: 20px;
    font-weight: 400;
    margin: 32px 0 12px;
    color: var(--luxury-black);
}
.article-body p { margin-bottom: 20px; }
.article-body ul, .article-body ol {
    padding-left: 20px;
    margin-bottom: 20px;
}
.article-body li { margin-bottom: 8px; }
.article-body blockquote {
    border-left: 3px solid var(--luxury-gold);
    margin: 32px 0;
    padding: 16px 24px;
    background: #f5f3ee;
    font-size: 16px;
    font-style: italic;
    color: var(--luxury-charcoal);
}
.article-body img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 32px 0;
}
.article-body table {
    width: 100%;
    border-collapse: collapse;
    margin: 32px 0;
    font-size: 13px;
}
.article-body th {
    background: var(--luxury-black);
    color: #fff;
    padding: 10px 14px;
    text-align: left;
    font-weight: 500;
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.article-body td {
    padding: 10px 14px;
    border-bottom: 1px solid #D4C5A9;
    color: var(--luxury-charcoal);
}
.article-body tr:hover td { background: #faf8f3; }
.article-body a {
    color: var(--luxury-gold);
    text-decoration: underline;
}

/* ── Article Footer ──────────────────────────────── */
.article-footer {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 32px;
    border-top: 1px solid #D4C5A9;
}

/* ── Sidebar ─────────────────────────────────────── */
.edu-sidebar-widget {
    margin-bottom: 40px;
    padding: 28px;
    border: 1px solid #D4C5A9;
    background: #fff;
}
.edu-sidebar-widget__title {
    font-family: var(--luxury-serif);
    font-size: 16px;
    font-weight: 400;
    color: var(--luxury-black);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #D4C5A9;
}
.edu-sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}
.edu-sidebar-nav li { border-bottom: 1px solid #ece8e0; }
.edu-sidebar-nav li:last-child { border-bottom: none; }
.edu-sidebar-nav a {
    display: block;
    padding: 10px 0;
    font-size: 13px;
    color: var(--luxury-charcoal);
    text-decoration: none;
    letter-spacing: 0.02em;
    transition: color 0.2s, padding-left 0.2s;
}
.edu-sidebar-nav a:hover {
    color: var(--luxury-gold);
    padding-left: 4px;
}

/* ── Related Cards ───────────────────────────────── */
.edu-related-list { display: flex; flex-direction: column; gap: 16px; }
.edu-related-card {
    display: flex;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    align-items: flex-start;
}
.edu-related-card:hover { text-decoration: none; }
.edu-related-card__img-wrap {
    width: 72px;
    height: 72px;
    flex-shrink: 0;
    overflow: hidden;
    background: #ece8e0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.edu-related-card__img-wrap--placeholder span {
    font-family: var(--luxury-serif);
    font-size: 24px;
    color: var(--luxury-champagne);
}
.edu-related-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}
.edu-related-card:hover .edu-related-card__img { transform: scale(1.06); }
.edu-related-card__body { flex: 1; }
.edu-related-card__cat {
    display: block;
    font-size: 9px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 4px;
}
.edu-related-card__title {
    font-size: 13px;
    line-height: 1.4;
    color: var(--luxury-black);
    margin: 0 0 4px;
    font-weight: 400;
    transition: color 0.2s;
}
.edu-related-card:hover .edu-related-card__title { color: var(--luxury-gold); }
.edu-related-card__time {
    font-size: 11px;
    color: var(--luxury-gray);
}

/* ── Sidebar CTA ─────────────────────────────────── */
.edu-sidebar-cta {
    background: var(--luxury-black);
    padding: 28px;
    margin-bottom: 40px;
    text-align: center;
}
.edu-sidebar-cta__label {
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--luxury-champagne);
    margin-bottom: 8px;
}
.edu-sidebar-cta__title {
    font-family: var(--luxury-serif);
    font-size: 22px;
    font-weight: 400;
    color: #fff;
    margin-bottom: 10px;
}
.edu-sidebar-cta__text {
    font-size: 13px;
    color: rgba(255,255,255,0.6);
    line-height: 1.6;
    margin-bottom: 20px;
}
</style>
@endsection
