@props(['status'])

@php
    $isLunas = $status === \App\Models\Transaksi::BAYAR_LUNAS;
    $class = $isLunas
        ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
        : 'bg-rose-50 text-rose-800 border-rose-200';
    $label = \App\Models\Transaksi::BAYAR_LABELS[$status] ?? ucfirst((string) $status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium {$class}"]) }}>
    {{ $label }}
</span>
