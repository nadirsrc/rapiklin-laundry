<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $pendapatanHariIni = (int) Transaksi::query()
            ->where('status_bayar', Transaksi::BAYAR_LUNAS)
            ->whereDate('tanggal_masuk', $today)
            ->sum('total_harga');

        $pendapatanBulanIni = (int) Transaksi::query()
            ->where('status_bayar', Transaksi::BAYAR_LUNAS)
            ->whereYear('tanggal_masuk', now()->year)
            ->whereMonth('tanggal_masuk', now()->month)
            ->sum('total_harga');

        $orderMasuk = Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_MASUK)->count();
        $orderProses = Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_PROSES)->count();
        $orderSelesai = Transaksi::query()->where('status_pengerjaan', Transaksi::STATUS_SELESAI)->count();
        $belumLunasCount = Transaksi::query()->where('status_bayar', Transaksi::BAYAR_BELUM_LUNAS)->count();
        $totalPelanggan = Pelanggan::query()->count();

        $antreanAktif = Transaksi::query()
            ->with(['pelanggan', 'layanan', 'user'])
            ->whereIn('status_pengerjaan', [Transaksi::STATUS_MASUK, Transaksi::STATUS_PROSES])
            ->orderBy('estimasi_selesai_at')
            ->orderBy('id')
            ->limit(8)
            ->get();

        $ringkasanLayanan = Layanan::query()
            ->withCount('transaksis')
            ->withSum('transaksis as total_berat', 'berat_kg')
            ->withSum([
                'transaksis as total_omzet' => fn ($query) => $query->where('status_bayar', Transaksi::BAYAR_LUNAS),
            ], 'total_harga')
            ->orderBy('harga_per_kg')
            ->get();

        return view('dashboard.index', [
            'pendapatanHariIni' => $pendapatanHariIni,
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'orderMasuk' => $orderMasuk,
            'orderProses' => $orderProses,
            'orderSelesai' => $orderSelesai,
            'belumLunasCount' => $belumLunasCount,
            'totalPelanggan' => $totalPelanggan,
            'antreanAktif' => $antreanAktif,
            'ringkasanLayanan' => $ringkasanLayanan,
        ]);
    }
}
