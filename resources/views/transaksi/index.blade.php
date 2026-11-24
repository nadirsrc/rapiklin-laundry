<x-app-layout title="Transaksi Laundry" subtitle="Pantau cucian yang baru diterima, lagi dicuci, sampai yang sudah beres dan siap diambil." scene="transaksi">
    <x-slot:actions>
        <a href="{{ route('transaksi.create') }}"
           class="btn-tactile inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:w-auto">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Terima Cucian Baru
        </a>
    </x-slot:actions>

    @php
        $activeStatus = request('status_pengerjaan', '');
        $tabs = [
            '' => ['label' => 'Semua', 'count' => $statusCounts['semua']],
            'masuk' => ['label' => 'Baru Masuk', 'count' => $statusCounts['masuk']],
            'proses' => ['label' => 'Lagi Dicuci', 'count' => $statusCounts['proses']],
            'selesai' => ['label' => 'Sudah Beres', 'count' => $statusCounts['selesai']],
        ];
    @endphp

    <!-- Status Tabs & Filters -->
    <div data-enter="1" class="relative z-20 mb-6 space-y-4 rounded-2xl border border-border bg-surface p-4 sm:p-5">
        <div class="flex flex-wrap gap-3 border-b border-border pb-4">
            @foreach ($tabs as $key => $tab)
                <a href="{{ route('transaksi.index', array_filter(array_merge(request()->except('page'), ['status_pengerjaan' => $key]))) }}"
                   class="btn-tactile inline-flex items-center gap-2 rounded-xl px-3.5 py-2.5 text-xs font-semibold {{ $activeStatus === $key ? 'bg-brand text-white' : 'bg-canvas text-ink-muted hover:bg-surface-muted hover:text-ink' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="rounded-full px-1.5 py-0.5 font-mono text-[11px] {{ $activeStatus === $key ? 'bg-white/20 text-white' : 'bg-surface text-ink' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        @php
            $layananOptions = [['value' => '', 'label' => 'Semua Paket Cuci']];
            foreach ($layanans as $layanan) {
                $layananOptions[] = ['value' => (string) $layanan->id, 'label' => $layanan->nama_layanan];
            }
        @endphp

        <form method="GET" action="{{ route('transaksi.index') }}" class="grid gap-3 md:grid-cols-12">
            @if ($activeStatus !== '')
                <input type="hidden" name="status_pengerjaan" value="{{ $activeStatus }}">
            @endif

            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nomor nota, nama, atau nomor HP..."
                       class="block w-full rounded-xl border border-border bg-canvas px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:bg-surface focus:outline-none">
            </div>

            <div class="md:col-span-3">
                <x-custom-select
                    name="layanan_id"
                    surface="canvas"
                    :selected="(string) request('layanan_id', '')"
                    :options="$layananOptions" />
            </div>

            <div class="md:col-span-3">
                <x-custom-select
                    name="status_bayar"
                    surface="canvas"
                    :selected="(string) request('status_bayar', '')"
                    :options="[
                        ['value' => '', 'label' => 'Semua Status Bayar'],
                        ['value' => 'lunas', 'label' => 'Sudah Lunas'],
                        ['value' => 'belum_lunas', 'label' => 'Belum Lunas'],
                    ]" />
            </div>

            <div class="flex items-center gap-3 md:col-span-2">
                <button type="submit"
                        class="btn-tactile flex-1 rounded-xl bg-ink px-4 py-2.5 text-sm font-semibold text-white hover:bg-ink/90">
                    Saring
                </button>
                @if (request()->hasAny(['search', 'layanan_id', 'status_bayar', 'status_pengerjaan']))
                    <a href="{{ route('transaksi.index') }}"
                       class="btn-tactile rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mobile Cards (Generous tap spacing, zero horizontal scroll) -->
    <div data-enter="2" class="space-y-4 md:hidden">
        @forelse ($transaksis as $trx)
            <div class="card-interactive rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline">
                            {{ $trx->kode_transaksi }}
                        </a>
                        <div class="mt-0.5 truncate text-base font-semibold text-ink">{{ $trx->pelanggan->nama }}</div>
                        <div class="mt-0.5 font-mono text-xs text-ink-muted">{{ $trx->pelanggan->no_hp }} · {{ $trx->tanggal_masuk->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1.5">
                        <x-status-badge :status="$trx->status_pengerjaan" />
                        <x-bayar-badge :status="$trx->status_bayar" />
                    </div>
                </div>

                <div class="mt-3.5 flex items-center justify-between rounded-xl bg-canvas px-3.5 py-3 text-xs">
                    <div>
                        <div class="font-semibold text-ink">{{ $trx->layanan->nama_layanan }}</div>
                        <div class="mt-0.5 font-mono text-ink-muted tabular-nums">
                            {{ number_format($trx->berat_kg, 2, ',', '.') }} kg × Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right font-mono text-sm font-bold text-ink tabular-nums">
                        Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                    </div>
                </div>

                @php
                    $hasQuickAction = (bool) $trx->nextStatus() || ! $trx->isLunas();
                @endphp
                <div class="mt-4 space-y-2.5 border-t border-border pt-4">
                    @if ($hasQuickAction)
                        <div class="flex gap-3">
                            @if ($next = $trx->nextStatus())
                                <form method="POST" action="{{ route('transaksi.status', $trx) }}" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status_pengerjaan" value="{{ $next }}">
                                    <button type="submit"
                                            class="btn-tactile w-full rounded-xl border border-brand/30 bg-brand-soft px-3.5 py-2.5 text-xs font-semibold text-brand hover:bg-brand hover:text-white">
                                        {{ $trx->nextStatusLabel() }}
                                    </button>
                                </form>
                            @endif

                            @if (! $trx->isLunas())
                                <form method="POST" action="{{ route('transaksi.pembayaran', $trx) }}" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status_bayar" value="lunas">
                                    <button type="submit"
                                            class="btn-tactile w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                        Lunasi
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <a href="{{ route('transaksi.show', $trx) }}"
                       class="btn-tactile block w-full rounded-xl border border-border bg-canvas px-4 py-2.5 text-center text-xs font-semibold text-ink hover:bg-surface-muted">
                        Buka Rincian &amp; Nota
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-border bg-surface p-8 text-center text-sm text-ink-muted">
                Belum ada nota cucian yang cocok dengan pencarian ini.
            </div>
        @endforelse

        @if ($transaksis->hasPages())
            <div class="rounded-2xl border border-border bg-surface px-4 py-3">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>

    <!-- Desktop Table -->
    <div data-enter="2" class="hidden min-w-0 overflow-hidden rounded-2xl border border-border bg-surface md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                    <tr>
                        <th class="px-4 py-3.5">Nota &amp; Waktu</th>
                        <th class="px-4 py-3.5">Pelanggan</th>
                        <th class="px-4 py-3.5">Paket</th>
                        <th class="px-4 py-3.5 text-right">Berat &amp; Tagihan</th>
                        <th class="px-4 py-3.5">Progres</th>
                        <th class="px-4 py-3.5">Bayaran</th>
                        <th class="px-4 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($transaksis as $trx)
                        <tr class="transition-colors hover:bg-canvas/60">
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline">
                                    {{ $trx->kode_transaksi }}
                                </a>
                                <div class="mt-0.5 text-xs text-ink-muted">
                                    {{ $trx->tanggal_masuk->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="whitespace-nowrap font-semibold text-ink">{{ $trx->pelanggan->nama }}</div>
                                <div class="whitespace-nowrap font-mono text-xs text-ink-muted">{{ $trx->pelanggan->no_hp }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <div class="font-medium text-ink">{{ $trx->layanan->nama_layanan }}</div>
                                <div class="text-xs text-ink-muted">
                                    Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}/kg
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                <div class="font-mono font-semibold text-ink tabular-nums">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </div>
                                <div class="font-mono text-xs text-ink-muted tabular-nums">
                                    {{ number_format($trx->berat_kg, 2, ',', '.') }} kg
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <x-status-badge :status="$trx->status_pengerjaan" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <x-bayar-badge :status="$trx->status_bayar" />
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($next = $trx->nextStatus())
                                        <form method="POST" action="{{ route('transaksi.status', $trx) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_pengerjaan" value="{{ $next }}">
                                            <button type="submit"
                                                    class="btn-tactile rounded-lg border border-brand/30 bg-brand-soft px-3 py-1.5 text-xs font-semibold text-brand hover:bg-brand hover:text-white">
                                                {{ $trx->nextStatusLabel() }}
                                            </button>
                                        </form>
                                    @endif

                                    @if (! $trx->isLunas())
                                        <form method="POST" action="{{ route('transaksi.pembayaran', $trx) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_bayar" value="lunas">
                                            <button type="submit"
                                                    class="btn-tactile rounded-lg border border-border bg-surface px-3 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                                Lunasi
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('transaksi.show', $trx) }}"
                                       class="btn-tactile rounded-lg border border-border bg-surface px-3 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-ink-muted">
                                Belum ada nota cucian yang cocok dengan pencarian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transaksis->hasPages())
            <div class="border-t border-border px-4 py-3">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
