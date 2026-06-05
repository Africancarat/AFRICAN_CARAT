{{-- Thin profile icons for engagement style pills ($slug from English label slugified). --}}
@php
    $s = strtolower((string) ($slug ?? ''));
    $c = 'luxury-mega-ico luxury-mega-ico--sm luxury-mega-pill__ico';
@endphp
<svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@if (str_contains($s, 'solitaire'))
    <path d="M12 5v3"/><ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M10 8h4"/>
@elseif(str_contains($s, 'bezel'))
    <circle cx="12" cy="11" r="4"/><ellipse cx="12" cy="17" rx="6" ry="2.8"/>
@elseif(str_contains($s, 'pav') || str_contains($s, 'pave'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M8 13l1-4h6l1 4"/>
@elseif(str_contains($s, 'hidden'))
    <circle cx="12" cy="11" r="3"/><ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M9 11h6" opacity=".5"/>
@elseif(str_contains($s, 'halo'))
    <circle cx="12" cy="11" r="3"/><circle cx="12" cy="11" r="5.5" stroke-dasharray="2 1"/><ellipse cx="12" cy="17" rx="6" ry="2.8"/>
@elseif(str_contains($s, 'channel'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M8 12h8"/><circle cx="9.5" cy="12" r=".8" fill="currentColor"/><circle cx="12" cy="12" r=".8" fill="currentColor"/><circle cx="14.5" cy="12" r=".8" fill="currentColor"/>
@elseif(str_contains($s, 'side'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><circle cx="9" cy="11" r="2"/><circle cx="15" cy="11" r="2"/>
@elseif(str_contains($s, 'three'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><circle cx="12" cy="10" r="2"/><circle cx="8" cy="11.5" r="1.6"/><circle cx="16" cy="11.5" r="1.6"/>
@elseif(str_contains($s, 'tension'))
    <path d="M8 8h8v8H8z" opacity=".25"/><ellipse cx="12" cy="17" rx="6" ry="2.8"/><circle cx="12" cy="10" r="2.5"/>
@elseif(str_contains($s, 'floral'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M12 8l1 2 2 .5-1.5 1.5.4 2.2-2-.7-2 .7.4-2.2L9 10.5l2-.5 1-2z"/>
@elseif(str_contains($s, 'cathedral'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M9 17V9l3-3 3 3v8"/>
@elseif(str_contains($s, 'tiara'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M8 12l1.5-4 2.5 2 2.5-2L16 12"/>
@elseif(str_contains($s, 'cluster'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><circle cx="12" cy="10" r="1.5"/><circle cx="9.5" cy="11.5" r="1.2"/><circle cx="14.5" cy="11.5" r="1.2"/>
@elseif(str_contains($s, 'vintage'))
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><path d="M9 10h6l-1 5h-4l-1-5z"/>
@else
    <ellipse cx="12" cy="17" rx="6" ry="2.8"/><circle cx="12" cy="11" r="2.5"/>
@endif
</svg>
