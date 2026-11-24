<x-app-layout title="Rekap Transaksi" subtitle="Cek pemasukan yang sudah lunas, nota yang belum dibayar, serta rincian tiap paket cuci." scene="laporan">
    <x-slot:actions>
        <a href="{{ route('laporan.export.xlsx', request()->query()) }}"
           class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-brand px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:flex-none">
            Excel (.xlsx)
        </a>
        <a href="{{ route('laporan.export', request()->query()) }}"
           class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-border bg-canvas px-3.5 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted sm:flex-none">
            CSV
        </a>
        <button type="button" onclick="window.print()"
                class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-ink px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-ink/90 sm:flex-none">
            Cetak Rekap
        </button>
    </x-slot:actions>

    <!-- Print Header -->
    <div class="mb-4 hidden border-b-2 border-ink pb-3 print-only">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold tracking-tight text-ink">Laporan Rekap Transaksi — RapiKlin Laundry</h1>
                <p class="mt-0.5 text-xs text-ink-muted">
                    Periode:
                    {{ $activeStartDate ? \Illuminate\Support\Carbon::parse($activeStartDate)->format('d M Y') : 'Awal' }}
                    s/d
                    {{ $activeEndDate ? \Illuminate\Support\Carbon::parse($activeEndDate)->format('d M Y') : 'Sekarang' }}
                </p>
            </div>
            <div class="text-right text-[11px] text-ink-muted">
                <div>Dicetak: {{ now()->format('d M Y, H:i') }}</div>
                <div>Petugas: {{ auth()->user()->name }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    @php
        $laporanLayananOptions = [['value' => '', 'label' => 'Semua Paket']];
        foreach ($layanans as $layanan) {
            $laporanLayananOptions[] = ['value' => (string) $layanan->id, 'label' => $layanan->nama_layanan];
        }
    @endphp
    <div data-enter="1" class="no-print relative z-20 mb-6 rounded-2xl border border-border bg-surface p-5">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid gap-3.5 md:grid-cols-12">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-ink-muted uppercase">Mulai Tanggal</label>
                <div class="mt-1.5">
                    <x-custom-date
                        id="start_date"
                        name="start_date"
                        surface="canvas"
                        :value="$activeStartDate" />
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-ink-muted uppercase">Sampai Tanggal</label>
                <div class="mt-1.5">
                    <x-custom-date
                        id="end_date"
                        name="end_date"
                        surface="canvas"
                        :value="$activeEndDate" />
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[11px] font-semibold text-ink-muted uppercase">Paket Cuci</label>
                <div class="mt-1.5">
                    <x-custom-select
                        id="layanan_id"
                        name="layanan_id"
                        surface="canvas"
                        :selected="(string) request('layanan_id', '')"
                        :options="$laporanLayananOptions" />
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-ink-muted uppercase">Progres</label>
                <div class="mt-1.5">
                    <x-custom-select
                        id="status_pengerjaan"
                        name="status_pengerjaan"
                        surface="canvas"
                        :selected="(string) request('status_pengerjaan', '')"
                        :options="[
                            ['value' => '', 'label' => 'Semua'],
                            ['value' => 'masuk', 'label' => 'Masuk'],
                            ['value' => 'proses', 'label' => 'Proses'],
                            ['value' => 'selesai', 'label' => 'Selesai'],
                        ]" />
                </div>
            </div>

            <div class="flex items-end gap-3 md:col-span-3">
                <button type="submit"
                        class="btn-tactile min-h-[42px] flex-1 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">
                    Tampilkan
                </button>
                <a href="{{ route('laporan.index', ['periode' => 'semua']) }}"
                   class="btn-tactile inline-flex min-h-[42px] items-center justify-center rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted">
                    Semua
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="print-avoid-break grid gap-4 sm:grid-cols-2 lg:grid-cols-4 print:grid-cols-4 print:gap-2.5">
        <div data-enter="1" class="card-interactive rounded-2xl border border-border bg-surface p-5 print:rounded-lg print:border-slate-300 print:p-3">
            <div class="text-xs font-semibold text-ink-muted uppercase print:text-[10px]">Banyaknya Nota</div>
            <div class="mt-2 font-mono text-2xl font-semibold text-ink tabular-nums print:mt-1 print:text-base">{{ $summary['total_transaksi'] }}</div>
            <div class="mt-1 text-xs text-ink-muted print:text-[10px]">
                Berat: <span class="font-mono font-semibold text-ink tabular-nums">{{ number_format($summary['total_berat'], 2, ',', '.') }} kg</span>
            </div>
        </div>

        <div data-enter="2" class="card-interactive rounded-2xl border border-border bg-surface p-5 print:rounded-lg print:border-slate-300 print:p-3">
            <div class="text-xs font-semibold text-ink-muted uppercase print:text-[10px]">Uang Sudah Masuk</div>
            <div class="mt-2 font-mono text-2xl font-semibold text-emerald-700 tabular-nums print:mt-1 print:text-base">
                Rp {{ number_format($summary['total_lunas'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-ink-muted print:text-[10px]">Nota sudah lunas</div>
        </div>

        <div data-enter="3" class="card-interactive rounded-2xl border border-border bg-surface p-5 print:rounded-lg print:border-slate-300 print:p-3">
            <div class="text-xs font-semibold text-ink-muted uppercase print:text-[10px]">Belum Dibayar</div>
            <div class="mt-2 font-mono text-2xl font-semibold text-rose-700 tabular-nums print:mt-1 print:text-base">
                Rp {{ number_format($summary['total_belum_lunas'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-ink-muted print:text-[10px]">Bayar saat ambil</div>
        </div>

        <div data-enter="4" class="card-interactive rounded-2xl border border-border bg-surface p-5 print:rounded-lg print:border-slate-300 print:p-3">
            <div class="text-xs font-semibold text-ink-muted uppercase print:text-[10px]">Total Keseluruhan</div>
            <div class="mt-2 font-mono text-2xl font-semibold text-ink tabular-nums print:mt-1 print:text-base">
                Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-ink-muted print:text-[10px]">Lunas &amp; belum bayar</div>
        </div>
    </div>

    <!-- Breakdown per Layanan (Relasi) -->
    <div data-enter="2" class="print-avoid-break mt-6 min-w-0 overflow-hidden rounded-2xl border border-border bg-surface print:mt-4 print:overflow-visible print:rounded-none print:border-0 print:bg-transparent">
        <div class="border-b border-border px-5 py-4 print:border-0 print:px-0 print:pt-0 print:pb-2">
            <h2 class="text-sm font-semibold text-ink print:text-xs">Rincian per Paket Cuci</h2>
            <p class="text-xs text-ink-muted print:hidden">Perbandingan banyaknya order, berat cucian, dan pemasukan tiap paket layanan.</p>
        </div>

        <!-- Mobile Breakdown Cards -->
        <div class="no-print divide-y divide-border md:hidden">
            @forelse ($rekapPerLayanan as $row)
                <div class="space-y-3 p-5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md border border-brand/25 bg-brand-soft px-2 py-0.5 font-mono text-[11px] font-semibold text-brand">
                                {{ $row->layanan?->kode_layanan ?? '-' }}
                            </span>
                            <span class="text-sm font-semibold text-ink">{{ $row->layanan?->nama_layanan ?? 'Paket Terhapus' }}</span>
                        </div>
                        <span class="font-mono font-semibold text-ink">{{ $row->jumlah_order }} nota</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 rounded-xl bg-canvas p-3">
                        <div>
                            <div class="text-[10px] text-ink-muted">Berat</div>
                            <div class="mt-0.5 font-mono font-medium text-ink tabular-nums">{{ number_format((float) $row->total_berat, 2, ',', '.') }} kg</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-ink-muted">Sudah Lunas</div>
                            <div class="mt-0.5 font-mono font-medium text-emerald-700 tabular-nums">Rp {{ number_format((int) $row->total_lunas, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-ink-muted">Total</div>
                            <div class="mt-0.5 font-mono font-semibold text-ink tabular-nums">Rp {{ number_format((int) $row->total_nilai, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-6 text-center text-sm text-ink-muted">
                    Belum ada transaksi pada rentang tanggal ini.
                </div>
            @endforelse
        </div>

        <!-- Desktop & Print Breakdown Table -->
        <div class="hidden overflow-x-auto md:block print:block print:overflow-visible">
            <table class="w-full text-left text-sm print:border-collapse print:table-fixed print:border print:border-slate-300 print:text-[11px]">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted print:bg-slate-100 print:text-[10px]">
                    <tr>
                        <th class="px-5 py-3 text-left print:w-[10%] print:border print:border-slate-300 print:px-2.5 print:py-2">Kode</th>
                        <th class="px-5 py-3 text-left print:w-[24%] print:border print:border-slate-300 print:px-2.5 print:py-2">Paket Cuci</th>
                        <th class="px-5 py-3 text-left print:w-[12%] print:border print:border-slate-300 print:px-2.5 print:py-2">Nota</th>
                        <th class="px-5 py-3 text-left print:w-[16%] print:border print:border-slate-300 print:px-2.5 print:py-2">Total Berat</th>
                        <th class="px-5 py-3 text-left print:w-[19%] print:border print:border-slate-300 print:px-2.5 print:py-2">Sudah Lunas</th>
                        <th class="px-5 py-3 text-left print:w-[19%] print:border print:border-slate-300 print:px-2.5 print:py-2">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border print:divide-y-0">
                    @forelse ($rekapPerLayanan as $row)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-5 py-3 text-left font-mono text-xs font-semibold text-brand print:border print:border-slate-300 print:px-2.5 print:py-2 print:text-[10.5px]">{{ $row->layanan?->kode_layanan ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-left font-semibold text-ink print:whitespace-normal print:border print:border-slate-300 print:px-2.5 print:py-2">{{ $row->layanan?->nama_layanan ?? 'Paket Terhapus' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-left font-mono tabular-nums print:border print:border-slate-300 print:px-2.5 print:py-2">{{ $row->jumlah_order }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-left font-mono tabular-nums print:border print:border-slate-300 print:px-2.5 print:py-2">{{ number_format((float) $row->total_berat, 2, ',', '.') }} kg</td>
                            <td class="whitespace-nowrap px-5 py-3 text-left font-mono text-emerald-700 tabular-nums print:border print:border-slate-300 print:px-2.5 print:py-2">Rp {{ number_format((int) $row->total_lunas, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-left font-mono font-semibold text-ink tabular-nums print:border print:border-slate-300 print:px-2.5 print:py-2">Rp {{ number_format((int) $row->total_nilai, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-sm text-ink-muted print:border print:border-slate-300">
                                Belum ada transaksi pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Transaksi -->
    <div data-enter="3" class="mt-6 min-w-0 overflow-hidden rounded-2xl border border-border bg-surface print:mt-4 print:overflow-visible print:rounded-none print:border-0 print:bg-transparent">
        <div class="flex items-center justify-between border-b border-border px-5 py-4 print:border-0 print:px-0 print:pt-0 print:pb-2">
            <h2 class="text-sm font-semibold text-ink print:text-xs">Daftar Nota pada Periode Ini</h2>
            @if ($transaksis->hasPages())
                <span class="hidden text-[10px] text-ink-muted print:inline">
                    Halaman {{ $transaksis->currentPage() }} dari {{ $transaksis->lastPage() }}
                </span>
            @endif
        </div>

        <!-- Mobile Transaction Cards -->
        <div class="no-print divide-y divide-border md:hidden">
            @forelse ($transaksis as $trx)
                <div class="space-y-3 p-5 text-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <a href="{{ route('transaksi.show', $trx) }}" class="font-mono font-semibold text-brand hover:underline">
                                {{ $trx->kode_transaksi }}
                            </a>
                            <div class="mt-0.5 truncate text-sm font-semibold text-ink">{{ $trx->pelanggan->nama }}</div>
                            <div class="mt-0.5 text-ink-muted">{{ $trx->tanggal_masuk->format('d/m/Y H:i') }} · Dicatat {{ $trx->user->name }}</div>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1.5">
                            <x-status-badge :status="$trx->status_pengerjaan" />
                            <x-bayar-badge :status="$trx->status_bayar" />
                        </div>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-canvas px-3.5 py-2.5">
                        <div>
                            <span class="font-semibold text-ink">{{ $trx->layanan->nama_layanan }}</span>
                            <span class="font-mono text-ink-muted">· {{ number_format($trx->berat_kg, 2, ',', '.') }} kg</span>
                        </div>
                        <div class="font-mono text-sm font-bold text-ink tabular-nums">
                            Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-ink-muted">
                    Tidak ada nota pada saringan tanggal ini.
                </div>
            @endforelse
        </div>

        <!-- Desktop & Print Table (Symmetrical full-grid borders in print, zero clipping) -->
        <div class="hidden overflow-x-auto md:block print:block print:overflow-visible">
            <table class="w-full text-left text-sm print:border-collapse print:table-fixed print:border print:border-slate-300 print:text-[10.5px]">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted print:bg-slate-100 print:text-[10px]">
                    <tr>
                        <th class="px-4 py-3 text-left print:w-[17%] print:border print:border-slate-300 print:px-2 print:py-2">Nota &amp; Waktu</th>
                        <th class="px-4 py-3 text-left print:w-[17%] print:border print:border-slate-300 print:px-2 print:py-2">Pelanggan</th>
                        <th class="px-4 py-3 text-left print:w-[11%] print:border print:border-slate-300 print:px-2 print:py-2">Paket</th>
                        <th class="px-4 py-3 text-left print:w-[9%] print:border print:border-slate-300 print:px-2 print:py-2">Berat</th>
                        <th class="px-4 py-3 text-left print:w-[12%] print:border print:border-slate-300 print:px-2 print:py-2">Tagihan</th>
                        <th class="px-4 py-3 text-left print:w-[11%] print:border print:border-slate-300 print:px-2 print:py-2">Progres</th>
                        <th class="px-4 py-3 text-left print:w-[11%] print:border print:border-slate-300 print:px-2 print:py-2">Bayaran</th>
                        <th class="px-4 py-3 text-left print:w-[12%] print:border print:border-slate-300 print:px-2 print:py-2">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border print:divide-y-0">
                    @forelse ($transaksis as $trx)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-4 py-3 text-left print:whitespace-normal print:break-words print:border print:border-slate-300 print:px-2 print:py-2">
                                <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline print:text-[10px]">{{ $trx->kode_transaksi }}</a>
                                <div class="mt-0.5 text-[11px] text-ink-muted print:text-[9.5px]">{{ $trx->tanggal_masuk->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-left print:whitespace-normal print:break-words print:border print:border-slate-300 print:px-2 print:py-2">
                                <div class="whitespace-nowrap font-medium text-ink print:whitespace-normal">{{ $trx->pelanggan->nama }}</div>
                                <div class="whitespace-nowrap font-mono text-[11px] text-ink-muted print:whitespace-normal print:text-[9.5px]">{{ $trx->pelanggan->no_hp }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-left font-medium text-ink print:whitespace-normal print:border print:border-slate-300 print:px-2 print:py-2">{{ $trx->layanan->nama_layanan }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-left font-mono tabular-nums print:border print:border-slate-300 print:px-2 print:py-2">{{ number_format($trx->berat_kg, 2, ',', '.') }} kg</td>
                            <td class="whitespace-nowrap px-4 py-3 text-left font-mono font-semibold tabular-nums print:border print:border-slate-300 print:px-2 print:py-2">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-left print:whitespace-normal print:border print:border-slate-300 print:px-2 print:py-2">
                                <x-status-badge :status="$trx->status_pengerjaan" class="print:px-1.5 print:py-0.5 print:text-[9.5px]" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-left print:whitespace-normal print:border print:border-slate-300 print:px-2 print:py-2">
                                <x-bayar-badge :status="$trx->status_bayar" class="print:px-1.5 print:py-0.5 print:text-[9.5px]" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-left text-xs text-ink-muted print:whitespace-normal print:break-words print:border print:border-slate-300 print:px-2 print:py-2 print:text-[10px]">{{ $trx->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-ink-muted print:border print:border-slate-300">
                                Tidak ada nota pada saringan tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transaksis->hasPages())
            <div class="no-print border-t border-border px-4 py-3">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>

    <!-- Print Footer Summary -->
    <div class="mt-6 hidden border-t border-slate-300 pt-3 text-[10.5px] text-ink-muted print-only">
        <div class="flex items-center justify-between">
            <span>RapiKlin Laundry · Dokumen Rekap Keuangan Internal</span>
            <span>Total {{ $summary['total_transaksi'] }} nota · Lunas Rp {{ number_format($summary['total_lunas'], 0, ',', '.') }}</span>
        </div>
    </div>
</x-app-layout>
