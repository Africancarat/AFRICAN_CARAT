<div class="edu-pillar-body mt-4">
    <p class="edu-lead">Clarity expresses how clean the crystalline roadmap looks under magnification—eyesight thresholds matter more than acronyms for most bridal buyers.</p>
    <div class="row g-4">
        <div class="col-md-6">
            <h3 class="edu-subheading">{{ __('Popular “eye-clean” zones') }}</h3>
            <ul class="edu-list">
                <li>{{ __('Round brilliants: VS2+ usually clean face-up ~1 ct; SI1 merits video proof.') }}</li>
                <li>{{ __('Step cuts (emerald, asscher) confess inclusions generously—prioritise VS+ unless budget forces compromise.') }}</li>
                <li>{{ __('Ask for plotting maps—not every cloud is disqualifying.') }}</li>
            </ul>
        </div>
        <div class="col-md-6">
            <div class="border position-relative mx-auto edu-clarity-viz p-4" style="width:260px;height:260px;border-color:#cfd4dc!important;background:radial-gradient(circle,#f7faff 42%, #dde4ee 100%);">
                @foreach([[28,52],[110,148],[174,94]] as $clIdx => [$x,$y])
                    <span class="rounded-circle bg-secondary position-absolute" style="width:{{ 4 + $clIdx }}px;height:{{ 4 + $clIdx }}px;left:{{ $x }}px;top:{{ $y }}px;opacity:.45;"></span>
                @endforeach
                <p class="small text-muted mb-0 position-absolute bottom-0 start-0">{{ __('Illustrative inclusions magnified') }}</p>
            </div>
        </div>
    </div>
</div>
