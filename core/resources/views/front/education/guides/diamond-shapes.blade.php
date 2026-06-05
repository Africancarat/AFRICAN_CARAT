<div class="edu-pillar-body mt-4">
    <p class="edu-lead">Shapes trade facet geometry for optics: brilliance vs elongated finger coverage vs vintage austerity.</p>
    <div class="row g-4">
        <div class="col-md-7">
            <ul class="edu-list">
                <li>{{ __('Round: maximum brilliance return; calibrated standards easiest to certify.') }}</li>
                <li>{{ __('Oval / marquise / pear: elongations conceal carat vertically but demand bowtie scrutiny.') }}</li>
                <li>{{ __('Emerald / asscher: hall-of-mirrors reflections + obvious clarity—elevate purity tier.') }}</li>
                <li>{{ __('Princess / cushion: romantic corners; bezel or half-bezel if active lifestyle.') }}</li>
            </ul>
        </div>
        <div class="col-md-5 mx-auto edu-shape-grid-simple d-grid gap-2" style="grid-template-columns: repeat(3, minmax(0,1fr));">
            @foreach(range(1,6) as $s)
                <div class="border ratio ratio-1x1 d-flex align-items-center justify-content-center" style="border-color:#D4C5A9!important;color:#bdb5a9;">
                    <span style="transform:rotate({{ ($s-1)*15 }}deg);font-weight:700;">{{ $s }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
