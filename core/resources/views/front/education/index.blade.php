@extends('master.front')

@section('title', 'Diamond Education Centre')

@section('meta')
    <meta name="description" content="Learn everything about diamonds — the 4Cs of Cut, Colour, Clarity and Carat Weight. Expert guides to help you choose the perfect diamond with confidence.">
    <meta property="og:title" content="Diamond Education Centre">
    <meta property="og:description" content="Understand Cut, Colour, Clarity and Carat. Expert guides from our certified gemologists.">
@endsection

@section('content')

{{-- Page Hero --}}
<div class="edu-hero">
    <div class="edu-hero__overlay"></div>
    <div class="edu-hero__content">
        <p class="edu-eyebrow">GIA Certified Experts</p>
        <h1 class="edu-hero__title">The Diamond Education Centre</h1>
        <p class="edu-hero__subtitle">Everything you need to choose a diamond with clarity and confidence. Our gemologists break down the 4Cs so every decision feels informed, not overwhelming.</p>
        <a href="#the-4cs" class="btn-luxury btn-luxury-outline-white">{{ __('Explore the 4Cs') }}</a>
        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
            <a href="{{ route('education.tool.4cs') }}" class="btn-luxury btn-luxury-outline-white">{{ __('Interactive 4Cs') }}</a>
            <a href="{{ route('education.compliance') }}" class="btn-luxury btn-luxury-outline-white">{{ __('Trust & compliance') }}</a>
        </div>
    </div>
</div>

@if (isset($pillars) && is_array($pillars) && count($pillars) > 0)
<section class="edu-section container edu-pillar-quicklinks pb-3">
    <div class="edu-section__header text-center">
        <p class="edu-label">{{ __('Pillar library') }}</p>
        <h2 class="edu-section__title">{{ __('Deep-dive guides (pillar library)') }}</h2>
        <p class="edu-section__intro">{{ __('Standalone URLs for Cut, Colour, Clarity, Carat, settings, metals—built for editorial SEO and reassurance copy.') }}</p>
    </div>
    <div class="edu-pillar-chips flex-wrap gap-2 d-flex justify-content-center">
        @foreach ($pillars as $pillarSlug => $pillarMeta)
            <a href="{{ route('education.guides.show', $pillarSlug) }}" class="edu-pillar-chip">{{ $pillarMeta['title'] }}</a>
        @endforeach
    </div>
</section>
@endif

<x-education-experts />

