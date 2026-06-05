@php
    $shapes = [
        ['Round', 'Round'], ['Princess', 'Princess'], ['Oval', 'Oval'], ['Cushion', 'Cushion'],
        ['Emerald', 'Emerald'], ['Pear', 'Pear'], ['Marquise', 'Marquise'], ['Asscher', 'Asscher'],
        ['Radiant', 'Radiant'], ['Heart', 'Heart'],
    ];
    $reqShape = request('shape');
    $currentShape = is_array($reqShape) ? (string) (($reqShape[0] ?? '') ?: '') : (string) ($reqShape ?: '');
@endphp

<div class="filter-group diamond-shape-group">
    <label class="filter-label">{{ __('Shape') }}</label>
    <div class="shape-icon-grid">
        @foreach($shapes as [$label, $key])
            <button type="button"
                    data-shape="{{ $key }}"
                    class="shape-icon-btn {{ $currentShape === $key ? 'shape-icon-btn--active' : '' }}"
                    title="{{ __($label) }}"
                    onclick="selectDiamondShape(event, '{{ $key }}')">
                <span class="shape-icon-wrap" aria-hidden="true">
                    @include('front.diamonds.partials.shape-svg', ['key' => $key])
                </span>
                <span class="shape-icon-label">{{ __($label) }}</span>
            </button>
        @endforeach
        <button type="button"
                data-shape=""
                class="shape-icon-btn shape-icon-btn--clear {{ $currentShape === '' ? 'shape-icon-btn--active' : '' }}"
                onclick="selectDiamondShape(event, '')">{{ __('All shapes') }}</button>
    </div>
    <input type="hidden" id="diamondShapeInput" value="{{ $currentShape }}" @if($currentShape !== '') name="shape" @endif>
</div>
