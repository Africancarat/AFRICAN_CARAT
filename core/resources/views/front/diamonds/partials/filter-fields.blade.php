@php
    $cutGrades = ['Ideal', 'Excellent', 'Very Good', 'Good'];
    $colors = array_merge(['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'], range('L', 'Z'));
    $clarities = ['FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2', 'I1', 'I2'];
    $labs = ['GIA', 'IGI', 'AGS', 'HRD', 'EGL', 'OTHER'];
    $fluorescence = ['None', 'Faint', 'Medium', 'Strong', 'Very Strong'];
    $polSy = ['Excellent', 'Very Good', 'Good', 'Fair'];
@endphp

{{-- Lab-grown vs natural — omit param = show both --}}
<div class="filter-group filter-group--toggle">
    <div class="filter-label">{{ __('Origin') }}</div>
    <div class="lux-toggle-row">
        <button type="button" id="labToggleLab" class="lab-toggle active"
                onclick="event.preventDefault(); setLabToggle(1)">{{ __('Lab-grown') }}</button>
    </div>
    <input type="hidden" id="labGrownInput" value="1" name="lab_grown">
</div>

@include('front.diamonds.partials.shape-selector')

<div class="filter-group">
    <label class="filter-label">{{ __('Carat weight') }}</label>
    <div class="lux-range-inputs">
        <input type="number" name="carat_min" step="0.01" placeholder="0.30" class="filter-input"
               value="{{ request('carat_min') }}">
        <span class="lux-range-dash">–</span>
        <input type="number" name="carat_max" step="0.01" placeholder="5.00" class="filter-input"
               value="{{ request('carat_max') }}">
    </div>
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Cut grade') }}</label>
    @foreach($cutGrades as $cut)
        <label class="filter-check">
            <input type="checkbox" name="cut[]" value="{{ $cut }}" @checked(in_array($cut, (array) request('cut', []), true))> {{ $cut }}
        </label>
    @endforeach
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Colour') }}</label>
    <div class="filter-check-grid filter-check-grid--cols">
        @foreach($colors as $c)
            <label class="filter-check filter-check--tight">
                <input type="checkbox" name="color[]" value="{{ $c }}" @checked(in_array($c, (array) request('color', []), true))> {{ $c }}
            </label>
        @endforeach
    </div>
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Clarity') }}</label>
    <div class="filter-check-grid">
        @foreach($clarities as $cl)
            <label class="filter-check filter-check--tight">
                <input type="checkbox" name="clarity[]" value="{{ $cl }}" @checked(in_array($cl, (array) request('clarity', []), true))> {{ $cl }}
            </label>
        @endforeach
    </div>
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Certificates & lab') }}</label>
    @foreach($labs as $lab)
        <label class="filter-check">
            <input type="checkbox" name="lab[]" value="{{ $lab }}" @checked(in_array($lab, (array) request('lab', []), true))> {{ $lab }}
        </label>
    @endforeach
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Price') }} — {{ __('store currency shown at checkout') }}</label>
    <div class="lux-range-inputs">
        <input type="number" name="price_min" step="100" placeholder="{{ __('Min') }}" class="filter-input"
               value="{{ request('price_min') }}">
        <span class="lux-range-dash">–</span>
        <input type="number" name="price_max" placeholder="{{ __('Max') }}" class="filter-input"
               value="{{ request('price_max') }}">
    </div>
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Optical proportions') }}</label>
    <div class="lux-range-inputs">
        <input type="number" name="table_min" step="0.1" placeholder="{{ __('Table % min') }}" class="filter-input"
               value="{{ request('table_min') }}">
        <span class="lux-range-dash">–</span>
        <input type="number" name="table_max" step="0.1" placeholder="{{ __('Table % max') }}" class="filter-input"
               value="{{ request('table_max') }}">
    </div>
    <div class="lux-range-inputs mt-2">
        <input type="number" name="depth_min" step="0.1" placeholder="{{ __('Depth % min') }}" class="filter-input"
               value="{{ request('depth_min') }}">
        <span class="lux-range-dash">–</span>
        <input type="number" name="depth_max" step="0.1" placeholder="{{ __('Depth % max') }}" class="filter-input"
               value="{{ request('depth_max') }}">
    </div>
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Fluorescence') }}</label>
    @foreach($fluorescence as $f)
        <label class="filter-check">
            <input type="checkbox" name="fluorescence[]" value="{{ $f }}" @checked(in_array($f, (array) request('fluorescence', []), true))> {{ $f }}
        </label>
    @endforeach
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Polish') }}</label>
    @foreach($polSy as $p)
        <label class="filter-check">
            <input type="checkbox" name="polish[]" value="{{ $p }}" @checked(in_array($p, (array) request('polish', []), true))> {{ $p }}
        </label>
    @endforeach
</div>

<div class="filter-group">
    <label class="filter-label">{{ __('Symmetry') }}</label>
    @foreach($polSy as $s)
        <label class="filter-check">
            <input type="checkbox" name="symmetry[]" value="{{ $s }}" @checked(in_array($s, (array) request('symmetry', []), true))> {{ $s }}
        </label>
    @endforeach
</div>