{{-- 4Cs Introduction --}}
<section class="edu-section container" id="the-4cs">
    <div class="edu-section__header text-center">
        <p class="edu-label">The Foundation</p>
        <h2 class="edu-section__title">The 4Cs of Diamond Quality</h2>
        <p class="edu-section__intro">Developed by the Gemological Institute of America, the 4Cs are the universal language for describing and comparing any diamond in the world.</p>
    </div>

    <div class="fourcs-grid">

        {{-- Cut --}}
        <a href="#cut-guide" class="fourcs-card fourcs-card--cut">
            <div class="fourcs-card__icon">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="32,4 60,24 50,58 14,58 4,24" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <polygon points="32,4 60,24 32,18" stroke="currentColor" stroke-width="1" fill="none" opacity="0.5"/>
                    <polygon points="32,4 4,24 32,18" stroke="currentColor" stroke-width="1" fill="none" opacity="0.5"/>
                    <line x1="32" y1="18" x2="50" y2="58" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                    <line x1="32" y1="18" x2="14" y2="58" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                    <line x1="32" y1="18" x2="32" y2="58" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                </svg>
            </div>
            <div class="fourcs-card__body">
                <span class="fourcs-card__label">The First C</span>
                <h3 class="fourcs-card__title">Cut</h3>
                <p class="fourcs-card__text">The most important of the 4Cs. Cut determines how a diamond interacts with light — its brilliance, fire, and scintillation. A perfectly cut diamond transforms light into an extraordinary display.</p>
                <span class="fourcs-card__cta">Explore Cut &rsaquo;</span>
            </div>
        </a>

        {{-- Colour --}}
        <a href="#colour-guide" class="fourcs-card fourcs-card--colour">
            <div class="fourcs-card__icon">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                    <circle cx="32" cy="32" r="10" stroke="currentColor" stroke-width="1" opacity="0.3"/>
                    <line x1="6" y1="32" x2="58" y2="32" stroke="currentColor" stroke-width="1" opacity="0.4"/>
                    <line x1="32" y1="6" x2="32" y2="58" stroke="currentColor" stroke-width="1" opacity="0.4"/>
                </svg>
            </div>
            <div class="fourcs-card__body">
                <span class="fourcs-card__label">The Second C</span>
                <h3 class="fourcs-card__title">Colour</h3>
                <p class="fourcs-card__text">The GIA colour scale runs D (colourless) to Z (light yellow). The less colour, the rarer and more valuable the diamond. Colourless diamonds allow more light to pass through, creating more sparkle.</p>
                <span class="fourcs-card__cta">Explore Colour &rsaquo;</span>
            </div>
        </a>

        {{-- Clarity --}}
        <a href="#clarity-guide" class="fourcs-card fourcs-card--clarity">
            <div class="fourcs-card__icon">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="8" y="8" width="48" height="48" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="16" y="16" width="32" height="32" rx="1" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                    <circle cx="26" cy="26" r="2" fill="currentColor" opacity="0.4"/>
                    <circle cx="40" cy="38" r="1.5" fill="currentColor" opacity="0.3"/>
                    <line x1="8" y1="32" x2="56" y2="32" stroke="currentColor" stroke-width="0.75" opacity="0.3" stroke-dasharray="3 3"/>
                </svg>
            </div>
            <div class="fourcs-card__body">
                <span class="fourcs-card__label">The Third C</span>
                <h3 class="fourcs-card__title">Clarity</h3>
                <p class="fourcs-card__text">Clarity measures the presence of internal inclusions and external blemishes. Graded from Flawless to Included, most imperfections are invisible to the naked eye — an eye-clean diamond is the practical goal.</p>
                <span class="fourcs-card__cta">Explore Clarity &rsaquo;</span>
            </div>
        </a>

        {{-- Carat --}}
        <a href="#carat-guide" class="fourcs-card fourcs-card--carat">
            <div class="fourcs-card__icon">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 8 L56 28 L32 56 L8 28 Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <path d="M32 8 L56 28 L32 28 Z" stroke="currentColor" stroke-width="1" fill="none" opacity="0.4"/>
                    <path d="M32 8 L8 28 L32 28 Z" stroke="currentColor" stroke-width="1" fill="none" opacity="0.4"/>
                    <line x1="8" y1="28" x2="56" y2="28" stroke="currentColor" stroke-width="1" opacity="0.4"/>
                </svg>
            </div>
            <div class="fourcs-card__body">
                <span class="fourcs-card__label">The Fourth C</span>
                <h3 class="fourcs-card__title">Carat</h3>
                <p class="fourcs-card__text">Carat is the unit of weight for diamonds — one carat equals 0.2 grams. Larger diamonds are rarer, so price increases exponentially with carat weight. Two diamonds of the same carat can differ greatly in value based on cut, colour, and clarity.</p>
                <span class="fourcs-card__cta">Explore Carat &rsaquo;</span>
            </div>
        </a>

    </div>
</section>

