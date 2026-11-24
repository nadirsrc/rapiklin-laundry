@props(['spin' => 'hover'])

@php
    $clipId = 'drum-clip-' . uniqid();
    $spinClass = match ($spin) {
        'slow' => 'svg-spin-slow',
        'active' => 'svg-spin-active',
        'hover' => 'svg-spin-hover',
        default => '',
    };
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <defs>
        <clipPath id="{{ $clipId }}">
            <circle cx="12" cy="13.8" r="4.3" />
        </clipPath>
    </defs>

    <!-- Machine Cabinet -->
    <rect x="3.5" y="2.5" width="17" height="19" rx="3.5" stroke="currentColor" stroke-width="1.75" />
    <path d="M3.5 7.2h17" stroke="currentColor" stroke-width="1.5" />

    <!-- Top Control Dial & Detergent Slot -->
    <path d="M6.5 4.9h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
    <circle cx="15.2" cy="4.9" r="0.9" fill="currentColor" />
    <circle cx="17.6" cy="4.9" r="0.9" fill="currentColor" opacity="0.65" />

    <!-- Stationary Outer Porthole Frame -->
    <circle cx="12" cy="13.8" r="5.1" stroke="currentColor" stroke-width="1.65" />

    <!-- Horizontal Water Level (stays upright, never flips upside-down) -->
    <g clip-path="url(#{{ $clipId }})">
        <g class="svg-water-slosh">
            <path d="M5.5 14.5c1.6-.9 3.2.9 4.8 0s3.2-.9 4.8 0 3.2.9 4.8 0V20H5.5v-5.5Z" fill="currentColor" opacity="0.22" />
            <path d="M5.5 14.5c1.6-.9 3.2.9 4.8 0s3.2-.9 4.8 0 3.2.9 4.8 0" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" />
        </g>
    </g>

    <!-- Rotating Inner Drum Basket (anchored strictly at exact center 12, 13.8) -->
    <g transform="translate(12, 13.8)">
        <g class="{{ $spinClass }}">
            <circle cx="0" cy="0" r="3.2" stroke="currentColor" stroke-width="1.2" stroke-dasharray="2.4 2.6" opacity="0.85" />
            <circle cx="0" cy="-2.1" r="0.55" fill="currentColor" />
            <circle cx="1.8" cy="1.1" r="0.55" fill="currentColor" />
            <circle cx="-1.8" cy="1.1" r="0.55" fill="currentColor" />
        </g>
    </g>
</svg>
