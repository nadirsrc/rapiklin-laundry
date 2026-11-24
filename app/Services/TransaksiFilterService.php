<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransaksiFilterService
{
    /**
     * Build a reusable filtered query for transaction lists, reports, and exports.
     */
    public function apply(Request $request, ?string $defaultStartDate = null, ?string $defaultEndDate = null): Builder
    {
        $query = Transaksi::query()
            ->with(['pelanggan', 'layanan', 'user'])
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id');

        if ($search = trim((string) $request->input('search', ''))) {
            $normalizedPhone = Pelanggan::normalizePhone($search);
            $query->where(function (Builder $q) use ($search, $normalizedPhone): void {
                $q->where('kode_transaksi', 'like', '%'.$search.'%')
                    ->orWhereHas('pelanggan', function (Builder $pq) use ($search, $normalizedPhone): void {
                        $pq->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('no_hp', 'like', '%'.$search.'%');
                        if ($normalizedPhone !== '' && $normalizedPhone !== $search) {
                            $pq->orWhere('no_hp', 'like', '%'.$normalizedPhone.'%');
                        }
                    });
            });
        }

        if ($layananId = $request->input('layanan_id')) {
            $query->where('layanan_id', $layananId);
        }

        if ($status = $request->input('status_pengerjaan')) {
            $query->where('status_pengerjaan', $status);
        }

        if ($bayar = $request->input('status_bayar')) {
            $query->where('status_bayar', $bayar);
        }

        $startDate = $request->has('start_date')
            ? $request->input('start_date')
            : $defaultStartDate;
        $endDate = $request->has('end_date')
            ? $request->input('end_date')
            : $defaultEndDate;

        if ($startDate) {
            $query->whereDate('tanggal_masuk', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_masuk', '<=', $endDate);
        }

        return $query;
    }
}
