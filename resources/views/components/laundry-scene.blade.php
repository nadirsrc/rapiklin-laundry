@props(['variant' => 'dashboard'])

@php
    $uid = 'scene-' . uniqid();
@endphp

@switch($variant)
    @case('login')
        <!-- Rich Workshop Hero Illustration: Front-load Washer + Folded Linen + Hanging Shirt -->
        <svg {{ $attributes->merge(['class' => 'h-36 w-full']) }} viewBox="0 0 360 128" fill="none" aria-hidden="true">
            <defs>
                <clipPath id="{{ $uid }}-porthole">
                    <circle cx="76" cy="76" r="24" />
                </clipPath>
            </defs>

            <!-- Soft Ground Line & Backdrop Tile -->
            <rect x="8" y="10" width="344" height="108" rx="16" fill="#E7EEF6" opacity="0.72" />
            <path d="M26 108h308" stroke="#C5D4E6" stroke-width="1.5" stroke-linecap="round" />

            <!-- Garment Rail & Swaying Clean Shirt (Right) -->
            <path d="M228 28h96" stroke="#516075" stroke-width="2" stroke-linecap="round" />
            <path d="M240 28v80M312 28v80" stroke="#99ACC4" stroke-width="1.5" stroke-linecap="round" />
            <g transform="translate(276, 28)">
                <g class="svg-sway-hanger">
                    <path d="M0 0v6m-12 7L0 6l12 7" stroke="#1E4B85" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M-13 13h26l5 13-7 3-2-6v26h-18V23l-2 6-7-3 5-13Z" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" stroke-linejoin="round" />
                    <path d="M-4 13c1.5 2.5 6.5 2.5 8 0" stroke="#1E4B85" stroke-width="1.4" stroke-linecap="round" />
                </g>
            </g>

            <!-- Folded Clean Towel Stack (Middle) -->
            <g class="svg-float-soft">
                <rect x="148" y="88" width="58" height="11" rx="5.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.6" />
                <rect x="151" y="76" width="52" height="11" rx="5.5" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.6" />
                <rect x="154" y="64" width="46" height="11" rx="5.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.6" />
                <!-- Clean Sparkle -->
                <path d="M180 48v8m-4-4h8" stroke="#D97706" stroke-width="1.8" stroke-linecap="round" />
            </g>

            <!-- Front-Load Washer Machine (Left) -->
            <rect x="38" y="22" width="76" height="86" rx="10" fill="#FFFFFF" stroke="#0F172A" stroke-width="1.8" />
            <path d="M38 41h76" stroke="#0F172A" stroke-width="1.5" />
            <rect x="46" y="29" width="20" height="6" rx="3" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.3" />
            <circle cx="94" cy="32" r="3" fill="#1E4B85" />
            <circle cx="103" cy="32" r="2.2" fill="#D97706" />

            <!-- Porthole Outer Ring -->
            <circle cx="76" cy="76" r="27" fill="#F3F6FA" stroke="#0F172A" stroke-width="1.7" />
            <circle cx="76" cy="76" r="23" fill="#EBF2FA" />

            <!-- Clipped Horizontal Water Slosh -->
            <g clip-path="url(#{{ $uid }}-porthole)">
                <g class="svg-water-slosh">
                    <path d="M42 78c6-4 12 4 18 0s12-4 18 0 12 4 18 0 12-4 18 0v28H42V78Z" fill="#1E4B85" opacity="0.24" />
                    <path d="M42 78c6-4 12 4 18 0s12-4 18 0 12 4 18 0 12-4 18 0" stroke="#1E4B85" stroke-width="1.8" stroke-linecap="round" />
                </g>
            </g>

            <!-- Exact-Center Rotating Drum Ribs (76, 76) -->
            <g transform="translate(76, 76)">
                <g class="svg-spin-slow">
                    <circle cx="0" cy="0" r="17" stroke="#1E4B85" stroke-width="1.5" stroke-dasharray="6 5" />
                    <path d="M0 -17v5M14.7 8.5l-4.3-2.5M-14.7 8.5l4.3-2.5" stroke="#1E4B85" stroke-width="2" stroke-linecap="round" />
                    <circle cx="5" cy="-6" r="2" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.2" />
                    <circle cx="-7" cy="4" r="1.6" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.2" />
                </g>
            </g>
            <circle cx="76" cy="76" r="23" stroke="#1E4B85" stroke-width="1.6" />

            <!-- Floating Soap Bubbles -->
            <circle cx="125" cy="44" r="4.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.4" class="svg-float-soft" />
            <circle cx="135" cy="33" r="2.8" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.3" class="svg-float-delayed" />
        </svg>
        @break

    @case('dashboard')
        <!-- Dashboard Telemetry + Active Washer SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <defs>
                <clipPath id="{{ $uid }}-dash-drum">
                    <circle cx="32" cy="38" r="14" />
                </clipPath>
            </defs>
            <rect x="8" y="8" width="48" height="52" rx="7" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" />
            <path d="M8 20h48" stroke="#1E4B85" stroke-width="1.4" />
            <circle cx="16" cy="14" r="1.6" fill="#1E4B85" />
            <circle cx="22" cy="14" r="1.6" fill="#D97706" />
            <g clip-path="url(#{{ $uid }}-dash-drum)">
                <g class="svg-water-slosh">
                    <path d="M12 40c5-3 10 3 15 0s10-3 15 0 10 3 15 0v18H12V40Z" fill="#1E4B85" opacity="0.2" />
                </g>
            </g>
            <g transform="translate(32, 38)">
                <g class="svg-spin-slow">
                    <circle cx="0" cy="0" r="10.5" stroke="#1E4B85" stroke-width="1.4" stroke-dasharray="4 3.5" />
                    <circle cx="0" cy="-6" r="1.4" fill="#1E4B85" />
                    <circle cx="5.2" cy="3" r="1.4" fill="#1E4B85" />
                    <circle cx="-5.2" cy="3" r="1.4" fill="#1E4B85" />
                </g>
            </g>
            <circle cx="32" cy="38" r="14" stroke="#1E4B85" stroke-width="1.6" />

            <!-- Mini Telemetry Bars -->
            <rect x="68" y="34" width="9" height="26" rx="2.5" fill="#CFE0F3" class="svg-bar-grow" />
            <rect x="82" y="24" width="9" height="36" rx="2.5" fill="#6F9ED0" class="svg-bar-grow" />
            <rect x="96" y="14" width="9" height="46" rx="2.5" fill="#1E4B85" class="svg-bar-grow" />
            <g transform="translate(100.5, 14)">
                <circle cx="0" cy="0" r="4" fill="#D97706" class="svg-pulse-ring" />
                <circle cx="0" cy="0" r="2.5" fill="#D97706" />
            </g>
        </svg>
        @break

    @case('transaksi')
        <!-- Laundry Tag & Garment Queue SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <g class="svg-float-soft">
                <rect x="12" y="10" width="46" height="48" rx="6" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" />
                <path d="M20 22h24M20 31h30M20 40h18" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
                <rect x="20" y="46" width="18" height="5" rx="2.5" fill="#EBF2FA" />
            </g>
            <path d="M62 14h46" stroke="#516075" stroke-width="1.8" stroke-linecap="round" />
            <g transform="translate(85, 14)">
                <g class="svg-sway-hanger">
                    <path d="M0 0v5m-11 6L0 5l11 6" stroke="#1E4B85" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M-12 11h24l4 10-6 2.5-1.5-4.5v22h-17V19l-1.5 4.5-6-2.5 4-10Z" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.6" stroke-linejoin="round" />
                </g>
            </g>
        </svg>
        @break

    @case('timbangan')
        <!-- Digital Laundry Scale & Basket SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <g class="svg-float-soft">
                <path d="M30 22h60l-6 18H36l-6-18Z" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.7" stroke-linejoin="round" />
                <path d="M38 22c3-6 11-8 17-3 5-5 15-4 19 3" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
                <path d="M43 28v7M60 28v7M77 28v7" stroke="#1E4B85" stroke-width="1.5" stroke-linecap="round" />
            </g>
            <path d="M26 42h68" stroke="#0F172A" stroke-width="2" stroke-linecap="round" />
            <rect x="36" y="45" width="48" height="16" rx="5" fill="#FFFFFF" stroke="#0F172A" stroke-width="1.7" />
            <rect x="44" y="49" width="32" height="8" rx="2.5" fill="#EBF2FA" />
            <circle cx="51" cy="53" r="1.6" fill="#1E4B85" />
            <path d="M57 53h13" stroke="#1E4B85" stroke-width="1.8" stroke-linecap="round" />
        </svg>
        @break

    @case('layanan')
        <!-- Steam Iron & Folded Linen Tiers SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <path d="M24 26v-7" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" class="svg-steam-rise" />
            <path d="M34 24v-8" stroke="#D97706" stroke-width="1.6" stroke-linecap="round" class="svg-steam-delayed" />
            <path d="M44 26v-7" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" class="svg-steam-rise" />

            <path d="M22 33h24c6 0 10 4 12 10H14l4-7c1-2 2-3 4-3Z" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" stroke-linejoin="round" />
            <path d="M25 33v-4c0-2 1.5-3.5 3.5-3.5H42" stroke="#1E4B85" stroke-width="1.7" stroke-linecap="round" />
            <path d="M13 46h46" stroke="#0F172A" stroke-width="1.8" stroke-linecap="round" />

            <g class="svg-float-soft">
                <rect x="68" y="44" width="40" height="9" rx="4.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.6" />
                <rect x="70" y="33" width="36" height="9" rx="4.5" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.6" />
                <rect x="72" y="22" width="32" height="9" rx="4.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.6" />
            </g>
        </svg>
        @break

    @case('pelanggan')
        <!-- Laundry Member Bag & Customer Card SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <rect x="14" y="16" width="52" height="38" rx="7" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" />
            <circle cx="31" cy="31" r="6" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.6" />
            <path d="M22 45c2-4.5 16-4.5 18 0" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
            <path d="M45 28h14M45 35h10" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />

            <g class="svg-float-soft">
                <path d="M74 24h30l3 32H71l3-32Z" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.7" stroke-linejoin="round" />
                <path d="M82 24v-4a7 7 0 0 1 14 0v4" stroke="#1E4B85" stroke-width="1.7" stroke-linecap="round" />
                <circle cx="89" cy="40" r="4.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.4" />
            </g>
        </svg>
        @break

    @case('laporan')
        <!-- Revenue Analytics & Ledger Chart SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <rect x="10" y="10" width="100" height="48" rx="8" fill="#FFFFFF" stroke="#D9E2EE" stroke-width="1.5" />
            <rect x="22" y="36" width="11" height="14" rx="2.5" fill="#CFE0F3" class="svg-bar-grow" />
            <rect x="40" y="28" width="11" height="22" rx="2.5" fill="#9BBCE0" class="svg-bar-grow" />
            <rect x="58" y="32" width="11" height="18" rx="2.5" fill="#5E90C8" class="svg-bar-grow" />
            <rect x="76" y="19" width="11" height="31" rx="2.5" fill="#1E4B85" class="svg-bar-grow" />
            <path d="M27 31l18-8 18 4 19-12" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <g transform="translate(82, 15)">
                <circle cx="0" cy="0" r="4.5" fill="#D97706" class="svg-pulse-ring" />
                <circle cx="0" cy="0" r="2.6" fill="#D97706" />
            </g>
        </svg>
        @break

    @case('users')
        <!-- Staff Lanyard Badges SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <g class="svg-float-soft">
                <rect x="18" y="16" width="38" height="42" rx="6" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" />
                <path d="M32 16v-6h10v6" stroke="#1E4B85" stroke-width="1.5" />
                <circle cx="37" cy="31" r="5.5" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.5" />
                <path d="M27 46h20" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
            </g>
            <g class="svg-float-delayed">
                <rect x="64" y="12" width="38" height="42" rx="6" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.7" />
                <circle cx="83" cy="27" r="5.5" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.5" />
                <path d="M73 42h20" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
            </g>
        </svg>
        @break

    @case('lacak')
        <!-- Order Tracking Radar & Clean Garment SVG -->
        <svg {{ $attributes->merge(['class' => 'h-16 w-28 shrink-0']) }} viewBox="0 0 120 68" fill="none" aria-hidden="true">
            <rect x="16" y="12" width="48" height="44" rx="7" fill="#FFFFFF" stroke="#1E4B85" stroke-width="1.7" />
            <path d="M25 24h22M25 33h30M25 42h16" stroke="#1E4B85" stroke-width="1.6" stroke-linecap="round" />
            <g transform="translate(84, 34)">
                <circle cx="0" cy="0" r="15" stroke="#1E4B85" stroke-width="1.4" opacity="0.25" />
                <circle cx="0" cy="0" r="10" fill="#EBF2FA" stroke="#1E4B85" stroke-width="1.6" />
                <circle cx="0" cy="0" r="6" fill="#1E4B85" class="svg-pulse-ring" />
                <path d="M-3 0l2 2.2 4.5-4.5" stroke="#1E4B85" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </g>
        </svg>
        @break
@endswitch
