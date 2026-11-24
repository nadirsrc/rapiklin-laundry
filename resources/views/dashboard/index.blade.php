@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $dashSubtitle = auth()->user()->isAdmin()
        ? 'Pantau cucian yang lagi jalan, pemasukan toko, dan ramainya tiap paket cuci hari ini.'
        : 'Cek antrean cucian yang perlu segera digarap, konfirmasi bayaran, atau catat cucian baru.';
@endphp

<x-app-layout :title="'Halo, ' . $firstName" :subtitle="$dashSubtitle" scene="dashboard">
    <x-slot:actions>
        <a href="{{ route('transaksi.create') }}"
           class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:flex-none">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Terima Cucian
        </a>
        <a href="{{ route('laporan.index') }}"
           class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted sm:flex-none">
            Cek Rekap
        </a>
    </x-slot:actions>

    <!-- KPI Cards -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div data-enter="1" class="card-interactive rounded-2xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold tracking-wide text-ink-muted uppercase">Pemasukan Bulan Ini</div>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 font-mono text-2xl font-semibold text-ink tabular-nums">
                Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 text-xs text-ink-muted">
                Hari ini masuk <span class="font-mono font-semibold text-ink tabular-nums">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</span>
            </div>
        </div>

        <div data-enter="2" class="card-interactive rounded-2xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold tracking-wide text-ink-muted uppercase">Masih Digarap</div>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <x-drum-icon spin="slow" class="h-4 w-4" />
                </span>
            </div>
            <div class="mt-2.5 font-mono text-2xl font-semibold text-ink tabular-nums">
                {{ $orderMasuk + $orderProses }} nota
            </div>
            <div class="mt-1.5 flex items-center gap-3 text-xs text-ink-muted">
                <span>Baru masuk: <strong class="text-sky-800">{{ $orderMasuk }}</strong></span>
                <span>Lagi dicuci: <strong class="text-amber-800">{{ $orderProses }}</strong></span>
            </div>
        </div>

        <div data-enter="3" class="card-interactive rounded-2xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold tracking-wide text-ink-muted uppercase">Cucian Beres</div>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 font-mono text-2xl font-semibold text-emerald-700 tabular-nums">
                {{ $orderSelesai }} nota
            </div>
            <div class="mt-1.5 text-xs text-ink-muted">
                @if ($belumLunasCount > 0)
                    <span class="font-semibold text-rose-700">{{ $belumLunasCount }} nota</span> belum dibayar
                @else
                    Semua nota sudah lunas
                @endif
            </div>
        </div>

        <div data-enter="4" class="card-interactive rounded-2xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold tracking-wide text-ink-muted uppercase">Pelanggan Toko</div>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 font-mono text-2xl font-semibold text-ink tabular-nums">
                {{ $totalPelanggan }} orang
            </div>
            <div class="mt-1.5 text-xs text-ink-muted">
                Tersimpan dari riwayat cucian
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12">
        <!-- Antrean Aktif -->
        <section data-enter="2" class="min-w-0 rounded-2xl border border-border bg-surface lg:col-span-8">
            <div class="flex items-center justify-between gap-2 border-b border-border px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-ink">Antrean Cucian Saat Ini</h2>
                    <p class="text-xs text-ink-muted">Klik tombol di samping kalau cucian mulai dicuci, sudah beres, atau baru dilunasi.</p>
                </div>
                <a href="{{ route('transaksi.index') }}" class="shrink-0 text-xs font-semibold text-brand hover:underline">
                    Lihat semua nota
                </a>
            </div>

            <!-- Mobile Cards (Generous spacing, zero horizontal scroll) -->
            <div class="divide-y divide-border md:hidden">
                @forelse ($antreanAktif as $trx)
                    <div class="space-y-3.5 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline">
                                    {{ $trx->kode_transaksi }}
                                </a>
                                <div class="mt-0.5 truncate text-base font-semibold text-ink">{{ $trx->pelanggan->nama }}</div>
                                <div class="font-mono text-xs text-ink-muted">{{ $trx->pelanggan->no_hp }}</div>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1.5">
                                <x-status-badge :status="$trx->status_pengerjaan" />
                                <x-bayar-badge :status="$trx->status_bayar" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-canvas px-3.5 py-2.5 text-xs">
                            <div>
                                <span class="font-semibold text-ink">{{ $trx->layanan->nama_layanan }}</span>
                                <span class="text-ink-muted">· {{ number_format($trx->berat_kg, 2, ',', '.') }} kg</span>
                                @if ($trx->estimasi_selesai_at)
                                    <div class="mt-0.5 text-[11px] text-ink-muted">Target beres: {{ $trx->estimasi_selesai_at->format('d/m H:i') }}</div>
                                @endif
                            </div>
                            <div class="font-mono text-sm font-semibold text-ink tabular-nums">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-0.5">
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
                                        Tandai Lunas
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-ink-muted">
                        Lagi kosong nih — belum ada cucian yang antre atau sedang dicuci.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                        <tr>
                            <th class="px-4 py-3">Nota &amp; Pelanggan</th>
                            <th class="px-4 py-3">Paket</th>
                            <th class="px-4 py-3 text-right">Tagihan</th>
                            <th class="px-4 py-3">Progres</th>
                            <th class="px-4 py-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($antreanAktif as $trx)
                            <tr class="transition-colors hover:bg-canvas/60">
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <a href="{{ route('transaksi.show', $trx) }}" class="font-mono text-xs font-semibold text-brand hover:underline">
                                        {{ $trx->kode_transaksi }}
                                    </a>
                                    <div class="font-medium text-ink">{{ $trx->pelanggan->nama }}</div>
                                    <div class="text-xs text-ink-muted">{{ $trx->pelanggan->no_hp }}</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <div class="font-medium text-ink">{{ $trx->layanan->nama_layanan }}</div>
                                    <div class="font-mono text-xs text-ink-muted tabular-nums">
                                        {{ number_format($trx->berat_kg, 2, ',', '.') }} kg
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                    <div class="font-mono font-semibold text-ink tabular-nums">
                                        Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-0.5">
                                        <x-bayar-badge :status="$trx->status_bayar" />
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <x-status-badge :status="$trx->status_pengerjaan" />
                                    @if ($trx->estimasi_selesai_at)
                                        <div class="mt-1 text-[11px] text-ink-muted">
                                            Target: {{ $trx->estimasi_selesai_at->format('d/m H:i') }}
                                        </div>
                                    @endif
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-ink-muted">
                                    Lagi kosong nih — belum ada cucian yang antre atau sedang dicuci.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Ringkasan Layanan -->
        <section data-enter="3" class="min-w-0 rounded-2xl border border-border bg-surface lg:col-span-4">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-ink">Ramainya Paket Cuci</h2>
                    <p class="text-xs text-ink-muted">Banyaknya nota &amp; pemasukan tiap paket</p>
                </div>
                <a href="{{ route('layanan.index') }}" class="text-xs font-semibold text-brand hover:underline">
                    Daftar Paket
                </a>
            </div>

            <div class="divide-y divide-border">
                @forelse ($ringkasanLayanan as $layanan)
                    <div class="px-5 py-4 transition-colors hover:bg-canvas/50">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-ink">{{ $layanan->nama_layanan }}</span>
                                    <span class="rounded border border-border bg-canvas px-1.5 py-0.5 font-mono text-[10px] text-ink-muted">
                                        {{ $layanan->kode_layanan }}
                                    </span>
                                </div>
                                <div class="mt-0.5 text-xs text-ink-muted">
                                    {{ $layanan->formattedHarga() }} · {{ $layanan->estimasi_durasi }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono text-sm font-semibold text-ink tabular-nums">
                                    {{ $layanan->transaksis_count }} nota
                                </div>
                                <div class="font-mono text-xs text-ink-muted tabular-nums">
                                    {{ number_format((float) ($layanan->total_berat ?? 0), 1, ',', '.') }} kg
                                </div>
                            </div>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between rounded-xl bg-canvas px-3 py-2 text-xs">
                            <span class="text-ink-muted">Sudah Lunas</span>
                            <span class="font-mono font-semibold text-ink tabular-nums">
                                Rp {{ number_format((int) ($layanan->total_omzet ?? 0), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-ink-muted">
                        Belum ada paket cuci.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
