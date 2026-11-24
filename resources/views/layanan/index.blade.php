@php
    $layananSubtitle = auth()->user()->isAdmin()
        ? 'Atur daftar paket kiloan, harga per kg, dan perkiraan lama pengerjaan.'
        : 'Daftar harga paket cuci kiloan yang berlaku di meja kasir saat ini.';
@endphp

<x-app-layout title="Data Layanan" :subtitle="$layananSubtitle" scene="layanan">
    <x-slot:actions>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('layanan.create') }}"
               class="btn-tactile inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:w-auto">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Paket Baru
            </a>
        @else
            <span class="rounded-xl border border-border bg-canvas px-3.5 py-2 text-xs font-medium text-ink-muted">
                Mode Kasir · Hanya Lihat Harga
            </span>
        @endif
    </x-slot:actions>

    <!-- Search Bar -->
    <div data-enter="1" class="mb-6 rounded-2xl border border-border bg-surface p-4 sm:p-5">
        <form method="GET" action="{{ route('layanan.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Cari nama paket atau kodenya..."
                   class="flex-1 rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm text-ink focus:border-brand focus:bg-surface focus:outline-none">
            <div class="flex gap-3">
                <button type="submit"
                        class="btn-tactile flex-1 rounded-xl bg-ink px-5 py-2.5 text-sm font-semibold text-white hover:bg-ink/90 sm:flex-none">
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('layanan.index') }}"
                       class="btn-tactile flex-1 rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-sm font-semibold text-ink hover:bg-surface-muted sm:flex-none">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mobile Cards (Spacious & zero horizontal scroll) -->
    <div data-enter="2" class="space-y-4 md:hidden">
        @forelse ($layanans as $layanan)
            <div class="card-interactive rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-md border border-brand/25 bg-brand-soft px-2 py-0.5 font-mono text-[11px] font-semibold text-brand">
                                {{ $layanan->kode_layanan }}
                            </span>
                            <h2 class="text-base font-semibold text-ink">{{ $layanan->nama_layanan }}</h2>
                        </div>
                        @if ($layanan->deskripsi)
                            <p class="mt-2 text-xs leading-relaxed text-ink-muted">{{ $layanan->deskripsi }}</p>
                        @endif
                    </div>
                    @if ($layanan->is_active)
                        <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                            Aktif
                        </span>
                    @else
                        <span class="shrink-0 rounded-full border border-stone-200 bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-700">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 rounded-xl bg-canvas p-3.5 text-xs">
                    <div>
                        <div class="text-[11px] text-ink-muted">Harga / kg</div>
                        <div class="mt-0.5 font-mono font-semibold text-ink tabular-nums">
                            Rp {{ number_format($layanan->harga_per_kg, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-ink-muted">Perkiraan</div>
                        <div class="mt-0.5 font-medium text-ink">{{ $layanan->estimasi_durasi }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] text-ink-muted">Dipakai</div>
                        <div class="mt-0.5 font-mono font-semibold text-ink tabular-nums">{{ $layanan->transaksis_count }}×</div>
                    </div>
                </div>

                @if (auth()->user()->isAdmin())
                    <div class="mt-4 flex gap-3 border-t border-border pt-4">
                        <a href="{{ route('layanan.edit', $layanan) }}"
                           class="btn-tactile flex-1 rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-xs font-semibold text-ink hover:bg-surface-muted">
                            Ubah Paket
                        </a>
                        <form method="POST" action="{{ route('layanan.destroy', $layanan) }}" class="flex-1"
                              onsubmit="return confirm('Hapus paket {{ $layanan->nama_layanan }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn-tactile w-full rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-border bg-surface p-8 text-center text-sm text-ink-muted">
                Paket cuci yang dicari belum ada.
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div data-enter="2" class="hidden min-w-0 overflow-hidden rounded-2xl border border-border bg-surface md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                    <tr>
                        <th class="px-5 py-3.5">Kode</th>
                        <th class="px-5 py-3.5">Nama Paket</th>
                        <th class="px-5 py-3.5 text-right">Harga / kg</th>
                        <th class="px-5 py-3.5">Perkiraan Selesai</th>
                        <th class="px-5 py-3.5 text-center">Dipakai</th>
                        <th class="px-5 py-3.5">Status</th>
                        @if (auth()->user()->isAdmin())
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($layanans as $layanan)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs font-semibold text-brand">
                                {{ $layanan->kode_layanan }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-ink">{{ $layanan->nama_layanan }}</div>
                                @if ($layanan->deskripsi)
                                    <div class="mt-0.5 max-w-md text-xs text-ink-muted">{{ $layanan->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-mono font-semibold text-ink tabular-nums">
                                Rp {{ number_format($layanan->harga_per_kg, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink">
                                <div>{{ $layanan->estimasi_durasi }}</div>
                                <div class="text-xs text-ink-muted">± {{ $layanan->estimasi_jam }} jam</div>
                            </td>
                            <td class="px-5 py-4 text-center font-mono text-ink tabular-nums">
                                {{ $layanan->transaksis_count }} nota
                            </td>
                            <td class="px-5 py-4">
                                @if ($layanan->is_active)
                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-stone-200 bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            @if (auth()->user()->isAdmin())
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2.5">
                                        <a href="{{ route('layanan.edit', $layanan) }}"
                                           class="btn-tactile rounded-lg border border-border bg-surface px-3.5 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('layanan.destroy', $layanan) }}"
                                              onsubmit="return confirm('Hapus paket {{ $layanan->nama_layanan }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn-tactile rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-5 py-10 text-center text-sm text-ink-muted">
                                Paket cuci yang dicari belum ada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
