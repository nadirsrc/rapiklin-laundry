<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class LaporanExportService
{
    /**
     * Stream an Excel-friendly UTF-8 CSV (semicolon-delimited for Indonesian Excel regional defaults).
     *
     * @param  Collection<int, Transaksi>  $rows
     */
    public function downloadCsv(Collection $rows, ?string $startDate, ?string $endDate): StreamedResponse
    {
        $filename = 'rekap-laundry-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows, $startDate, $endDate): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM + semicolon delimiter ensures Indonesian Windows Excel splits columns automatically.
            fwrite($handle, "\xEF\xBB\xBF");

            $periodeText = ($startDate ?: 'Awal').' s/d '.($endDate ?: 'Sekarang');
            fputcsv($handle, ['LAPORAN REKAP TRANSAKSI — RAPIKLIN LAUNDRY'], ';');
            fputcsv($handle, ['Periode', $periodeText, 'Dicetak', now()->format('d/m/Y H:i')], ';');
            fputcsv($handle, [], ';');

            fputcsv($handle, [
                'No',
                'Kode Transaksi',
                'Tanggal Masuk',
                'Nama Pelanggan',
                'No HP',
                'Jenis Layanan',
                'Berat (kg)',
                'Harga per kg (Rp)',
                'Total Harga (Rp)',
                'Status Pengerjaan',
                'Status Bayar',
                'Petugas',
            ], ';');

            $totalBerat = 0.0;
            $totalNilai = 0;
            $totalLunas = 0;

            foreach ($rows as $index => $row) {
                $totalBerat += (float) $row->berat_kg;
                $totalNilai += (int) $row->total_harga;
                if ($row->isLunas()) {
                    $totalLunas += (int) $row->total_harga;
                }

                // Formula-text guard keeps leading zero in Excel without altering visible text.
                $phoneCell = '="'.$row->pelanggan->no_hp.'"';

                fputcsv($handle, [
                    $index + 1,
                    $row->kode_transaksi,
                    $row->tanggal_masuk->format('d/m/Y H:i'),
                    $row->pelanggan->nama,
                    $phoneCell,
                    $row->layanan->nama_layanan,
                    number_format($row->berat_kg, 2, ',', ''),
                    $row->harga_satuan,
                    $row->total_harga,
                    $row->statusLabel(),
                    $row->bayarLabel(),
                    $row->user->name,
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, [
                '',
                'TOTAL',
                $rows->count().' nota',
                '',
                '',
                '',
                number_format($totalBerat, 2, ',', ''),
                '',
                $totalNilai,
                'Lunas: Rp '.number_format($totalLunas, 0, ',', '.'),
                'Belum Lunas: Rp '.number_format($totalNilai - $totalLunas, 0, ',', '.'),
                '',
            ], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Build and stream a native styled .xlsx spreadsheet using ZipArchive (no external libraries required).
     *
     * @param  Collection<int, Transaksi>  $rows
     */
    public function downloadXlsx(Collection $rows, ?string $startDate, ?string $endDate): StreamedResponse
    {
        $filename = 'rekap-laundry-'.now()->format('Ymd-His').'.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'laundry_xlsx_');
        if ($tempPath === false) {
            throw new RuntimeException('Gagal membuat berkas sementara untuk ekspor Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            @unlink($tempPath);
            throw new RuntimeException('Gagal mengemas berkas Excel (.xlsx).');
        }

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->buildRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->buildStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($rows, $startDate, $endDate));
        $zip->close();

        return response()->streamDownload(function () use ($tempPath): void {
            readfile($tempPath);
            @unlink($tempPath);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  Collection<int, Transaksi>  $rows
     */
    private function buildSheetXml(Collection $rows, ?string $startDate, ?string $endDate): string
    {
        $periodeText = ($startDate ?: 'Awal').' s/d '.($endDate ?: 'Sekarang');
        $printedAt = now()->format('d/m/Y H:i');

        $cols = [
            ['w' => 6],
            ['w' => 21],
            ['w' => 18],
            ['w' => 22],
            ['w' => 16],
            ['w' => 16],
            ['w' => 12],
            ['w' => 17],
            ['w' => 18],
            ['w' => 16],
            ['w' => 15],
            ['w' => 18],
        ];

        $colXml = '';
        foreach ($cols as $idx => $col) {
            $n = $idx + 1;
            $colXml .= '<col min="'.$n.'" max="'.$n.'" width="'.$col['w'].'" customWidth="1"/>';
        }

        $sheetRows = [];
        $sheetRows[] = $this->makeRowXml(1, [
            ['type' => 's', 'val' => 'LAPORAN REKAP TRANSAKSI — RAPIKLIN LAUNDRY', 'style' => 1],
        ], 26);
        $sheetRows[] = $this->makeRowXml(2, [
            ['type' => 's', 'val' => 'Periode: '.$periodeText.'   |   Dicetak: '.$printedAt, 'style' => 2],
        ], 19);
        $sheetRows[] = $this->makeRowXml(3, [], 10);

        $headers = [
            'No',
            'Kode Transaksi',
            'Tanggal Masuk',
            'Nama Pelanggan',
            'No HP',
            'Paket Layanan',
            'Berat (kg)',
            'Harga / kg',
            'Total Tagihan',
            'Progres',
            'Pembayaran',
            'Petugas',
        ];
        $headerCells = array_map(
            fn (string $label): array => ['type' => 's', 'val' => $label, 'style' => 3],
            $headers
        );
        $sheetRows[] = $this->makeRowXml(4, $headerCells, 24);

        $rowNum = 5;
        $totalBerat = 0.0;
        $totalNilai = 0;
        $totalLunas = 0;

        foreach ($rows as $index => $trx) {
            $totalBerat += (float) $trx->berat_kg;
            $totalNilai += (int) $trx->total_harga;
            if ($trx->isLunas()) {
                $totalLunas += (int) $trx->total_harga;
            }

            $sheetRows[] = $this->makeRowXml($rowNum, [
                ['type' => 'n', 'val' => $index + 1, 'style' => 4],
                ['type' => 's', 'val' => $trx->kode_transaksi, 'style' => 4],
                ['type' => 's', 'val' => $trx->tanggal_masuk->format('d/m/Y H:i'), 'style' => 4],
                ['type' => 's', 'val' => $trx->pelanggan->nama, 'style' => 4],
                ['type' => 's', 'val' => $trx->pelanggan->no_hp, 'style' => 4],
                ['type' => 's', 'val' => $trx->layanan->nama_layanan, 'style' => 4],
                ['type' => 'n', 'val' => number_format($trx->berat_kg, 2, '.', ''), 'style' => 5],
                ['type' => 'n', 'val' => $trx->harga_satuan, 'style' => 6],
                ['type' => 'n', 'val' => $trx->total_harga, 'style' => 6],
                ['type' => 's', 'val' => $trx->statusLabel(), 'style' => 4],
                ['type' => 's', 'val' => $trx->bayarLabel(), 'style' => 4],
                ['type' => 's', 'val' => $trx->user->name, 'style' => 4],
            ], 20);
            $rowNum++;
        }

        // Summary Row
        $sheetRows[] = $this->makeRowXml($rowNum, [
            ['type' => 's', 'val' => '', 'style' => 7],
            ['type' => 's', 'val' => 'TOTAL REKAP', 'style' => 7],
            ['type' => 's', 'val' => $rows->count().' nota', 'style' => 7],
            ['type' => 's', 'val' => '', 'style' => 7],
            ['type' => 's', 'val' => '', 'style' => 7],
            ['type' => 's', 'val' => '', 'style' => 7],
            ['type' => 'n', 'val' => number_format($totalBerat, 2, '.', ''), 'style' => 8],
            ['type' => 's', 'val' => '', 'style' => 7],
            ['type' => 'n', 'val' => $totalNilai, 'style' => 9],
            ['type' => 's', 'val' => 'Lunas: Rp '.number_format($totalLunas, 0, ',', '.'), 'style' => 7],
            ['type' => 's', 'val' => 'Sisa: Rp '.number_format($totalNilai - $totalLunas, 0, ',', '.'), 'style' => 7],
            ['type' => 's', 'val' => '', 'style' => 7],
        ], 22);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="19"/>'
            .'<cols>'.$colXml.'</cols>'
            .'<sheetData>'.implode('', $sheetRows).'</sheetData>'
            .'<mergeCells count="2"><mergeCell ref="A1:L1"/><mergeCell ref="A2:L2"/></mergeCells>'
            .'<pageSetup orientation="landscape" paperSize="9"/>'
            .'</worksheet>';
    }

    /**
     * @param  array<int, array{type: string, val: mixed, style: int}>  $cells
     */
    private function makeRowXml(int $rowIndex, array $cells, int $height): string
    {
        $cellXml = '';
        foreach ($cells as $colIndex => $cell) {
            $colRef = $this->colLetter($colIndex).$rowIndex;
            $style = $cell['style'];
            if ($cell['type'] === 'n') {
                $cellXml .= '<c r="'.$colRef.'" s="'.$style.'"><v>'.$cell['val'].'</v></c>';
            } else {
                $escaped = htmlspecialchars((string) $cell['val'], ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $cellXml .= '<c r="'.$colRef.'" t="inlineStr" s="'.$style.'"><is><t>'.$escaped.'</t></is></c>';
            }
        }

        return '<row r="'.$rowIndex.'" ht="'.$height.'" customHeight="1">'.$cellXml.'</row>';
    }

    private function colLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26) - 1;
        }

        return $letter;
    }

    private function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="2">'
            .'<numFmt numFmtId="164" formatCode="0.00"/>'
            .'<numFmt numFmtId="165" formatCode="&quot;Rp &quot;#,##0"/>'
            .'</numFmts>'
            .'<fonts count="5">'
            .'<font><sz val="10"/><name val="Calibri"/><color rgb="FF0F172A"/></font>'
            .'<font><b/><sz val="13"/><name val="Calibri"/><color rgb="FF1E4B85"/></font>'
            .'<font><sz val="10"/><name val="Calibri"/><color rgb="FF516075"/></font>'
            .'<font><b/><sz val="10"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>'
            .'<font><b/><sz val="10"/><name val="Calibri"/><color rgb="FF0F172A"/></font>'
            .'</fonts>'
            .'<fills count="4">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF1E4B85"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF3F6FA"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border>'
            .'<left style="thin"><color rgb="FFD9E2EE"/></left>'
            .'<right style="thin"><color rgb="FFD9E2EE"/></right>'
            .'<top style="thin"><color rgb="FFD9E2EE"/></top>'
            .'<bottom style="thin"><color rgb="FFD9E2EE"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="10">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="right"/></xf>'
            .'<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="right"/></xf>'
            .'<xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="right"/></xf>'
            .'<xf numFmtId="165" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="right"/></xf>'
            .'</cellXfs>'
            .'</styleSheet>';
    }

    private function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function buildWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Rekap Transaksi" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }
}
