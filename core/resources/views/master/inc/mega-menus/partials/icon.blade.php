{{-- Minimal line icons for mega menus (stroke = currentColor). Pass $name and optional $class. --}}
@php
    $n = $name ?? 'dot';
    $c = trim('luxury-mega-ico ' . ($class ?? ''));
@endphp
@switch($n)
    @case('ring-setting')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <ellipse cx="12" cy="16" rx="7" ry="3.5"/>
            <path d="M9 12.5c0-1.2 1.2-2.2 3-2.2s3 1 3 2.2"/>
            <path d="M10.5 10.2L12 7l1.5 3.2"/>
        </svg>
        @break
    @case('diamond')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M7 10h10l-2.2 8.5H9.2L7 10z"/>
            <path d="M7 10L9.5 5.5h5L17 10"/>
            <path d="M9.5 5.5L12 3l2.5 2.5"/>
        </svg>
        @break
    @case('diamond-lab')
        <svg class="{{ $c }} luxury-mega-ico--tone-lab" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M7 10h10l-2.2 8.5H9.2L7 10z"/>
            <path d="M7 10L9.5 5.5h5L17 10"/>
            <path d="M9.5 5.5L12 3l2.5 2.5"/>
        </svg>
        @break
    @case('diamond-fancy')
        <svg class="{{ $c }} luxury-mega-ico--tone-fancy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.15" stroke-linejoin="round" aria-hidden="true">
            <path d="M7 10h10l-2.2 8.5H9.2L7 10z"/>
            <path d="M7 10L9.5 5.5h5L17 10"/>
            <path d="M9.5 5.5L12 3l2.5 2.5"/>
        </svg>
        @break
    @case('gemstone')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M8 10l3.5-5h1L16 10l-1.2 6.5H9.2L8 10z"/>
            <path d="M8 10h8"/>
        </svg>
        @break
    @case('sparkle')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
            <path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/>
            <circle cx="12" cy="12" r="2.2"/>
        </svg>
        @break
    @case('earrings')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <circle cx="7.5" cy="10" r="2.2"/>
            <circle cx="16.5" cy="10" r="2.2"/>
            <path d="M7.5 12.2v2.3M16.5 12.2v2.3" stroke-linecap="round"/>
        </svg>
        @break
    @case('hearts-pair')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M5.5 8.2a1.5 1.5 0 0 1 2.3-1.4L9 7.5l1.2-.7a1.5 1.5 0 0 1 2.2 1.4c0 1.2-1.1 2.2-2.3 3.1-1.2-.9-2.3-1.9-2.3-3.1z"/>
            <path d="M12.5 8.2a1.5 1.5 0 0 1 2.3-1.4L16 7.5l1.2-.7a1.5 1.5 0 0 1 2.2 1.4c0 1.2-1.1 2.2-2.3 3.1-1.2-.9-2.3-1.9-2.3-3.1z" transform="translate(1.2 0)"/>
        </svg>
        @break
    @case('hearts-pair-lab')
        <svg class="{{ $c }} luxury-mega-ico--tone-lab" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M5.5 8.2a1.5 1.5 0 0 1 2.3-1.4L9 7.5l1.2-.7a1.5 1.5 0 0 1 2.2 1.4c0 1.2-1.1 2.2-2.3 3.1-1.2-.9-2.3-1.9-2.3-3.1z"/>
            <path d="M12.5 8.2a1.5 1.5 0 0 1 2.3-1.4L16 7.5l1.2-.7a1.5 1.5 0 0 1 2.2 1.4c0 1.2-1.1 2.2-2.3 3.1-1.2-.9-2.3-1.9-2.3-3.1z" transform="translate(1.2 0)"/>
        </svg>
        @break
    @case('pendant')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 4.5v2.2"/>
            <path d="M9.2 8.2h5.6l1.2 2.2-3.5 7.1-3.5-7.1 1.2-2.2z"/>
        </svg>
        @break
    @case('ring-eternity')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <ellipse cx="12" cy="15" rx="7" ry="3.2"/>
            <path d="M6.2 14.2l.5-1.1M8.4 12.5l.3-1.2M10.8 12l.1-1.2M13.1 12l.1 1.2M15.5 12.5l.3 1.2M17.7 14.2l.5 1.1" stroke-linecap="round"/>
        </svg>
        @break
    @case('ring-anniversary')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <ellipse cx="12" cy="15" rx="7" ry="3.2"/>
            <path d="M7.5 13.2h9" stroke-linecap="round"/>
        </svg>
        @break
    @case('studs')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <circle cx="8" cy="11" r="2"/>
            <circle cx="16" cy="11" r="2"/>
            <path d="M8 13v2M16 13v2" stroke-linecap="round"/>
        </svg>
        @break
    @case('tennis')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
            <path d="M5 12h14"/>
            <circle cx="7.5" cy="12" r="1.3"/><circle cx="12" cy="12" r="1.3"/><circle cx="16.5" cy="12" r="1.3"/>
        </svg>
        @break
    @case('bracelet')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
            <ellipse cx="12" cy="13" rx="7.5" ry="5"/>
            <path d="M6 13H5M19 13h-1"/>
        </svg>
        @break
    @case('necklace-arc')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
            <path d="M5 14c2.5-5 11.5-5 14 0"/>
            <circle cx="12" cy="17" r="1.8"/>
        </svg>
        @break
    @case('chain')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
            <path d="M6 10c2 0 3 2 5 2s3-2 5-2 3 2 5 2"/>
            <path d="M6 14c2 0 3 2 5 2s3-2 5-2 3 2 5 2"/>
        </svg>
        @break
    @case('star-premier')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.15" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 4l1.8 4.2L18 9.3l-3.4 2.9 1 4.5L12 14.8 8.4 16.7l1-4.5L6 9.3l4.2-1.1L12 4z"/>
        </svg>
        @break
    @case('ring-women')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <ellipse cx="12" cy="15" rx="6" ry="3"/>
            <path d="M9 12l1.5-4h3l1.5 4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('ring-men')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" aria-hidden="true">
            <ellipse cx="12" cy="15" rx="7" ry="3.3"/>
        </svg>
        @break
    @case('book')
        <svg class="{{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 4.5h5a2 2 0 0 1 2 2v13a2 2 0 0 0-2-2H6V4.5z"/>
            <path d="M18 4.5h-5a2 2 0 0 0-2 2v13a2 2 0 0 1 2-2h5V4.5z"/>
        </svg>
        @break
    @default
        <svg class="{{ $c }}" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2" fill="currentColor"/></svg>
@endswitch