{{-- Cut In-Depth --}}
<section class="edu-section edu-section--alt" id="cut-guide">
    <div class="container">
        <div class="edu-deep-header">
            <p class="edu-label">In Depth</p>
            <h2 class="edu-section__title">Understanding Cut</h2>
        </div>
        <div class="edu-deep-body">
            <p class="edu-lead">Cut is the only C entirely determined by human craftsmanship. It directly governs the three visual properties that make a diamond breathtaking.</p>
            <div class="cut-trio">
                <div class="cut-trio__item">
                    <span class="cut-trio__word">Brilliance</span>
                    <p>The total light reflected from a diamond — white light bouncing back to your eye from within the stone.</p>
                </div>
                <div class="cut-trio__item">
                    <span class="cut-trio__word">Fire</span>
                    <p>The dispersion of light into the colours of the spectrum — the rainbow flashes you see as the diamond moves.</p>
                </div>
                <div class="cut-trio__item">
                    <span class="cut-trio__word">Scintillation</span>
                    <p>The sparkle you see as the diamond, the light, or your eye moves — the pattern of light and dark areas.</p>
                </div>
            </div>

            <h3 class="edu-subheading">GIA Cut Grades — Round Brilliant</h3>
            <div class="grade-scale">
                <div class="grade-scale__item grade-scale__item--best">
                    <span class="grade-scale__name">Ideal / Excellent</span>
                    <p class="grade-scale__desc">Reflects almost all light that enters. Exceptional brilliance and fire. The highest grade — chosen for its uncompromising beauty.</p>
                    <span class="grade-scale__badge">Recommended</span>
                </div>
                <div class="grade-scale__item">
                    <span class="grade-scale__name">Very Good</span>
                    <p class="grade-scale__desc">Reflects most light that enters. Slight deviations from the ideal proportions, barely perceptible to the naked eye. Excellent value.</p>
                </div>
                <div class="grade-scale__item">
                    <span class="grade-scale__name">Good</span>
                    <p class="grade-scale__desc">Reflects a significant amount of light. Prioritising carat or budget over cut quality. Still beautiful in person.</p>
                </div>
                <div class="grade-scale__item">
                    <span class="grade-scale__name">Fair / Poor</span>
                    <p class="grade-scale__desc">Light leaks noticeably from the bottom or sides. The diamond appears dull compared to better-cut stones. Generally not recommended.</p>
                </div>
            </div>
            <p class="edu-tip"><strong>Our recommendation:</strong> For round brilliant diamonds, choose Excellent or Ideal cut. For fancy shapes (oval, cushion, pear), there is no official GIA cut grade — look for well-proportioned examples with strong light performance.</p>
        </div>
    </div>
</section>

{{-- Colour In-Depth --}}
<section class="edu-section" id="colour-guide">
    <div class="container">
        <div class="edu-deep-header">
            <p class="edu-label">In Depth</p>
            <h2 class="edu-section__title">Understanding Colour</h2>
        </div>
        <div class="edu-deep-body">
            <p class="edu-lead">The GIA colour grading scale begins at D (completely colourless) and ends at Z (noticeable light yellow or brown). The difference between adjacent grades is often invisible to the untrained eye.</p>
            <div class="colour-scale">
                <div class="colour-scale__group">
                    <span class="colour-scale__range">D – F</span>
                    <span class="colour-scale__tier">Colourless</span>
                    <p class="colour-scale__desc">Chemically pure and structurally perfect. Extremely rare and valuable. D is the finest possible grade.</p>
                </div>
                <div class="colour-scale__group">
                    <span class="colour-scale__range">G – J</span>
                    <span class="colour-scale__tier">Near Colourless</span>
                    <p class="colour-scale__desc">Colour is difficult to detect unless compared side-by-side with a master stone. Exceptional value — G and H are the most popular choices.</p>
                    <span class="colour-scale__badge">Best Value</span>
                </div>
                <div class="colour-scale__group">
                    <span class="colour-scale__range">K – M</span>
                    <span class="colour-scale__tier">Faint</span>
                    <p class="colour-scale__desc">Faint yellow visible. When set in yellow gold, the warmth can complement the metal beautifully.</p>
                </div>
                <div class="colour-scale__group colour-scale__group--muted">
                    <span class="colour-scale__range">N – Z</span>
                    <span class="colour-scale__tier">Very Light – Light</span>
                    <p class="colour-scale__desc">Noticeable yellow or brown. Significantly reduced brilliance. Rarely recommended for engagement rings.</p>
                </div>
            </div>
            <p class="edu-tip"><strong>Metal pairing tip:</strong> If set in white gold or platinum, choose G–H or better. In yellow gold, K–M can look beautiful at a much lower price point. Always view the diamond in the setting metal before purchasing.</p>
        </div>
    </div>
