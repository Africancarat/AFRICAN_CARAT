<div class="edu-pillar-body mt-4">
    <p class="edu-lead">Cut dictates how crisply light refracts toward your eye—the reason two diamonds can share specs yet look unmistakably different.</p>

    <h3 class="edu-subheading">Interactive cue</h3>
    <p class="small text-muted">{{ __('Open ') }} <a href="{{ route('education.tool.4cs') }}">{{ __('the interactive explorer') }}</a> {{ __('to exaggerate brilliance vs softness while you skim this copy.') }}</p>

    <div class="edu-cut-scale row g-4 align-items-center mt-3">
        <div class="col-md-8">
            <h3 class="edu-subheading">GIA grading snapshot</h3>
            <ul class="edu-list">
                <li><strong>Excellent:</strong> {{ __('Crowns near 34–35°, pavilion around 40.6°, tight facet meets—maximum face-up brilliance for rounds.') }}</li>
                <li><strong>Very Good:</strong> {{ __('Slight deviations can still sparkle impressively once set.') }}</li>
                <li><strong>Below:</strong> {{ __('Bellies retain weight but leak light—you pay for measurable carats you literally don’t perceive.') }}</li>
            </ul>
            <p class="small">{{ __('Fancies use different symmetry targets; always reconcile vendor videos with pavilion depth photography.') }}</p>
        </div>
        <div class="col-md-4">
            <div class="border p-3 text-center" style="border-color:#D4C5A9!important;">
                <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg" class="mx-auto edu-cut-svg">
                    <polygon points="60,14 113,76 93,134 27,134 7,76" stroke="#B8860B" stroke-width="1.2" fill="none"/>
                    <line x1="60" y1="14" x2="60" y2="110" stroke="#333" stroke-width="0.6" opacity="0.35"/>
                    <text x="60" y="128" font-size="8" text-anchor="middle">{{ __('facet trace') }}</text>
                </svg>
            </div>
        </div>
    </div>
</div>
