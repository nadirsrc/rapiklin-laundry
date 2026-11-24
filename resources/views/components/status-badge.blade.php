@props(['status'])

@php
    $styles = [
        'masuk' => 'bg-sky-50 text-sky-800 border-sky-200',
        'proses' => 'bg-amber-50 text-amber-800 border-amber-200',
        'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    ];
    $labels = \App\Models\Transaksi::STATUS_LABELS;
    $class = $styles[$status] ?? 'bg-stone-100 text-stone-700 border-stone-200';
    $label = $labels[$status] ?? ucfirst((string) $status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium {$class}"]) }}>
    @if ($status === 'proses')
        <svg class="h-3.5 w-3.5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" opacity="0.25" />
            <g transform="translate(12, 12)">
                <g class="svg-spin-active">
                    <path d="M0 -9A9 9 0 0 1 9 0" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                    <path d="M0 9A9 9 0 0 1 -7.8 -4.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.7" />
                </g>
            </g>
            <circle cx="12" cy="12" r="2.2" fill="currentColor" />
        </svg>
    @elseif ($status === 'selesai')
        <svg class="h-3.5 w-3.5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    @else
        <span class="h-1.5 w-1.5 rounded-full bg-sky-600"></span>
    @endif
    {{ $label }}
</span>