</section>

{{-- Clarity In-Depth --}}
<section class="edu-section edu-section--alt" id="clarity-guide">
    <div class="container">
        <div class="edu-deep-header">
            <p class="edu-label">In Depth</p>
            <h2 class="edu-section__title">Understanding Clarity</h2>
        </div>
        <div class="edu-deep-body">
            <p class="edu-lead">Virtually all diamonds contain naturally occurring internal characteristics called inclusions and surface characteristics called blemishes. The GIA clarity scale has 6 categories and 11 grades.</p>
            <div class="clarity-table">
                <div class="clarity-row clarity-row--header">
                    <span>Grade</span><span>Name</span><span>What it means</span>
                </div>
                @foreach([
                    ['FL', 'Flawless', 'No inclusions or blemishes visible under 10× magnification. Extremely rare — fewer than 1% of all diamonds.'],
                    ['IF', 'Internally Flawless', 'No inclusions visible under 10× magnification. Only minor surface blemishes. Exceptionally rare.'],
                    ['VVS1 – VVS2', 'Very Very Slightly Included', 'Inclusions so slight they are difficult for a skilled grader to see under 10×. Invisible to the naked eye.'],
                    ['VS1 – VS2', 'Very Slightly Included', 'Minor inclusions visible under 10× magnification but invisible to the naked eye. Excellent practical choice.'],
                    ['SI1 – SI2', 'Slightly Included', 'Inclusions noticeable under 10×. SI1 is often eye-clean; SI2 may have inclusions visible without magnification.'],
                    ['I1 – I3', 'Included', 'Inclusions obvious under 10× and usually visible to the naked eye. May affect transparency and brilliance.'],
                ] as [$grade, $name, $desc])
                <div class="clarity-row {{ $grade === 'VS1 – VS2' ? 'clarity-row--highlight' : '' }}">
                    <span class="clarity-grade">{{ $grade }}</span>
                    <span class="clarity-name">{{ $name }}</span>
                    <span class="clarity-desc">{{ $desc }}</span>
                    @if($grade === 'VS1 – VS2')<span class="clarity-badge">Sweet Spot</span>@endif
                </div>
                @endforeach
            </div>
            <p class="edu-tip"><strong>The eye-clean principle:</strong> We recommend VS1–VS2 as the ideal balance of quality and value. An eye-clean SI1 can offer outstanding savings — always request an inclusion plot or HD image to verify before purchasing.</p>
        </div>
    </div>
</section>

{{-- Carat In-Depth --}}
<section class="edu-section" id="carat-guide">
    <div class="container">
        <div class="edu-deep-header">
            <p class="edu-label">In Depth</p>
            <h2 class="edu-section__title">Understanding Carat Weight</h2>
        </div>
        <div class="edu-deep-body">
            <p class="edu-lead">One carat equals exactly 200 milligrams (0.2 grams). Each carat divides into 100 points — a 0.75 ct diamond is described as "seventy-five points." Larger diamonds are exponentially rarer: a 2 ct stone costs far more than two 1 ct diamonds of comparable quality.</p>

            <h3 class="edu-subheading">Approximate Face-Up Diameter (Round Brilliant)</h3>
            <div class="carat-visual">
                @foreach([
                    ['0.25 ct', '4.1 mm'],
                    ['0.50 ct', '5.2 mm'],
                    ['0.75 ct', '5.9 mm'],
                    ['1.00 ct', '6.5 mm'],
                    ['1.50 ct', '7.4 mm'],
                    ['2.00 ct', '8.2 mm'],
                    ['3.00 ct', '9.4 mm'],
                ] as [$carat, $diameter])
                @php $px = (float)$diameter * 5; @endphp
                <div class="carat-visual__item">
                    <div class="carat-visual__circle" style="width:{{ $px }}px; height:{{ $px }}px;"></div>
                    <span class="carat-visual__weight">{{ $carat }}</span>
                    <span class="carat-visual__mm">{{ $diameter }}</span>
                </div>
                @endforeach
            </div>
            <p class="edu-tip"><strong>Magic sizes:</strong> Diamonds just below round numbers (0.90 ct, 1.45 ct, 1.90 ct) offer significantly better value than their just-above counterparts, with a face-up size difference imperceptible to the eye. Always weigh carat against cut — a well-cut 0.90 ct will outshine a poorly-cut 1.10 ct.</p>
        </div>
    </div>
