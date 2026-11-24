<x-guest-layout title="Cek Status Cucian">
    <div class="mx-auto min-h-screen max-w-3xl px-4 py-8 sm:px-6 sm:py-10">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand text-white">
                    <x-drum-icon spin="slow" class="h-5 w-5" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-ink">RapiKlin Laundry</div>
                    <div class="text-xs text-ink-muted">Lacak Cucian Pelanggan</div>
                </div>
            </div>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
               class="btn-tactile rounded-xl border border-border bg-surface px-4 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                {{ auth()->check() ? 'Kembali ke Dashboard' : 'Login Petugas' }}
            </a>
        </div>

        <div data-enter="1" class="rounded-2xl border border-border bg-surface p-6 sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-ink">Sudah beres belum ya cucianmu?</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink-muted">
                        Ketik nomor nota (misal <span class="font-mono text-ink">LND-{{ now()->format('Ymd') }}-0001</span>) atau nomor HP yang kamu kasih waktu naruh cucian.
                    </p>
                </div>
                <div class="self-start rounded-xl border border-border bg-canvas px-2 py-1 sm:self-center">
                    <x-laundry-scene variant="lacak" />
                </div>
            </div>

            <form method="GET" action="{{ route('cek-cucian') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
                <input type="text" name="q" value="{{ $queryText }}" required
                       placeholder="Nomor nota atau nomor HP..."
                       class="flex-1 rounded-xl border border-border bg-canvas px-4 py-3 text-sm text-ink focus:border-brand focus:bg-surface focus:ring-2 focus:ring-brand/15 focus:outline-none">
                <button type="submit"
                        class="btn-tactile rounded-xl bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-hover">
                    Cari Cucian
                </button>
            </form>
        </div>

        @if ($queryText !== '')
            <div data-enter="2" class="mt-6 space-y-4">
                @forelse ($results as $item)
                    <div class="card-interactive rounded-2xl border border-border bg-surface p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-border pb-4">
                            <div>
                                <div class="font-mono text-sm font-semibold text-brand">{{ $item->kode_transaksi }}</div>
                                <div class="mt-0.5 text-base font-semibold text-ink">{{ $item->pelanggan->nama }}</div>
                                <div class="text-xs text-ink-muted">Diterima: {{ $item->tanggal_masuk->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$item->status_pengerjaan" />
                                <x-bayar-badge :status="$item->status_bayar" />
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                            <div>
                                <div class="text-xs text-ink-muted">Paket &amp; Berat</div>
                                <div class="mt-0.5 font-medium text-ink">
                                    {{ $item->layanan->nama_layanan }} ({{ number_format($item->berat_kg, 2, ',', '.') }} kg)
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-ink-muted">Total Biaya</div>
                                <div class="mt-0.5 font-mono font-semibold text-ink tabular-nums">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-ink-muted">
                                    {{ $item->isSelesai() ? 'Beres Pada' : 'Perkiraan Selesai' }}
                                </div>
                                <div class="mt-0.5 font-medium text-ink">
                                    @if ($item->isSelesai() && $item->tanggal_selesai)
                                        {{ $item->tanggal_selesai->format('d M Y, H:i') }}
                                    @elseif ($item->estimasi_selesai_at)
                                        {{ $item->estimasi_selesai_at->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-border bg-surface p-8 text-center">
                        <div class="text-sm font-semibold text-ink">Nota tidak ketemu</div>
                        <p class="mt-1 text-xs text-ink-muted">Coba cek lagi penulisan nomor nota atau nomor HP yang kamu masukkan.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-guest-layout>
