<div class="row">
    @forelse($diamonds as $d)
        @php
            $da = $d->diamondAttribute;
            $img = \App\Helpers\ImageHelper::storageImageUrl($d->thumbnail ?: $d->photo);
        @endphp
        <div class="col-md-6 col-xl-4 mb-4" data-diamond-id="{{ $d->id }}">
            <div class="diamond-card card h-100 border-0 shadow-sm">
                <a href="{{ route('front.product', $d->slug) }}" class="diamond-card__media">
                    <img src="{{ $img }}" class="card-img-top" alt="{{ $d->name }}">
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title diamond-card__title">
                        <a href="{{ route('front.product', $d->slug) }}" class="text-dark text-decoration-none">{{ $d->name }}</a>
                    </h6>
                    @if($da)
                        @php
                            $daColor = is_array($da->color_grade ?? null) ? implode(', ', $da->color_grade) : ($da->color_grade ?? '');
                            $daClarity = is_array($da->clarity_grade ?? null) ? implode(', ', $da->clarity_grade) : ($da->clarity_grade ?? '');
                        @endphp
                        <ul class="diamond-card__specs small text-muted list-unstyled mb-2">
                            <li>{{ $da->shape }} · {{ $da->carat_weight }} ct</li>
                            <li>{{ __('Cut') }} {{ $da->cut_grade }} · {{ __('Colour') }} {{ $daColor }} · {{ __('Clarity') }} {{ $daClarity }}</li>
                            @if($da->lab)
                                <li>
                                    {{ __('Certificate') }}: {{ $da->lab }}
                                    @if($da->certificate_url)
                                        <a href="{{ $da->certificate_url }}" class="ms-1" target="_blank" rel="noopener">{{ __('View report') }}</a>
                                    @endif
                                </li>
                            @endif
                            <li>{{__('Natural') }}</li>
                        </ul>
                    @endif
                    <div class="mt-auto d-flex flex-wrap gap-2 align-items-center">
                        <span class="h6 mb-0">{{ \App\Helpers\PriceHelper::grandCurrencyPrice($d) }}</span>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary ms-auto"
                                onclick="addDiamondCompare({{ $d->id }})">{{ __('Compare') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-muted">{{ __('No diamonds match these filters yet. Try widening carat or price.') }}</p>
        </div>
    @endforelse
</div>
