@php
    $isEdit = isset($layanan);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="kode_layanan" class="block text-xs font-semibold text-ink">Kode Paket <span class="text-rose-600">*</span></label>
        <input id="kode_layanan" name="kode_layanan" type="text"
               value="{{ old('kode_layanan', $layanan->kode_layanan ?? '') }}"
               placeholder="REG / EXP / SKL" required
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 font-mono text-sm uppercase text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
        <p class="mt-1 text-[11px] text-ink-muted">Kode singkat tanpa spasi, contohnya REG atau SKL.</p>
    </div>

    <div>
        <label for="nama_layanan" class="block text-xs font-semibold text-ink">Nama Paket Cuci <span class="text-rose-600">*</span></label>
        <input id="nama_layanan" name="nama_layanan" type="text"
               value="{{ old('nama_layanan', $layanan->nama_layanan ?? '') }}"
               placeholder="Misal: Reguler, Ekspress, Superkilat" required
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>

    <div>
        <label for="harga_per_kg" class="block text-xs font-semibold text-ink">Harga per Kilogram (Rp) <span class="text-rose-600">*</span></label>
        <input id="harga_per_kg" name="harga_per_kg" type="number" min="500" step="100"
               value="{{ old('harga_per_kg', $layanan->harga_per_kg ?? '') }}"
               placeholder="7000" required
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 font-mono text-sm text-ink tabular-nums focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="estimasi_durasi" class="block text-xs font-semibold text-ink">Teks Perkiraan <span class="text-rose-600">*</span></label>
            <input id="estimasi_durasi" name="estimasi_durasi" type="text"
                   value="{{ old('estimasi_durasi', $layanan->estimasi_durasi ?? '') }}"
                   placeholder="3-4 hari / 5 jam" required
                   class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
        </div>
        <div>
            <label for="estimasi_jam" class="block text-xs font-semibold text-ink">Hitungan Jam <span class="text-rose-600">*</span></label>
            <input id="estimasi_jam" name="estimasi_jam" type="number" min="1" max="720"
                   value="{{ old('estimasi_jam', $layanan->estimasi_jam ?? 48) }}"
                   required
                   class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 font-mono text-sm text-ink tabular-nums focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
        </div>
    </div>

    <div class="sm:col-span-2">
        <label for="deskripsi" class="block text-xs font-semibold text-ink">Keterangan Paket</label>
        <textarea id="deskripsi" name="deskripsi" rows="3"
                  placeholder="Tulis singkat apa saja yang didapat pelanggan, misal cuci kering setrika lipat..."
                  class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">{{ old('deskripsi', $layanan->deskripsi ?? '') }}</textarea>
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2.5 rounded-xl border border-border bg-canvas px-4 py-3 text-sm font-medium text-ink">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $isEdit ? $layanan->is_active : true))
                   class="h-4 w-4 rounded border-border text-brand focus:ring-brand">
            <span>Tampilkan paket ini saat kasir menerima cucian baru</span>
        </label>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-border pt-5 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('layanan.index') }}"
       class="btn-tactile rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-sm font-semibold text-ink hover:bg-surface-muted">
        Batal
    </a>
    <button type="submit"
            class="btn-tactile rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Paket' }}
    </button>
</div>