</section>

{{-- Dynamic Articles --}}
@if($articles->count())
<section class="edu-section edu-section--alt">
    <div class="container">
        <div class="edu-section__header text-center">
            <p class="edu-label">Further Reading</p>
            <h2 class="edu-section__title">Buying Guides & Expert Advice</h2>
        </div>
        <div class="edu-articles-grid">
            @foreach($articles as $article)
            <a href="{{ route('education.show', $article->slug) }}" class="edu-article-card">
                @if($article->hero_image)
                    <div class="edu-article-card__img-wrap">
                        <img class="lazy edu-article-card__img"
                             data-src="{{ url('/core/public/storage/images/' . $article->hero_image) }}"
                             alt="{{ $article->title }}">
                    </div>
                @else
                    <div class="edu-article-card__img-wrap edu-article-card__img-wrap--placeholder">
                        <span class="edu-article-card__category-icon">{{ strtoupper(substr($article->category, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="edu-article-card__body">
                    <span class="edu-article-card__category">{{ $article->category }}</span>
                    <h3 class="edu-article-card__title">{{ $article->title }}</h3>
                    @if($article->meta_description)
                        <p class="edu-article-card__excerpt">{{ Str::limit($article->meta_description, 120) }}</p>
                    @endif
                    <span class="edu-article-card__meta">Read article &rsaquo;</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Strip --}}
<section class="edu-cta-strip">
    <div class="container text-center">
        <p class="edu-label">Ready to Choose?</p>
        <h2 class="edu-cta-strip__title">Browse Our Diamond Collection</h2>
        <p class="edu-cta-strip__sub">Filter by Cut, Colour, Clarity and Carat — every stone GIA or IGI certified.</p>
        <a href="{{ route('diamonds.index') }}" class="btn-luxury btn-luxury-gold">Search Diamonds</a>
    </div>
</section>

@endsection

@section('styleplugins')
<style>
/* ── Education Hero ──────────────────────────────── */
.edu-hero {
    position: relative;
    min-height: 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--luxury-black);
    overflow: hidden;
}
.edu-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(10,10,10,0.4) 100%);
}
.edu-hero__content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: #fff;
    padding: 80px 24px;
    max-width: 720px;
}
.edu-eyebrow {
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--luxury-champagne);
    margin-bottom: 20px;
}
.edu-hero__title {
    font-family: var(--luxury-serif);
    font-size: clamp(32px, 4.5vw, 56px);
    font-weight: 400;
    color: #fff;
    margin-bottom: 20px;
    line-height: 1.2;
}
.edu-hero__subtitle {
    font-size: 15px;
    font-weight: 300;
    color: rgba(255,255,255,0.75);
    margin-bottom: 32px;
    line-height: 1.7;
}

/* ── Section Shell ───────────────────────────────── */
.edu-section {
    padding: 80px 0;
}
.edu-section--alt {
    background: #f5f3ee;
}
.edu-label {
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 12px;
}
.edu-section__header {
    margin-bottom: 56px;
}
.edu-section__title {
    font-family: var(--luxury-serif);
    font-size: clamp(26px, 3vw, 40px);
    font-weight: 400;
    margin-bottom: 16px;
    color: var(--luxury-black);
}
.edu-section__intro {
    font-size: 15px;
    color: var(--luxury-gray);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.7;
}
.edu-deep-header {
    margin-bottom: 40px;
}
.edu-deep-body {
    max-width: 860px;
    margin: 0 auto;
}
.edu-lead {
    font-size: 16px;
    font-weight: 300;
    line-height: 1.8;
    color: var(--luxury-charcoal);
    margin-bottom: 40px;
}
.edu-subheading {
    font-family: var(--luxury-serif);
    font-size: 20px;
    font-weight: 400;
    margin: 40px 0 20px;
    color: var(--luxury-black);
}
.edu-tip {
    background: #fff;
    border-left: 3px solid var(--luxury-gold);
    padding: 16px 20px;
    font-size: 13px;
    line-height: 1.7;
    color: var(--luxury-charcoal);
    margin-top: 32px;
}

