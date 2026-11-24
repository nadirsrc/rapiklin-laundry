<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $transaksi->kode_transaksi }} — RapiKlin Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas py-8 text-ink">
    <div class="mx-auto max-w-md px-4">
        <div class="no-print mb-4 flex items-center justify-between gap-3">
            <a href="{{ route('transaksi.show', $transaksi) }}"
               class="btn-tactile rounded-xl border border-border bg-surface px-4 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="btn-tactile rounded-xl bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand-hover">
                Cetak Struk
            </button>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-6 font-mono text-xs print-reset">
            <div class="border-b border-dashed border-ink-muted/40 pb-4 text-center">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-brand text-white print:border print:border-black print:bg-white print:text-black">
                    <x-drum-icon spin="none" class="h-5 w-5" />
                </div>
                <div class="font-sans text-base font-bold tracking-tight text-ink">RapiKlin Laundry</div>
                <div class="mt-0.5 font-sans text-xs text-ink-muted">Bukti Terima Cucian Kiloan</div>
                <div class="mt-2 font-semibold text-brand">{{ $transaksi->kode_transaksi }}</div>
            </div>

            <div class="space-y-1.5 border-b border-dashed border-ink-muted/40 py-4">
                <div class="flex justify-between">
                    <span class="text-ink-muted">Diterima</span>
                    <span>{{ $transaksi->tanggal_masuk->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Perkiraan Beres</span>
                    <span>{{ $transaksi->estimasi_selesai_at?->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Pelanggan</span>
                    <span class="font-semibold">{{ $transaksi->pelanggan->nama }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">No. HP</span>
                    <span>{{ $transaksi->pelanggan->no_hp }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Petugas</span>
                    <span>{{ $transaksi->user->name }}</span>
                </div>
            </div>

            <div class="space-y-2 border-b border-dashed border-ink-muted/40 py-4">
                <div class="flex justify-between font-semibold">
                    <span>{{ $transaksi->layanan->nama_layanan }} ({{ $transaksi->layanan->kode_layanan }})</span>
                    <span>{{ number_format($transaksi->berat_kg, 2, ',', '.') }} kg</span>
                </div>
                <div class="flex justify-between text-ink-muted">
                    <span>Harga per kg</span>
                    <span>Rp {{ number_format($transaksi->harga_satuan, 0, ',', '.') }} / kg</span>
                </div>
                @if ($transaksi->catatan)
                    <div class="pt-1 text-ink-muted">
                        Pesan: {{ $transaksi->catatan }}
                    </div>
                @endif
            </div>

            <div class="space-y-2 py-4">
                <div class="flex justify-between text-sm font-bold">
                    <span>TOTAL TAGIHAN</span>
                    <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Posisi Cucian</span>
                    <span class="font-semibold uppercase">{{ $transaksi->statusLabel() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Bayaran</span>
                    <span class="font-semibold uppercase">{{ $transaksi->bayarLabel() }}</span>
                </div>
            </div>

            <div class="border-t border-dashed border-ink-muted/40 pt-4 text-center font-sans text-[11px] text-ink-muted">
                Terima kasih sudah mencuci di RapiKlin. Bawa struk ini saat pengambilan cucian ya!
            </div>
        </div>
    </div>
</body>
</html>
