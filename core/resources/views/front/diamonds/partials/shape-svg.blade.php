@php
    $k = strtolower((string) ($key ?? 'round'));
    $svgClass = trim(($svgClass ?? '') !== '' ? $svgClass : 'shape-svg');
@endphp
<svg viewBox="0 0 48 48" class="{{ $svgClass }}" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-linecap="round" stroke-width="1.35" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
@if($k === 'round')
    <circle cx="24" cy="24" r="17"/>
@elseif($k === 'princess')
    <rect x="11" y="11" width="26" height="26" rx="1"/>
@elseif($k === 'oval')
    <ellipse cx="24" cy="24" rx="18" ry="12.5"/>
@elseif($k === 'cushion')
    <rect x="13" y="13" width="22" height="22" rx="6"/>
@elseif($k === 'emerald')
    <rect x="12" y="15" width="24" height="18"/>
@elseif($k === 'pear')
    <ellipse cx="24.5" cy="25.5" rx="14.5" ry="18.5" transform="rotate(-18 24.5 25.5)"/>
@elseif($k === 'marquise')
    <ellipse cx="24" cy="24" rx="19.5" ry="10"/>
@elseif($k === 'asscher')
    <polygon points="24,13 39,22 39,37 26,43 22,43 9,37 9,22"/>
@elseif($k === 'radiant')
    <rect x="14" y="14" width="20" height="20" rx="4" transform="rotate(45 24 24)"/>
@elseif($k === 'heart')
    <g transform="translate(12 11) scale(2)">
        <path stroke-width="0.9" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
    </g>
@elseif($k === 'octagon')
    <polygon points="24,9 35,15 35,27 24,33 13,27 13,15"/>
@else
    <circle cx="24" cy="24" r="17"/>
@endif
</svg>
