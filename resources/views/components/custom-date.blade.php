@props([
    'id' => null,
    'name',
    'value' => '',
    'placeholder' => 'Pilih tanggal',
    'surface' => 'canvas',
])

@php
    $valueStr = (string) ($value ?? '');
    $displayValue = $placeholder;
    if ($valueStr !== '') {
        try {
            $parsed = \Illuminate\Support\Carbon::parse($valueStr);
            $monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $displayValue = $parsed->format('d').' '.$monthsShort[$parsed->month - 1].' '.$parsed->format('Y');
        } catch (\Throwable) {
            $displayValue = $valueStr;
        }
    }
    $bgClass = $surface === 'canvas' ? 'bg-canvas' : 'bg-surface';
    $hasValue = $valueStr !== '';
@endphp

<div class="relative" data-custom-date>
    <input type="hidden" @if ($id) id="{{ $id }}" @endif name="{{ $name }}" value="{{ $valueStr }}" data-date-input>

    <button type="button"
            data-date-trigger
            aria-haspopup="dialog"
            aria-expanded="false"
            class="flex min-h-[42px] w-full items-center justify-between gap-2 rounded-xl border border-border {{ $bgClass }} px-3 py-2.5 text-left text-sm transition hover:border-brand/50 focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
        <span data-date-label class="truncate font-mono text-xs tabular-nums {{ $hasValue ? 'font-medium text-ink' : 'text-ink-muted' }}">
            {{ $displayValue }}
        </span>
        <svg class="h-4 w-4 shrink-0 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </button>

    <div data-date-menu
         role="dialog"
         aria-label="Pilih tanggal"
         class="custom-select-menu hidden absolute left-0 z-50 mt-2 w-72 max-w-[calc(100vw-2.5rem)] rounded-2xl border border-border bg-surface p-3.5 shadow-xl shadow-ink/10">
        <!-- Calendar Header -->
        <div class="mb-3 flex items-center justify-between gap-2">
            <button type="button" data-date-month-toggle
                    class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold text-ink transition hover:bg-canvas">
                <span data-date-heading>November 2026</span>
                <svg class="h-3.5 w-3.5 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="flex items-center gap-1">
                <button type="button" data-date-prev aria-label="Bulan sebelumnya"
                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-border bg-canvas text-ink-muted transition hover:bg-surface-muted hover:text-ink">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" data-date-next aria-label="Bulan berikutnya"
                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-border bg-canvas text-ink-muted transition hover:bg-surface-muted hover:text-ink">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Month/Year Quick Picker (Hidden by default) -->
        <div data-date-month-panel class="hidden">
            <div class="mb-2.5 flex items-center justify-between rounded-xl bg-canvas px-2.5 py-1.5">
                <button type="button" data-year-prev class="rounded-md p-1 text-ink-muted hover:bg-surface hover:text-ink" aria-label="Tahun sebelumnya">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <span data-year-label class="font-mono text-xs font-semibold text-ink">2026</span>
                <button type="button" data-year-next class="rounded-md p-1 text-ink-muted hover:bg-surface hover:text-ink" aria-label="Tahun berikutnya">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div data-month-grid class="grid grid-cols-3 gap-1.5 pb-1"></div>
        </div>

        <!-- Day Grid -->
        <div data-date-day-panel>
            <div class="mb-1.5 grid grid-cols-7 text-center text-[11px] font-semibold text-ink-muted">
                <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
            </div>
            <div data-date-grid class="grid grid-cols-7 gap-1 text-center text-xs"></div>
        </div>

        <!-- Footer -->
        <div class="mt-3 flex items-center justify-between border-t border-border pt-2.5">
            <button type="button" data-date-clear
                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-ink-muted transition hover:bg-canvas hover:text-ink">
                Kosongkan
            </button>
            <button type="button" data-date-today
                    class="rounded-lg bg-brand-soft px-3 py-1.5 text-xs font-semibold text-brand transition hover:bg-brand hover:text-white">
                Hari ini
            </button>
        </div>
    </div>
</div>
