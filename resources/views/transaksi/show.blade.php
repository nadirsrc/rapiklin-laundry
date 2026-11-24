<x-app-layout :title="'Nota ' . $transaksi->kode_transaksi" subtitle="Lihat rincian cucian pelanggan, ubah progres pengerjaan, atau cetak struk nota." scene="transaksi">
    <x-slot:actions>
        <a href="{{ route('transaksi.nota', $transaksi) }}" target="_blank"
           class="btn-tactile inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-ink px-4 py-2.5 text-sm font-semibold text-white hover:bg-ink/90 sm:flex-none">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Struk
        </a>
        <a href="{{ route('transaksi.index') }}"
           class="btn-tactile inline-flex flex-1 items-center justify-center rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted sm:flex-none">
            Daftar Cucian
        </a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Main Receipt Detail -->
        <div class="space-y-6 lg:col-span-8">
            <!-- Status Progress -->
            <div data-enter="1" class="rounded-2xl border border-border bg-surface p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border pb-4">
                    <div>
                        <div class="font-mono text-sm font-semibold text-brand">{{ $transaksi->kode_transaksi }}</div>
                        <div class="mt-0.5 text-xs text-ink-muted">
                            Diterima {{ $transaksi->tanggal_masuk->format('d M Y, H:i') }} · Dicatat oleh {{ $transaksi->user->name }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$transaksi->status_pengerjaan" />
                        <x-bayar-badge :status="$transaksi->status_bayar" />
                    </div>
                </div>

                @php
                    $steps = [
                        'masuk' => 'Baru Diterima',
                        'proses' => 'Lagi Dicuci / Setrika',
                        'selesai' => 'Beres & Siap Diambil',
                    ];
                    $orderMap = ['masuk' => 1, 'proses' => 2, 'selesai' => 3];
                    $currentStep = $orderMap[$transaksi->status_pengerjaan] ?? 1;
                @endphp

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    @foreach ($steps as $key => $label)
                        @php $done = ($orderMap[$key] ?? 0) <= $currentStep; @endphp
                        <div class="rounded-xl border px-4 py-3 {{ $done ? 'border-brand/40 bg-brand-soft/60' : 'border-border bg-canvas' }}">
                            <div class="text-[11px] font-semibold uppercase {{ $done ? 'text-brand' : 'text-ink-muted' }}">
                                Langkah {{ $orderMap[$key] }}
                            </div>
                            <div class="mt-0.5 text-sm font-semibold {{ $done ? 'text-ink' : 'text-ink-muted' }}">
                                {{ $label }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Rincian Transaksi -->
            <div data-enter="2" class="rounded-2xl border border-border bg-surface p-6">
                <h2 class="border-b border-border pb-3 text-sm font-semibold text-ink">Info Pelanggan &amp; Cucian</h2>

                <div class="mt-4 grid gap-5 text-sm sm:grid-cols-2">
                    <div>
                        <div class="text-xs text-ink-muted">Nama Pelanggan</div>
                        <div class="mt-0.5 font-semibold text-ink">{{ $transaksi->pelanggan->nama }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-ink-muted">Nomor HP</div>
                        <div class="mt-0.5 font-mono font-medium text-ink">{{ $transaksi->pelanggan->no_hp }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-ink-muted">Paket Cuci</div>
                        <div class="mt-0.5 font-semibold text-ink">
                            {{ $transaksi->layanan->nama_layanan }} ({{ $transaksi->layanan->kode_layanan }})
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-ink-muted">Perkiraan Selesai</div>
                        <div class="mt-0.5 text-ink">
                            {{ $transaksi->layanan->estimasi_durasi }}
                            @if ($transaksi->estimasi_selesai_at)
                                <span class="text-xs text-ink-muted">(± {{ $transaksi->estimasi_selesai_at->format('d M Y, H:i') }})</span>
                            @endif
                        </div>
                    </div>
                    @if ($transaksi->pelanggan->alamat)
                        <div class="sm:col-span-2">
                            <div class="text-xs text-ink-muted">Alamat</div>
                            <div class="mt-0.5 text-ink">{{ $transaksi->pelanggan->alamat }}</div>
                        </div>
                    @endif
                    @if ($transaksi->catatan)
                        <div class="sm:col-span-2">
                            <div class="text-xs text-ink-muted">Pesan Pelanggan</div>
                            <div class="mt-1 rounded-xl border border-border bg-canvas px-3.5 py-2.5 text-ink">
                                {{ $transaksi->catatan }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 rounded-xl border border-border bg-canvas p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-muted">Hitungan Timbangan</span>
                        <span class="font-mono text-ink tabular-nums">
                            {{ number_format($transaksi->berat_kg, 2, ',', '.') }} kg × Rp {{ number_format($transaksi->harga_satuan, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between border-t border-border pt-2.5">
                        <span class="text-sm font-semibold text-ink">Total Tagihan</span>
                        <span class="font-mono text-xl font-bold text-brand tabular-nums">
                            Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Control Panel -->
        <div class="space-y-5 lg:col-span-4">
            <!-- Update Status Pengerjaan -->
            <div data-enter="2" class="relative z-30 rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold text-ink">Ubah Progres Cucian</h2>
                <p class="mt-0.5 text-xs text-ink-muted">Sesuaikan dengan kondisi cucian sekarang.</p>

                <form method="POST" action="{{ route('transaksi.status', $transaksi) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <x-custom-select
                        name="status_pengerjaan"
                        surface="canvas"
                        :selected="$transaksi->status_pengerjaan"
                        :options="[
                            ['value' => 'masuk', 'label' => 'Masuk'],
                            ['value' => 'proses', 'label' => 'Proses'],
                            ['value' => 'selesai', 'label' => 'Selesai'],
                        ]" />
                    <button type="submit"
                            class="btn-tactile w-full rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">
                        Simpan Progres
                    </button>
                </form>

                @if ($transaksi->tanggal_selesai)
                    <div class="mt-3 text-xs text-emerald-800">
                        Beres pada {{ $transaksi->tanggal_selesai->format('d M Y, H:i') }}
                    </div>
                @endif
            </div>

            <!-- Update Status Pembayaran -->
            <div data-enter="3" class="relative z-20 rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold text-ink">Status Bayaran</h2>
                <p class="mt-0.5 text-xs text-ink-muted">Ubah ke Lunas kalau pelanggan sudah bayar.</p>

                <form method="POST" action="{{ route('transaksi.pembayaran', $transaksi) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <x-custom-select
                        name="status_bayar"
                        surface="canvas"
                        :selected="$transaksi->status_bayar"
                        :options="[
                            ['value' => 'belum_lunas', 'label' => 'Belum Lunas'],
                            ['value' => 'lunas', 'label' => 'Lunas'],
                        ]" />
                    <button type="submit"
                            class="btn-tactile w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted">
                        Simpan Status Bayar
                    </button>
                </form>
            </div>

            @if (auth()->user()->isAdmin())
                <div data-enter="4" class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5">
                    <h2 class="text-sm font-semibold text-rose-900">Hapus Nota Ini</h2>
                    <p class="mt-0.5 text-xs text-rose-800">Pakai hanya kalau nota ini salah catat atau dobel.</p>
                    <form method="POST" action="{{ route('transaksi.destroy', $transaksi) }}" class="mt-3.5"
                          onsubmit="return confirm('Yakin hapus nota {{ $transaksi->kode_transaksi }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn-tactile w-full rounded-xl border border-rose-300 bg-white px-4 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                            Hapus Nota
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
