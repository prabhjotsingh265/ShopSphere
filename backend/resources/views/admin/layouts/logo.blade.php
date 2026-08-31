@php
    $size = $size ?? 28;
    $ring = ($variant ?? 'on-paper') === 'on-pine' ? '#EEF0E8' : '#1B3A2B';
@endphp
<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" aria-hidden="true">
    <circle cx="50" cy="50" r="37" stroke="{{ $ring }}" stroke-width="8"/>
    <circle cx="38" cy="38" r="4.4" fill="{{ $ring }}"/>
    <circle cx="62" cy="38" r="4.4" fill="{{ $ring }}"/>
    <circle cx="38" cy="62" r="4.4" fill="{{ $ring }}"/>
    <circle cx="62" cy="62" r="4.4" fill="{{ $ring }}"/>
    <line x1="38" y1="38" x2="62" y2="62" stroke="#C9922B" stroke-width="6.5" stroke-linecap="round"/>
    <line x1="62" y1="38" x2="38" y2="62" stroke="#C9922B" stroke-width="6.5" stroke-linecap="round"/>
</svg>
