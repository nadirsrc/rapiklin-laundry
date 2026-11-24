<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CekCucianController extends Controller
{
    public function index(Request $request): View
    {
        $queryText = trim((string) $request->input('q', ''));
        $results = collect();

        if ($queryText !== '') {
            $normalizedPhone = Pelanggan::normalizePhone($queryText);

            $results = Transaksi::query()
                ->with(['pelanggan', 'layanan'])
                ->where(function ($query) use ($queryText, $normalizedPhone): void {
                    $query->where('kode_transaksi', $queryText)
                        ->orWhereHas('pelanggan', function ($pq) use ($queryText, $normalizedPhone): void {
                            $pq->where('no_hp', $queryText);
                            if ($normalizedPhone !== '') {
                                $pq->orWhere('no_hp', $normalizedPhone);
                            }
                        });
                })
                ->orderByDesc('tanggal_masuk')
                ->limit(10)
                ->get();
        }

        return view('public.cek-cucian', compact('queryText', 'results'));
    }
}
