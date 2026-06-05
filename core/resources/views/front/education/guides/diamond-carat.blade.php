<div class="edu-pillar-body mt-4">
    <p class="edu-lead">Carat denotes weight—not visual span. Two Excellent-cut rounds at 1.00 ct can diverge noticeably in perceived size if girdles differ.</p>
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <h3 class="edu-subheading">{{ __('Face-up diameter cheat sheet — rounds') }}</h3>
            <table class="table table-sm border">
                <thead class="small text-muted"><tr><th>{{ __('Approx. diameter') }}</th><th>{{ __('Representative ct') }}</th></tr></thead>
                <tbody class="small">
                    <tr><td>6.5 mm</td><td>&plusmn;1.00 ct</td></tr>
                    <tr><td>6.9 mm</td><td>&plusmn;1.25 ct</td></tr>
                    <tr><td>7.7 mm</td><td>&plusmn;2.00 ct</td></tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-4 text-center">
            @foreach([['label'=>'0.50 ct','s'=>118],['label'=>'1.00 ct','s'=>154],['label'=>'2.00 ct','s'=>198]] as $dot)
                <div class="mb-3">
                    <div class="rounded-circle mx-auto shadow-sm" style="width:{{ $dot['s'] }}px;height:{{ $dot['s'] }}px;background:radial-gradient(circle at 32% 30%,#ffffff,#eaeef6);border:1px solid #dbd6cd;"></div>
                    <small class="text-muted d-block">{{ $dot['label'] }}</small>
                </div>
            @endforeach
        </div>
    </div>
</div>
