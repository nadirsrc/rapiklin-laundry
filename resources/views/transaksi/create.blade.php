<x-app-layout title="Terima Cucian Baru" subtitle="Tulis nama pelanggan dan berat timbangan — total biayanya langsung terhitung di kanan." scene="timbangan">
    <x-slot:actions>
        <a href="{{ route('transaksi.index') }}"
           class="btn-tactile inline-flex w-full items-center justify-center rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface-muted sm:w-auto">
            Kembali ke Daftar
        </a>
    </x-slot:actions>

    <form method="POST" action="{{ route('transaksi.store') }}" class="grid gap-6 lg:grid-cols-12">
        @csrf

        <!-- Left Form -->
        <div class="space-y-6 lg:col-span-8">
            <!-- Data Pelanggan -->
            <div data-enter="1" class="relative z-30 rounded-2xl border border-border bg-surface p-5 sm:p-6">
                <div class="flex flex-col gap-3 border-b border-border pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-ink">1. Siapa Pelanggannya?</h2>
                        <p class="text-xs text-ink-muted">Kalau pernah cuci di sini, riwayatnya otomatis nyambung lewat nomor HP.</p>
                    </div>
                    @if ($pelanggans->isNotEmpty())
                        <div class="relative w-full sm:w-72" data-customer-picker>
                            <button type="button"
                                    data-picker-trigger
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    class="flex w-full items-center justify-between gap-2.5 rounded-xl border border-border bg-canvas px-3.5 py-2 text-left text-xs transition hover:border-brand/50 focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                                <span class="flex min-w-0 items-center gap-2">
                                    <svg class="h-4 w-4 shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span data-picker-label class="truncate font-medium text-ink-muted">Pilih pelanggan langganan...</span>
                                </span>
                                <svg data-picker-chevron class="h-4 w-4 shrink-0 text-ink-muted transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div data-picker-menu
                                 class="custom-select-menu hidden absolute right-0 left-0 z-40 mt-2 overflow-hidden rounded-2xl border border-border bg-surface shadow-xl shadow-ink/10 sm:left-auto sm:w-80">
                                <div class="border-b border-border bg-canvas/70 p-2.5">
                                    <div class="relative">
                                        <svg class="pointer-events-none absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text"
                                               data-picker-search
                                               placeholder="Cari nama atau nomor HP..."
                                               class="block w-full rounded-xl border border-border bg-surface py-2 pr-3 pl-8 text-xs text-ink placeholder:text-ink-muted/70 focus:border-brand focus:outline-none">
                                    </div>
                                </div>

                                <div class="max-h-60 space-y-1 overflow-y-auto p-1.5">
                                    @foreach ($pelanggans as $p)
                                        @php
                                            $parts = preg_split('/\s+/', trim($p->nama)) ?: [];
                                            $initials = strtoupper(substr($parts[0] ?? 'P', 0, 1) . substr($parts[1] ?? '', 0, 1));
                                        @endphp
                                        <button type="button"
                                                data-picker-item
                                                data-nama="{{ $p->nama }}"
                                                data-hp="{{ $p->no_hp }}"
                                                data-alamat="{{ $p->alamat }}"
                                                data-search="{{ strtolower($p->nama . ' ' . $p->no_hp . ' ' . ($p->alamat ?? '')) }}"
                                                class="flex w-full items-center justify-between gap-2.5 rounded-xl border border-transparent px-2.5 py-2 text-left transition hover:bg-canvas">
                                            <div class="flex min-w-0 items-center gap-2.5">
                                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-soft font-mono text-[11px] font-semibold text-brand">
                                                    {{ $initials }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="truncate text-xs font-semibold text-ink">{{ $p->nama }}</div>
                                                    <div class="truncate font-mono text-[11px] text-ink-muted">{{ $p->no_hp }}</div>
                                                </div>
                                            </div>
                                            <svg data-picker-check class="h-4 w-4 shrink-0 text-brand opacity-0 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach

                                    <div data-picker-empty class="hidden px-3 py-5 text-center text-xs text-ink-muted">
                                        Nama atau nomor HP tidak ketemu.
                                    </div>
                                </div>

                                <div class="border-t border-border bg-canvas/60 p-1.5">
                                    <button type="button"
                                            data-picker-clear
                                            class="flex w-full items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold text-ink-muted transition hover:bg-surface hover:text-ink">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Pelanggan baru (kosongkan isian)
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nama_pelanggan" class="block text-xs font-semibold text-ink">Nama Pelanggan <span class="text-rose-600">*</span></label>
                        <input id="nama_pelanggan" name="nama_pelanggan" type="text" value="{{ old('nama_pelanggan') }}" required
                               placeholder="Misal: Budi Santoso"
                               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-ink">Nomor HP / WhatsApp <span class="text-rose-600">*</span></label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                               placeholder="081234567890"
                               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 font-mono text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="alamat" class="block text-xs font-semibold text-ink">Alamat (Kalau perlu diantar)</label>
                        <input id="alamat" name="alamat" type="text" value="{{ old('alamat') }}"
                               placeholder="Kosongkan kalau diambil sendiri di toko"
                               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Pilih Layanan & Berat -->
            <div data-enter="2" class="relative z-10 rounded-2xl border border-border bg-surface p-5 sm:p-6">
                <div class="border-b border-border pb-4">
                    <h2 class="text-sm font-semibold text-ink">2. Paket Cuci &amp; Berat Timbangan</h2>
                    <p class="text-xs text-ink-muted">Pilih paket cuci sesuai kecepatan yang diminta pelanggan.</p>
                </div>

                @if ($layanans->isEmpty())
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        Belum ada paket cuci yang aktif. Minta Admin menambahkan paket di menu Data Layanan dulu ya.
                    </div>
                @else
                    <div class="mt-4 grid gap-3.5 sm:grid-cols-3">
                        @foreach ($layanans as $index => $layanan)
                            @php
                                $selectedLayanan = old('layanan_id')
                                    ? (string) old('layanan_id') === (string) $layanan->id
                                    : $index === 0;
                            @endphp
                            <label class="card-interactive relative flex cursor-pointer flex-col justify-between rounded-xl border border-border bg-canvas p-4 hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand-soft/60 has-[:checked]:ring-2 has-[:checked]:ring-brand/20">
                                <input type="radio" name="layanan_id" value="{{ $layanan->id }}"
                                       data-nama="{{ $layanan->nama_layanan }}"
                                       data-harga="{{ $layanan->harga_per_kg }}"
                                       data-estimasi="{{ $layanan->estimasi_durasi }}"
                                       class="layanan-radio sr-only"
                                       @checked($selectedLayanan)>
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-semibold text-ink">{{ $layanan->nama_layanan }}</span>
                                        <span class="rounded border border-border bg-surface px-1.5 py-0.5 font-mono text-[10px] text-ink-muted">
                                            {{ $layanan->kode_layanan }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-ink-muted">Durasi: {{ $layanan->estimasi_durasi }}</div>
                                </div>
                                <div class="mt-3 font-mono text-sm font-semibold text-brand tabular-nums">
                                    Rp {{ number_format($layanan->harga_per_kg, 0, ',', '.') }}/kg
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="berat_kg" class="block text-xs font-semibold text-ink">Berat Timbangan (kg) <span class="text-rose-600">*</span></label>
                        <input id="berat_kg" name="berat_kg" type="number" step="0.1" min="0.1" max="500"
                               value="{{ old('berat_kg', '1.0') }}" required
                               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 font-mono text-sm text-ink tabular-nums focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink">Posisi Awal</label>
                        <div class="mt-1.5">
                            <x-custom-select
                                id="status_pengerjaan"
                                name="status_pengerjaan"
                                :selected="old('status_pengerjaan', 'masuk')"
                                :options="[
                                    ['value' => 'masuk', 'label' => 'Masuk Antrean'],
                                    ['value' => 'proses', 'label' => 'Langsung Masuk Mesin'],
                                ]" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink">Bayaran <span class="text-rose-600">*</span></label>
                        <div class="mt-1.5">
                            <x-custom-select
                                id="status_bayar"
                                name="status_bayar"
                                :selected="old('status_bayar', 'belum_lunas')"
                                :options="[
                                    ['value' => 'belum_lunas', 'label' => 'Bayar Pas Ambil'],
                                    ['value' => 'lunas', 'label' => 'Langsung Lunas'],
                                ]" />
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="catatan" class="block text-xs font-semibold text-ink">Pesan Pelanggan (Opsional)</label>
                        <input id="catatan" name="catatan" type="text" value="{{ old('catatan') }}"
                               placeholder="Contoh: Baju putih pisah, jangan pakai pewangi menyengat..."
                               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sticky Summary -->
        <div class="lg:col-span-4">
            <div data-enter="3" class="sticky top-6 rounded-2xl border border-border bg-surface p-6">
                <h2 class="border-b border-border pb-3 text-sm font-semibold text-ink">Hitung-hitungan Nota</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-muted">Paket Dipilih</dt>
                        <dd id="summary-layanan" class="font-semibold text-ink">-</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-muted">Harga per kg</dt>
                        <dd id="summary-harga" class="font-mono text-ink tabular-nums">Rp 0</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-muted">Berat Timbangan</dt>
                        <dd id="summary-berat" class="font-mono text-ink tabular-nums">0 kg</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-muted">Perkiraan Beres</dt>
                        <dd id="summary-estimasi" class="font-medium text-ink">-</dd>
                    </div>
                </dl>

                <div class="mt-5 border-t border-dashed border-border pt-4">
                    <div class="text-xs font-semibold text-ink-muted uppercase">Total Tagihan</div>
                    <div id="summary-total" class="mt-1 inline-block font-mono text-2xl font-bold text-brand tabular-nums">
                        Rp 0
                    </div>
                    <p class="mt-1 text-[11px] leading-relaxed text-ink-muted">
                        Cek lagi angka timbangan dan paketnya sebelum cetak nota.
                    </p>
                </div>

                <button type="submit"
                        @disabled($layanans->isEmpty())
                        class="btn-tactile mt-6 w-full rounded-xl bg-brand px-4 py-3 text-sm font-semibold text-white hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                    Simpan &amp; Buat Nota
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const beratInput = document.getElementById('berat_kg');
            const totalEl = document.getElementById('summary-total');
            const radios = document.querySelectorAll('.layanan-radio');
            const formatRupiah = (angka) => 'Rp ' + Math.round(angka).toLocaleString('id-ID');

            const recalculate = (animateBump = false) => {
                const selected = document.querySelector('.layanan-radio:checked');
                const berat = Math.max(0, parseFloat(beratInput.value || '0') || 0);

                if (!selected) return;

                const harga = parseInt(selected.dataset.harga || '0', 10);
                const total = Math.round(berat * harga);

                document.getElementById('summary-layanan').textContent = selected.dataset.nama || '-';
                document.getElementById('summary-harga').textContent = formatRupiah(harga);
                document.getElementById('summary-berat').textContent = berat.toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 2
                }) + ' kg';
                document.getElementById('summary-estimasi').textContent = selected.dataset.estimasi || '-';
                totalEl.textContent = formatRupiah(total);

                if (animateBump) {
                    totalEl.classList.remove('summary-bump');
                    void totalEl.offsetWidth;
                    totalEl.classList.add('summary-bump');
                }
            };

            radios.forEach((radio) => radio.addEventListener('change', () => recalculate(true)));
            beratInput.addEventListener('input', () => recalculate(true));
            recalculate(false);
        });
    </script>
</x-app-layout>
