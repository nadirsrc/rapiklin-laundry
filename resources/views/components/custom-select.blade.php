@props([
    'id' => null,
    'name',
    'options' => [],
    'selected' => '',
    'disabled' => false,
    'surface' => 'surface',
])

@php
    $selectedStr = (string) $selected;
    $selectedLabel = $options[0]['label'] ?? 'Pilih...';
    foreach ($options as $opt) {
        if ((string) $opt['value'] === $selectedStr) {
            $selectedLabel = $opt['label'];
            break;
        }
    }
    $bgClass = $surface === 'canvas' ? 'bg-canvas' : 'bg-surface';
@endphp

<div class="custom-select relative" data-custom-select>
    <input type="hidden" @if ($id) id="{{ $id }}" @endif name="{{ $name }}" value="{{ $selectedStr }}">

    <button type="button"
            data-select-trigger
            aria-haspopup="listbox"
            aria-expanded="false"
            @disabled($disabled)
            class="flex w-full items-center justify-between gap-2 rounded-xl border border-border {{ $bgClass }} px-3.5 py-2.5 text-left text-sm text-ink transition hover:border-brand/50 focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none disabled:cursor-not-allowed disabled:bg-canvas disabled:opacity-75">
        <span data-select-label class="truncate font-medium">{{ $selectedLabel }}</span>
        <svg data-select-chevron class="h-4 w-4 shrink-0 text-ink-muted transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div data-select-menu
         role="listbox"
         class="custom-select-menu hidden absolute left-0 right-0 z-40 mt-1.5 max-h-64 overflow-y-auto rounded-2xl border border-border bg-surface p-1.5 shadow-xl shadow-ink/10">
        @foreach ($options as $opt)
            @php $isActive = (string) $opt['value'] === $selectedStr; @endphp
            <button type="button"
                    role="option"
                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    data-select-option
                    data-value="{{ $opt['value'] }}"
                    data-label="{{ $opt['label'] }}"
                    class="flex w-full items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-sm transition {{ $isActive ? 'bg-brand-soft font-semibold text-brand' : 'text-ink hover:bg-canvas' }}">
                <span class="truncate">{{ $opt['label'] }}</span>
                <svg data-select-check class="h-4 w-4 shrink-0 text-brand {{ $isActive ? '' : 'opacity-0' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        @endforeach
    </div>
</div>
