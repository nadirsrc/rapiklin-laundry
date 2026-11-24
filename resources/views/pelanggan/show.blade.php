<x-app-layout :title="'Pelanggan: ' . $pelanggan->nama" :subtitle="'No. HP: ' . $pelanggan->no_hp . ' · ' . ($pelanggan->alamat ?: 'Belum ada catatan alamat')" scene="pelanggan">
    <x-slot:actions>
        <a href="{{ route('pelanggan.index') }}"
           class="btn-tactile inline-flex w-full items-center justify-center rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted sm:w-auto">
            Kembali ke Pelanggan
        </a>
    </x-slot:actions>

    <!-- Mobile Cards -->
    <div data-enter="1" class="space-y-4 md:hidden">
        @forelse ($transaksis as $trx)
            <div class="card-interactive rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline">
                            {{ $trx->kode_transaksi }}
                        </a>
                        <div class="mt-0.5 text-xs text-ink-muted">{{ $trx->tanggal_masuk->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="flex flex-col items-end gap-1.5">
                        <x-status-badge :status="$trx->status_pengerjaan" />
                        <x-bayar-badge :status="$trx->status_bayar" />
                    </div>
                </div>
                <div class="mt-3.5 flex items-center justify-between rounded-xl bg-canvas px-3.5 py-2.5 text-xs">
                    <div>
                        <span class="font-semibold text-ink">{{ $trx->layanan->nama_layanan }}</span>
                        <span class="font-mono text-ink-muted">· {{ number_format($trx->berat_kg, 2, ',', '.') }} kg</span>
                    </div>
                    <div class="font-mono text-sm font-semibold text-ink tabular-nums">
                        Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3.5 border-t border-border pt-3.5">
                    <a href="{{ route('transaksi.show', $trx) }}"
                       class="btn-tactile block w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-xs font-semibold text-ink hover:bg-surface-muted">
                        Buka Nota
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-border bg-surface p-8 text-center text-sm text-ink-muted">
                Pelanggan ini belum punya riwayat cucian.
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div data-enter="1" class="hidden min-w-0 overflow-hidden rounded-2xl border border-border bg-surface md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Nota</th>
                        <th class="px-5 py-3.5">Diterima</th>
                        <th class="px-5 py-3.5">Paket</th>
                        <th class="px-5 py-3.5 text-right">Berat</th>
                        <th class="px-5 py-3.5 text-right">Tagihan</th>
                        <th class="px-5 py-3.5">Progres</th>
                        <th class="px-5 py-3.5">Bayaran</th>
                        <th class="px-5 py-3.5 text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($transaksis as $trx)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-5 py-3.5 font-mono text-xs font-semibold text-brand">{{ $trx->kode_transaksi }}</td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-ink-muted">{{ $trx->tanggal_masuk->format('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-3.5 font-medium text-ink">{{ $trx->layanan->nama_layanan }}</td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono tabular-nums">{{ number_format($trx->berat_kg, 2, ',', '.') }} kg</td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono font-semibold tabular-nums">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-3.5"><x-status-badge :status="$trx->status_pengerjaan" /></td>
                            <td class="whitespace-nowrap px-5 py-3.5"><x-bayar-badge :status="$trx->status_bayar" /></td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                <a href="{{ route('transaksi.show', $trx) }}" class="text-xs font-semibold text-brand hover:underline">Buka</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-ink-muted">Pelanggan ini belum punya riwayat cucian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