/* ── 4Cs Card Grid ───────────────────────────────── */
.fourcs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 2px;
    background: #D4C5A9;
    border: 2px solid #D4C5A9;
}
.fourcs-card {
    display: flex;
    flex-direction: column;
    padding: 40px 32px;
    background: var(--luxury-ivory);
    text-decoration: none;
    transition: background 0.25s;
}
.fourcs-card:hover {
    background: #fff;
    text-decoration: none;
}
.fourcs-card__icon {
    width: 56px;
    height: 56px;
    color: var(--luxury-gold);
    margin-bottom: 24px;
    flex-shrink: 0;
}
.fourcs-card__icon svg {
    width: 100%;
    height: 100%;
}
.fourcs-card__label {
    display: block;
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 8px;
}
.fourcs-card__title {
    font-family: var(--luxury-serif);
    font-size: 28px;
    font-weight: 400;
    color: var(--luxury-black);
    margin-bottom: 16px;
}
.fourcs-card__text {
    font-size: 14px;
    line-height: 1.75;
    color: var(--luxury-charcoal);
    flex: 1;
    margin-bottom: 20px;
}
.fourcs-card__cta {
    font-size: 12px;
    letter-spacing: 0.08em;
    color: var(--luxury-gold);
    font-weight: 500;
}

/* ── Cut Trio ────────────────────────────────────── */
.cut-trio {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}
.cut-trio__item {
    padding: 24px;
    border: 1px solid #D4C5A9;
    background: #fff;
}
.cut-trio__word {
    display: block;
    font-family: var(--luxury-serif);
    font-size: 22px;
    font-weight: 400;
    color: var(--luxury-black);
    margin-bottom: 12px;
}
.cut-trio__item p {
    font-size: 13px;
    line-height: 1.7;
    color: var(--luxury-gray);
    margin: 0;
}

/* ── Grade Scale ─────────────────────────────────── */
.grade-scale {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: #D4C5A9;
    border: 1px solid #D4C5A9;
}
.grade-scale__item {
    padding: 20px 24px;
    background: var(--luxury-ivory);
    position: relative;
}
.grade-scale__item--best {
    background: #fff;
    border-left: 3px solid var(--luxury-gold);
}
.grade-scale__name {
    display: block;
    font-family: var(--luxury-serif);
    font-size: 18px;
    font-weight: 400;
    margin-bottom: 6px;
    color: var(--luxury-black);
}
.grade-scale__desc {
    font-size: 13px;
    color: var(--luxury-charcoal);
    line-height: 1.6;
    margin: 0;
}
.grade-scale__badge, .colour-scale__badge, .clarity-badge {
    display: inline-block;
    margin-top: 8px;
    padding: 2px 10px;
    font-size: 10px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    border: 1px solid var(--luxury-gold);
}

/* ── Colour Scale ────────────────────────────────── */
.colour-scale {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2px;
    background: #D4C5A9;
    border: 1px solid #D4C5A9;
    margin-bottom: 24px;
}
.colour-scale__group {
    padding: 24px;
    background: var(--luxury-ivory);
}
.colour-scale__group--muted {
    opacity: 0.6;
}
.colour-scale__range {
    display: block;
    font-family: var(--luxury-serif);
    font-size: 22px;
    font-weight: 400;
    color: var(--luxury-black);
    margin-bottom: 4px;
}
.colour-scale__tier {
    display: block;
    font-size: 10px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 10px;
}
.colour-scale__desc {
    font-size: 13px;
    line-height: 1.65;
    color: var(--luxury-charcoal);
    margin: 0;
}

