<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayananCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_unused_layanan(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        // Create
        $this->post(route('layanan.store'), [
            'kode_layanan' => 'skl',
            'nama_layanan' => 'Superkilat',
            'harga_per_kg' => 25000,
            'estimasi_durasi' => '5 jam selesai',
            'estimasi_jam' => 5,
            'deskripsi' => 'Selesai dalam 5 jam',
            'is_active' => '1',
        ])->assertRedirect(route('layanan.index'));

        $layanan = Layanan::query()->where('kode_layanan', 'SKL')->firstOrFail();
        $this->assertSame('Superkilat', $layanan->nama_layanan);
        $this->assertSame(25000, $layanan->harga_per_kg);

        // Update
        $this->put(route('layanan.update', $layanan), [
            'kode_layanan' => 'SKL',
            'nama_layanan' => 'Superkilat VIP',
            'harga_per_kg' => 27000,
            'estimasi_durasi' => '4 jam selesai',
            'estimasi_jam' => 4,
            'is_active' => '1',
        ])->assertRedirect(route('layanan.index'));

        $layanan->refresh();
        $this->assertSame('Superkilat VIP', $layanan->nama_layanan);
        $this->assertSame(27000, $layanan->harga_per_kg);

        // Delete unused
        $this->delete(route('layanan.destroy', $layanan))
            ->assertRedirect(route('layanan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('layanans', ['id' => $layanan->id]);
    }

    public function test_layanan_referenced_by_transaksi_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $layanan = Layanan::query()->create([
            'kode_layanan' => 'REG',
            'nama_layanan' => 'Reguler',
            'harga_per_kg' => 7000,
            'estimasi_durasi' => '3-4 hari',
            'estimasi_jam' => 84,
            'is_active' => true,
        ]);

        $pelanggan = Pelanggan::query()->create([
            'nama' => 'Budi',
            'no_hp' => '081234567890',
        ]);

        Transaksi::query()->create([
            'kode_transaksi' => 'LND-TEST-0001',
            'pelanggan_id' => $pelanggan->id,
            'layanan_id' => $layanan->id,
            'user_id' => $admin->id,
            'berat_kg' => 2.0,
            'harga_satuan' => 7000,
            'total_harga' => 14000,
            'status_pengerjaan' => Transaksi::STATUS_MASUK,
            'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
            'tanggal_masuk' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('layanan.destroy', $layanan))
            ->assertRedirect(route('layanan.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('layanans', ['id' => $layanan->id]);
    }
}
