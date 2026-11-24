<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_can_create_transaction_and_progress_status_to_selesai(): void
    {
        $kasir = User::factory()->kasir()->create();
        $ekspress = Layanan::query()->create([
            'kode_layanan' => 'EXP',
            'nama_layanan' => 'Ekspress',
            'harga_per_kg' => 12000,
            'estimasi_durasi' => '1-2 hari',
            'estimasi_jam' => 36,
            'is_active' => true,
        ]);

        $this->actingAs($kasir);

        $response = $this->post(route('transaksi.store'), [
            'nama_pelanggan' => 'Nadia Kusuma',
            'no_hp' => '+62 812-3456-7890',
            'alamat' => 'Jl. Melati No. 5',
            'layanan_id' => $ekspress->id,
            'berat_kg' => 3.5,
            'status_pengerjaan' => Transaksi::STATUS_MASUK,
            'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
            'catatan' => 'Jangan pakai pemutih',
        ]);

        $transaksi = Transaksi::query()->firstOrFail();
        $response->assertRedirect(route('transaksi.show', $transaksi));

        // Relasi & normalisasi HP & kalkulasi tarif otomatis (3.5 * 12000 = 42000)
        $this->assertSame('081234567890', $transaksi->pelanggan->no_hp);
        $this->assertSame($ekspress->id, $transaksi->layanan_id);
        $this->assertSame(12000, $transaksi->harga_satuan);
        $this->assertSame(42000, $transaksi->total_harga);
        $this->assertSame(Transaksi::STATUS_MASUK, $transaksi->status_pengerjaan);
        $this->assertNull($transaksi->tanggal_selesai);

        // Repeat customer with same phone should reuse pelanggan row
        $this->post(route('transaksi.store'), [
            'nama_pelanggan' => 'Nadia Kusuma',
            'no_hp' => '081234567890',
            'layanan_id' => $ekspress->id,
            'berat_kg' => 2.0,
            'status_bayar' => Transaksi::BAYAR_LUNAS,
        ]);
        $this->assertSame(1, Pelanggan::query()->count());

        // Masuk -> Proses
        $this->patch(route('transaksi.status', $transaksi), [
            'status_pengerjaan' => Transaksi::STATUS_PROSES,
        ])->assertRedirect();
        $transaksi->refresh();
        $this->assertSame(Transaksi::STATUS_PROSES, $transaksi->status_pengerjaan);
        $this->assertNull($transaksi->tanggal_selesai);

        // Proses -> Selesai
        $this->patch(route('transaksi.status', $transaksi), [
            'status_pengerjaan' => Transaksi::STATUS_SELESAI,
        ])->assertRedirect();
        $transaksi->refresh();
        $this->assertSame(Transaksi::STATUS_SELESAI, $transaksi->status_pengerjaan);
        $this->assertNotNull($transaksi->tanggal_selesai);

        // Belum lunas -> Lunas
        $this->patch(route('transaksi.pembayaran', $transaksi), [
            'status_bayar' => Transaksi::BAYAR_LUNAS,
        ])->assertRedirect();
        $transaksi->refresh();
        $this->assertTrue($transaksi->isLunas());

        // Printable nota & detail page
        $this->get(route('transaksi.show', $transaksi))
            ->assertOk()
            ->assertSee($transaksi->kode_transaksi)
            ->assertSee('Nadia Kusuma');

        $this->get(route('transaksi.nota', $transaksi))
            ->assertOk()
            ->assertSee($transaksi->kode_transaksi)
            ->assertSee('Nadia Kusuma')
            ->assertSee('42.000');

        // Inactive service is rejected
        $inactive = Layanan::query()->create([
            'kode_layanan' => 'OFF',
            'nama_layanan' => 'Nonaktif',
            'harga_per_kg' => 9000,
            'estimasi_durasi' => '2 hari',
            'estimasi_jam' => 48,
            'is_active' => false,
        ]);
        $this->post(route('transaksi.store'), [
            'nama_pelanggan' => 'Test',
            'no_hp' => '081299998888',
            'layanan_id' => $inactive->id,
            'berat_kg' => 1.5,
            'status_bayar' => Transaksi::BAYAR_LUNAS,
        ])->assertSessionHasErrors('layanan_id');

        // Kasir cannot delete transaksi, but Admin can
        $this->delete(route('transaksi.destroy', $transaksi))->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->delete(route('transaksi.destroy', $transaksi))
            ->assertRedirect(route('transaksi.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('transaksis', ['id' => $transaksi->id]);
    }
}