/* ── Clarity Table ───────────────────────────────── */
.clarity-table {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: #D4C5A9;
    border: 1px solid #D4C5A9;
    margin-bottom: 24px;
}
.clarity-row {
    display: grid;
    grid-template-columns: 110px 180px 1fr;
    gap: 12px;
    padding: 16px 20px;
    background: var(--luxury-ivory);
    align-items: start;
    position: relative;
}
.clarity-row--header {
    background: var(--luxury-black);
    color: #fff;
    font-size: 10px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.clarity-row--highlight {
    background: #fff;
    border-left: 3px solid var(--luxury-gold);
}
.clarity-grade {
    font-family: var(--luxury-serif);
    font-size: 15px;
    font-weight: 400;
    color: var(--luxury-black);
}
.clarity-name {
    font-size: 13px;
    font-weight: 500;
    color: var(--luxury-charcoal);
}
.clarity-desc {
    font-size: 13px;
    line-height: 1.6;
    color: var(--luxury-gray);
}
.clarity-badge {
    position: absolute;
    right: 20px;
    top: 16px;
}
@media (max-width: 640px) {
    .clarity-row {
        grid-template-columns: 80px 1fr;
        grid-template-rows: auto auto;
    }
    .clarity-name { grid-column: 2; grid-row: 1; }
    .clarity-desc { grid-column: 1 / -1; grid-row: 2; }
    .clarity-badge { position: static; margin-top: 6px; grid-column: 1 / -1; }
}

/* ── Carat Visual ────────────────────────────────── */
.carat-visual {
    display: flex;
    align-items: flex-end;
    gap: 24px;
    flex-wrap: wrap;
    padding: 32px 0;
    margin-bottom: 24px;
}
.carat-visual__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.carat-visual__circle {
    border-radius: 50%;
    background: transparent;
    border: 2px solid var(--luxury-gold);
    flex-shrink: 0;
}
.carat-visual__weight {
    font-family: var(--luxury-serif);
    font-size: 14px;
    color: var(--luxury-black);
}
.carat-visual__mm {
    font-size: 11px;
    color: var(--luxury-gray);
    letter-spacing: 0.06em;
}

/* ── Articles Grid ───────────────────────────────── */
.edu-articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 32px;
}
.edu-article-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    border: 1px solid #D4C5A9;
    transition: box-shadow 0.25s, transform 0.25s;
}
.edu-article-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    transform: translateY(-4px);
    text-decoration: none;
    color: inherit;
}
.edu-article-card__img-wrap {
    overflow: hidden;
    aspect-ratio: 16/9;
    background: #ece8e0;
}
.edu-article-card__img-wrap--placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
}
.edu-article-card__category-icon {
    font-family: var(--luxury-serif);
    font-size: 48px;
    color: var(--luxury-champagne);
}
.edu-article-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.edu-article-card:hover .edu-article-card__img { transform: scale(1.04); }
.edu-article-card__body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.edu-article-card__category {
    font-size: 10px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--luxury-gold);
    margin-bottom: 8px;
}
.edu-article-card__title {
    font-family: var(--luxury-serif);
    font-size: 20px;
    font-weight: 400;
    color: var(--luxury-black);
    margin-bottom: 12px;
    line-height: 1.3;
}
.edu-article-card__excerpt {
    font-size: 13px;
    line-height: 1.7;
    color: var(--luxury-gray);
    flex: 1;
    margin-bottom: 16px;
}
.edu-article-card__meta {
    font-size: 11px;
    letter-spacing: 0.06em;
    color: var(--luxury-gold);
    margin-top: auto;
}

/* ── CTA Strip ───────────────────────────────────── */
.edu-cta-strip {
    padding: 80px 24px;
    background: var(--luxury-black);
    text-align: center;
}
.edu-cta-strip__title {
    font-family: var(--luxury-serif);
    font-size: clamp(24px, 3vw, 36px);
    font-weight: 400;
    color: #fff;
    margin-bottom: 12px;
}
.edu-cta-strip__sub {
    font-size: 14px;
    color: rgba(255,255,255,0.6);
    margin-bottom: 32px;
}
</style>
@endsection
