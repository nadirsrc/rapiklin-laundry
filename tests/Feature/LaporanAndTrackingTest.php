<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanAndTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_laporan_displays_relational_breakdown_filters_and_csv_export(): void
    {
        $admin = User::factory()->admin()->create();
        $reguler = Layanan::query()->create([
            'kode_layanan' => 'REG',
            'nama_layanan' => 'Reguler',
            'harga_per_kg' => 7000,
            'estimasi_durasi' => '3-4 hari',
            'estimasi_jam' => 84,
            'is_active' => true,
        ]);
        $superkilat = Layanan::query()->create([
            'kode_layanan' => 'SKL',
            'nama_layanan' => 'Superkilat',
            'harga_per_kg' => 25000,
            'estimasi_durasi' => '5 jam selesai',
            'estimasi_jam' => 5,
            'is_active' => true,
        ]);

        $pelanggan = Pelanggan::query()->create([
            'nama' => 'Dimas Saputra',
            'no_hp' => '081399887766',
        ]);

        Transaksi::query()->create([
            'kode_transaksi' => 'LND-20261124-0001',
            'pelanggan_id' => $pelanggan->id,
            'layanan_id' => $reguler->id,
            'user_id' => $admin->id,
            'berat_kg' => 4.0,
            'harga_satuan' => 7000,
            'total_harga' => 28000,
            'status_pengerjaan' => Transaksi::STATUS_SELESAI,
            'status_bayar' => Transaksi::BAYAR_LUNAS,
            'tanggal_masuk' => now(),
        ]);

        Transaksi::query()->create([
            'kode_transaksi' => 'LND-20261124-0002',
            'pelanggan_id' => $pelanggan->id,
            'layanan_id' => $superkilat->id,
            'user_id' => $admin->id,
            'berat_kg' => 2.0,
            'harga_satuan' => 25000,
            'total_harga' => 50000,
            'status_pengerjaan' => Transaksi::STATUS_PROSES,
            'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
            'tanggal_masuk' => now(),
        ]);

        $this->actingAs($admin);

        // Full report shows both layanan relations
        $this->get(route('laporan.index', ['periode' => 'semua']))
            ->assertOk()
            ->assertSee('Reguler')
            ->assertSee('Superkilat')
            ->assertSee('28.000')
            ->assertSee('50.000')
            ->assertSee('78.000');

        // Filtered by layanan_id shows only matching relation
        $this->get(route('laporan.index', ['periode' => 'semua', 'layanan_id' => $superkilat->id]))
            ->assertOk()
            ->assertSee('LND-20261124-0002')
            ->assertDontSee('LND-20261124-0001');

        // CSV Export (semicolon-delimited + UTF-8 BOM + phone text guard + summary row)
        $csvResponse = $this->get(route('laporan.export', ['periode' => 'semua']));
        $csvResponse->assertOk();
        $this->assertStringContainsString('text/csv', (string) $csvResponse->headers->get('content-type'));
        $content = $csvResponse->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString(';', $content);
        $this->assertStringContainsString('LND-20261124-0001', $content);
        $this->assertStringContainsString('Superkilat', $content);
        $this->assertStringContainsString('081399887766', $content);
        $this->assertStringContainsString('TOTAL', $content);

        // Native Excel (.xlsx) Export
        $xlsxResponse = $this->get(route('laporan.export.xlsx', ['periode' => 'semua']));
        $xlsxResponse->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $xlsxResponse->headers->get('content-type')
        );
        $xlsxBytes = $xlsxResponse->streamedContent();
        $tmpXlsx = tempnam(sys_get_temp_dir(), 'test_xlsx_');
        $this->assertNotFalse($tmpXlsx);
        file_put_contents($tmpXlsx, $xlsxBytes);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tmpXlsx) === true);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $stylesXml = $zip->getFromName('xl/styles.xml');
        $zip->close();
        @unlink($tmpXlsx);

        $this->assertIsString($sheetXml);
        $this->assertIsString($stylesXml);
        $this->assertNotFalse(@simplexml_load_string($sheetXml));
        $this->assertNotFalse(@simplexml_load_string($stylesXml));
        $this->assertStringContainsString('LND-20261124-0001', $sheetXml);
        $this->assertStringContainsString('Superkilat', $sheetXml);
        $this->assertStringContainsString('TOTAL REKAP', $sheetXml);
    }

    public function test_public_cek_cucian_finds_order_by_code_or_phone(): void
    {
        $kasir = User::factory()->kasir()->create();
        $layanan = Layanan::query()->create([
            'kode_layanan' => 'REG',
            'nama_layanan' => 'Reguler',
            'harga_per_kg' => 7000,
            'estimasi_durasi' => '3-4 hari',
            'estimasi_jam' => 84,
            'is_active' => true,
        ]);
        $pelanggan = Pelanggan::query()->create([
            'nama' => 'Maya Puspita',
            'no_hp' => '087744556677',
        ]);

        Transaksi::query()->create([
            'kode_transaksi' => 'LND-20261124-0099',
            'pelanggan_id' => $pelanggan->id,
            'layanan_id' => $layanan->id,
            'user_id' => $kasir->id,
            'berat_kg' => 3.0,
            'harga_satuan' => 7000,
            'total_harga' => 21000,
            'status_pengerjaan' => Transaksi::STATUS_PROSES,
            'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
            'tanggal_masuk' => now(),
        ]);

        $this->get(route('cek-cucian', ['q' => 'LND-20261124-0099']))
            ->assertOk()
            ->assertSee('Maya Puspita')
            ->assertSee('Proses')
            ->assertSee('21.000');

        $this->get(route('cek-cucian', ['q' => '0877-4455-6677']))
            ->assertOk()
            ->assertSee('LND-20261124-0099');
    }
}
