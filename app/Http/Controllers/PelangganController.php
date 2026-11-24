<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $pelanggans = Pelanggan::query()
            ->withCount('transaksis')
            ->withSum('transaksis as total_belanja', 'total_harga')
            ->withSum([
                'transaksis as total_lunas' => fn ($query) => $query->where('status_bayar', Transaksi::BAYAR_LUNAS),
            ], 'total_harga')
            ->when($search !== '', function ($query) use ($search): void {
                $normalized = Pelanggan::normalizePhone($search);
                $query->where(function ($q) use ($search, $normalized): void {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('no_hp', 'like', '%'.$search.'%');
                    if ($normalized !== '' && $normalized !== $search) {
                        $q->orWhere('no_hp', 'like', '%'.$normalized.'%');
                    }
                });
            })
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return view('pelanggan.index', compact('pelanggans', 'search'));
    }

    public function show(Pelanggan $pelanggan): View
    {
        $transaksis = $pelanggan->transaksis()
            ->with(['layanan', 'user'])
            ->orderByDesc('tanggal_masuk')
            ->get();

        return view('pelanggan.show', compact('pelanggan', 'transaksis'));
    }
}
