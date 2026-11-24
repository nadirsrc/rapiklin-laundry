<x-app-layout title="Data Pelanggan" subtitle="Daftar orang yang pernah naruh cucian di toko beserta riwayat ordernya." scene="pelanggan">
    <x-slot:actions>
        <a href="{{ route('transaksi.create') }}"
           class="btn-tactile inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:w-auto">
            + Terima Cucian Baru
        </a>
    </x-slot:actions>

    <div data-enter="1" class="mb-6 rounded-2xl border border-border bg-surface p-4 sm:p-5">
        <form method="GET" action="{{ route('pelanggan.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Cari nama pelanggan atau nomor HP..."
                   class="flex-1 rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm text-ink focus:border-brand focus:bg-surface focus:outline-none">
            <div class="flex gap-3">
                <button type="submit"
                        class="btn-tactile flex-1 rounded-xl bg-ink px-5 py-2.5 text-sm font-semibold text-white hover:bg-ink/90 sm:flex-none">
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('pelanggan.index') }}"
                       class="btn-tactile flex-1 rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-sm font-semibold text-ink hover:bg-surface-muted sm:flex-none">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mobile Cards -->
    <div data-enter="2" class="space-y-4 md:hidden">
        @forelse ($pelanggans as $pelanggan)
            <div class="card-interactive rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="truncate text-base font-semibold text-ink">{{ $pelanggan->nama }}</div>
                        <div class="mt-0.5 font-mono text-xs text-ink-muted">{{ $pelanggan->no_hp }}</div>
                        @if ($pelanggan->alamat)
                            <div class="mt-1.5 text-xs text-ink-muted">{{ $pelanggan->alamat }}</div>
                        @endif
                    </div>
                    <span class="shrink-0 rounded-full border border-border bg-canvas px-2.5 py-0.5 font-mono text-xs font-semibold text-ink">
                        {{ $pelanggan->transaksis_count }}× cuci
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3 border-t border-border pt-4">
                    <div>
                        <div class="text-[11px] text-ink-muted">Total Belanja</div>
                        <div class="font-mono text-sm font-semibold text-ink tabular-nums">
                            Rp {{ number_format((int) ($pelanggan->total_belanja ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <a href="{{ route('pelanggan.show', $pelanggan) }}"
                       class="btn-tactile rounded-xl border border-border bg-surface px-4 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                        Lihat Riwayat
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-border bg-surface p-8 text-center text-sm text-ink-muted">
                Belum ada pelanggan yang cocok dengan pencarian ini.
            </div>
        @endforelse

        @if ($pelanggans->hasPages())
            <div class="rounded-2xl border border-border bg-surface px-4 py-3">
                {{ $pelanggans->links() }}
            </div>
        @endif
    </div>

    <!-- Desktop Table -->
    <div data-enter="2" class="hidden min-w-0 overflow-hidden rounded-2xl border border-border bg-surface md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pelanggan</th>
                        <th class="px-5 py-3.5">No. HP</th>
                        <th class="px-5 py-3.5">Alamat</th>
                        <th class="px-5 py-3.5 text-center">Berapa Kali Cuci</th>
                        <th class="px-5 py-3.5 text-right">Total Belanja</th>
                        <th class="px-5 py-3.5 text-right">Riwayat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($pelanggans as $pelanggan)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-ink">{{ $pelanggan->nama }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-ink">{{ $pelanggan->no_hp }}</td>
                            <td class="px-5 py-4 text-ink-muted">{{ $pelanggan->alamat ?: '-' }}</td>
                            <td class="px-5 py-4 text-center font-mono text-ink tabular-nums">{{ $pelanggan->transaksis_count }}×</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-mono font-semibold text-ink tabular-nums">
                                Rp {{ number_format((int) ($pelanggan->total_belanja ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('pelanggan.show', $pelanggan) }}"
                                   class="btn-tactile rounded-lg border border-border bg-surface px-3.5 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                    Buka Riwayat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">
                                Belum ada pelanggan yang cocok dengan pencarian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pelanggans->hasPages())
            <div class="border-t border-border px-4 py-3">
                {{ $pelanggans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
