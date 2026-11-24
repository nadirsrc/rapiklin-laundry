<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use App\Services\LaporanExportService;
use App\Services\TransaksiFilterService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        private readonly TransaksiFilterService $filterService,
        private readonly LaporanExportService $exportService
    ) {
    }

    public function index(Request $request): View
    {
        [$defaultStart, $defaultEnd] = $this->resolveDefaultDates($request);
        $baseQuery = $this->filterService->apply($request, $defaultStart, $defaultEnd);

        $aggregateRow = (clone $baseQuery)
            ->reorder()
            ->selectRaw(
                'COUNT(*) as total_transaksi,
                COALESCE(SUM(berat_kg), 0) as total_berat,
                COALESCE(SUM(total_harga), 0) as total_nilai,
                COALESCE(SUM(CASE WHEN status_bayar = ? THEN total_harga ELSE 0 END), 0) as total_lunas,
                COALESCE(SUM(CASE WHEN status_bayar = ? THEN total_harga ELSE 0 END), 0) as total_belum_lunas',
                [Transaksi::BAYAR_LUNAS, Transaksi::BAYAR_BELUM_LUNAS]
            )
            ->first();

        $summary = [
            'total_transaksi' => (int) ($aggregateRow->total_transaksi ?? 0),
            'total_berat' => (float) ($aggregateRow->total_berat ?? 0),
            'total_nilai' => (int) ($aggregateRow->total_nilai ?? 0),
            'total_lunas' => (int) ($aggregateRow->total_lunas ?? 0),
            'total_belum_lunas' => (int) ($aggregateRow->total_belum_lunas ?? 0),
        ];

        $rekapPerLayanan = (clone $baseQuery)
            ->reorder()
            ->selectRaw(
                'layanan_id,
                COUNT(*) as jumlah_order,
                COALESCE(SUM(berat_kg), 0) as total_berat,
                COALESCE(SUM(total_harga), 0) as total_nilai,
                COALESCE(SUM(CASE WHEN status_bayar = ? THEN total_harga ELSE 0 END), 0) as total_lunas',
                [Transaksi::BAYAR_LUNAS]
            )
            ->groupBy('layanan_id')
            ->with('layanan')
            ->get();

        $transaksis = $baseQuery->paginate(15)->withQueryString();
        $layanans = Layanan::query()->orderBy('harga_per_kg')->get(['id', 'nama_layanan']);

        $activeStartDate = $request->has('start_date') ? $request->input('start_date') : $defaultStart;
        $activeEndDate = $request->has('end_date') ? $request->input('end_date') : $defaultEnd;

        return view('laporan.index', compact(
            'transaksis',
            'summary',
            'rekapPerLayanan',
            'layanans',
            'activeStartDate',
            'activeEndDate'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$defaultStart, $defaultEnd] = $this->resolveDefaultDates($request);
        $rows = $this->filterService->apply($request, $defaultStart, $defaultEnd)->get();
        $activeStart = $request->has('start_date') ? $request->input('start_date') : $defaultStart;
        $activeEnd = $request->has('end_date') ? $request->input('end_date') : $defaultEnd;

        return $this->exportService->downloadCsv($rows, $activeStart, $activeEnd);
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        [$defaultStart, $defaultEnd] = $this->resolveDefaultDates($request);
        $rows = $this->filterService->apply($request, $defaultStart, $defaultEnd)->get();
        $activeStart = $request->has('start_date') ? $request->input('start_date') : $defaultStart;
        $activeEnd = $request->has('end_date') ? $request->input('end_date') : $defaultEnd;

        return $this->exportService->downloadXlsx($rows, $activeStart, $activeEnd);
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveDefaultDates(Request $request): array
    {
        if ($request->input('periode') === 'semua') {
            return [null, null];
        }

        return [
            now()->startOfMonth()->toDateString(),
            now()->toDateString(),
        ];
    }
}
