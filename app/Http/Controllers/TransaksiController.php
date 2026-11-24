<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Services\TransaksiFilterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function __construct(private readonly TransaksiFilterService $filterService)
    {
    }

    public function index(Request $request): View
    {
        $transaksis = $this->filterService
            ->apply($request)
            ->paginate(12)
            ->withQueryString();

        $statusCounts = [
            'semua' => Transaksi::query()->count(),
            Transaksi::STATUS_MASUK => Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_MASUK)->count(),
            Transaksi::STATUS_PROSES => Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_PROSES)->count(),
            Transaksi::STATUS_SELESAI => Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_SELESAI)->count(),
        ];

        $layanans = Layanan::query()->orderBy('harga_per_kg')->get(['id', 'nama_layanan']);

        return view('transaksi.index', compact('transaksis', 'statusCounts', 'layanans'));
    }

    public function create(): View
    {
        $layanans = Layanan::aktif()->orderBy('harga_per_kg')->get();
        $pelanggans = Pelanggan::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'no_hp', 'alamat']);

        return view('transaksi.create', compact('layanans', 'pelanggans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:120'],
            'no_hp' => ['required', 'string', 'min:8', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'layanan_id' => [
                'required',
                Rule::exists('layanans', 'id')->where('is_active', true),
            ],
            'berat_kg' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'status_pengerjaan' => ['nullable', Rule::in([Transaksi::STATUS_MASUK, Transaksi::STATUS_PROSES])],
            'status_bayar' => ['required', Rule::in([Transaksi::BAYAR_BELUM_LUNAS, Transaksi::BAYAR_LUNAS])],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'no_hp.required' => 'Nomor HP pelanggan wajib diisi.',
            'no_hp.min' => 'Nomor HP minimal 8 karakter.',
            'layanan_id.required' => 'Jenis layanan wajib dipilih.',
            'layanan_id.exists' => 'Layanan yang dipilih tidak tersedia atau sedang nonaktif.',
            'berat_kg.required' => 'Berat cucian (kg) wajib diisi.',
            'berat_kg.min' => 'Berat cucian minimal 0.1 kg.',
        ]);

        $normalizedPhone = Pelanggan::normalizePhone($validated['no_hp']);
        if (strlen($normalizedPhone) < 8) {
            return back()
                ->withErrors(['no_hp' => 'Nomor HP harus berisi minimal 8 digit angka.'])
                ->withInput();
        }

        $transaksi = DB::transaction(function () use ($request, $validated, $normalizedPhone): Transaksi {
            $pelanggan = Pelanggan::query()->firstOrNew(['no_hp' => $normalizedPhone]);
            $pelanggan->nama = trim($validated['nama_pelanggan']);
            if (filled($validated['alamat'] ?? null)) {
                $pelanggan->alamat = trim((string) $validated['alamat']);
            }
            $pelanggan->save();

            $layanan = Layanan::query()->findOrFail($validated['layanan_id']);
            $beratKg = round((float) $validated['berat_kg'], 2);
            $totalHarga = Transaksi::hitungTotal($beratKg, $layanan->harga_per_kg);
            $tanggalMasuk = now();

            return Transaksi::query()->create([
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'pelanggan_id' => $pelanggan->id,
                'layanan_id' => $layanan->id,
                'user_id' => $request->user()->id,
                'berat_kg' => $beratKg,
                'harga_satuan' => $layanan->harga_per_kg,
                'total_harga' => $totalHarga,
                'status_pengerjaan' => $validated['status_pengerjaan'] ?? Transaksi::STATUS_MASUK,
                'status_bayar' => $validated['status_bayar'],
                'tanggal_masuk' => $tanggalMasuk,
                'estimasi_selesai_at' => $tanggalMasuk->copy()->addHours($layanan->estimasi_jam),
                'catatan' => filled($validated['catatan'] ?? null) ? trim((string) $validated['catatan']) : null,
            ]);
        });

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi '.$transaksi->kode_transaksi.' berhasil dicatat.');
    }

    public function show(Transaksi $transaksi): View
    {
        $transaksi->load(['pelanggan', 'layanan', 'user']);

        return view('transaksi.show', compact('transaksi'));
    }

    public function nota(Transaksi $transaksi): View
    {
        $transaksi->load(['pelanggan', 'layanan', 'user']);

        return view('transaksi.nota', compact('transaksi'));
    }

    public function updateStatus(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $validated = $request->validate([
            'status_pengerjaan' => [
                'required',
                Rule::in([Transaksi::STATUS_MASUK, Transaksi::STATUS_PROSES, Transaksi::STATUS_SELESAI]),
            ],
        ]);

        $newStatus = $validated['status_pengerjaan'];
        $transaksi->update([
            'status_pengerjaan' => $newStatus,
            'tanggal_selesai' => $newStatus === Transaksi::STATUS_SELESAI
                ? ($transaksi->tanggal_selesai ?? now())
                : null,
        ]);

        return back()->with(
            'success',
            'Status pengerjaan '.$transaksi->kode_transaksi.' diubah menjadi '.Transaksi::STATUS_LABELS[$newStatus].'.'
        );
    }

    public function updatePembayaran(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $validated = $request->validate([
            'status_bayar' => [
                'required',
                Rule::in([Transaksi::BAYAR_BELUM_LUNAS, Transaksi::BAYAR_LUNAS]),
            ],
        ]);

        $transaksi->update([
            'status_bayar' => $validated['status_bayar'],
        ]);

        return back()->with(
            'success',
            'Status pembayaran '.$transaksi->kode_transaksi.' diperbarui menjadi '.Transaksi::BAYAR_LABELS[$validated['status_bayar']].'.'
        );
    }

    public function destroy(Transaksi $transaksi): RedirectResponse
    {
        $kode = $transaksi->kode_transaksi;
        $transaksi->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi '.$kode.' berhasil dihapus.');
    }
}
