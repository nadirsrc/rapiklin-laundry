<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@rapiklin.test'],
            [
                'name' => 'Raka Pratama',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $kasir = User::query()->updateOrCreate(
            ['email' => 'kasir@rapiklin.test'],
            [
                'name' => 'Sinta Dewi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KASIR,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $layanans = [
            [
                'kode_layanan' => 'REG',
                'nama_layanan' => 'Reguler',
                'harga_per_kg' => 7000,
                'estimasi_durasi' => '3-4 hari',
                'estimasi_jam' => 84,
                'deskripsi' => 'Cuci komplit, pengeringan higienis, setrika uap, dan lipat rapi standar harian.',
                'is_active' => true,
            ],
            [
                'kode_layanan' => 'EXP',
                'nama_layanan' => 'Ekspress',
                'harga_per_kg' => 12000,
                'estimasi_durasi' => '1-2 hari',
                'estimasi_jam' => 36,
                'deskripsi' => 'Antrean prioritas dengan pewangi premium dan pengemasan rapi lebih cepat.',
                'is_active' => true,
            ],
            [
                'kode_layanan' => 'SKL',
                'nama_layanan' => 'Superkilat',
                'harga_per_kg' => 25000,
                'estimasi_durasi' => '5 jam selesai',
                'estimasi_jam' => 5,
                'deskripsi' => 'Layanan kilat 5 jam selesai dengan mesin terpisah untuk kebutuhan mendesak.',
                'is_active' => true,
            ],
        ];

        $layananModels = [];
        foreach ($layanans as $item) {
            $layananModels[$item['kode_layanan']] = Layanan::query()->updateOrCreate(
                ['kode_layanan' => $item['kode_layanan']],
                $item
            );
        }

        if (Transaksi::query()->exists()) {
            return;
        }

        $pelanggans = [
            ['nama' => 'Budi Santoso', 'no_hp' => '081234567890', 'alamat' => 'Jl. Melati No. 14'],
            ['nama' => 'Nadia Kusuma', 'no_hp' => '082198765432', 'alamat' => 'Perum Griya Asri Blok B3'],
            ['nama' => 'Fajar Nugroho', 'no_hp' => '085611223344', 'alamat' => 'Jl. Kenanga No. 8'],
            ['nama' => 'Maya Puspita', 'no_hp' => '087744556677', 'alamat' => 'Kost Putri Harmoni No. 5'],
            ['nama' => 'Dimas Saputra', 'no_hp' => '081399887766', 'alamat' => 'Jl. Diponegoro No. 27'],
        ];

        $pelangganModels = [];
        foreach ($pelanggans as $p) {
            $pelangganModels[] = Pelanggan::query()->updateOrCreate(
                ['no_hp' => $p['no_hp']],
                $p
            );
        }

        $samples = [
            [
                'kode_transaksi' => 'LND-'.now()->subDays(2)->format('Ymd').'-0001',
                'pelanggan' => $pelangganModels[0],
                'layanan' => $layananModels['REG'],
                'user' => $kasir,
                'berat_kg' => 4.5,
                'status_pengerjaan' => Transaksi::STATUS_SELESAI,
                'status_bayar' => Transaksi::BAYAR_LUNAS,
                'tanggal_masuk' => now()->subDays(2)->setTime(9, 15),
                'tanggal_selesai' => now()->subHours(4),
                'catatan' => 'Pisahkan kemeja putih',
            ],
            [
                'kode_transaksi' => 'LND-'.now()->subDay()->format('Ymd').'-0001',
                'pelanggan' => $pelangganModels[1],
                'layanan' => $layananModels['EXP'],
                'user' => $kasir,
                'berat_kg' => 3.0,
                'status_pengerjaan' => Transaksi::STATUS_SELESAI,
                'status_bayar' => Transaksi::BAYAR_LUNAS,
                'tanggal_masuk' => now()->subDay()->setTime(13, 30),
                'tanggal_selesai' => now()->subHours(2),
                'catatan' => 'Pewangi lavender',
            ],
            [
                'kode_transaksi' => 'LND-'.now()->format('Ymd').'-0001',
                'pelanggan' => $pelangganModels[2],
                'layanan' => $layananModels['SKL'],
                'user' => $kasir,
                'berat_kg' => 2.5,
                'status_pengerjaan' => Transaksi::STATUS_PROSES,
                'status_bayar' => Transaksi::BAYAR_LUNAS,
                'tanggal_masuk' => now()->subHours(2),
                'tanggal_selesai' => null,
                'catatan' => 'Dipakai sore ini',
            ],
            [
                'kode_transaksi' => 'LND-'.now()->format('Ymd').'-0002',
                'pelanggan' => $pelangganModels[3],
                'layanan' => $layananModels['EXP'],
                'user' => $admin,
                'berat_kg' => 5.0,
                'status_pengerjaan' => Transaksi::STATUS_PROSES,
                'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
                'tanggal_masuk' => now()->subHours(3),
                'tanggal_selesai' => null,
                'catatan' => 'Bayar saat ambil',
            ],
            [
                'kode_transaksi' => 'LND-'.now()->format('Ymd').'-0003',
                'pelanggan' => $pelangganModels[4],
                'layanan' => $layananModels['REG'],
                'user' => $kasir,
                'berat_kg' => 6.0,
                'status_pengerjaan' => Transaksi::STATUS_MASUK,
                'status_bayar' => Transaksi::BAYAR_BELUM_LUNAS,
                'tanggal_masuk' => now()->subMinutes(40),
                'tanggal_selesai' => null,
                'catatan' => 'Sprei dan pakaian harian',
            ],
        ];

        foreach ($samples as $sample) {
            $layanan = $sample['layanan'];
            $tanggalMasuk = $sample['tanggal_masuk'];
            $totalHarga = Transaksi::hitungTotal($sample['berat_kg'], $layanan->harga_per_kg);

            Transaksi::query()->create([
                'kode_transaksi' => $sample['kode_transaksi'],
                'pelanggan_id' => $sample['pelanggan']->id,
                'layanan_id' => $layanan->id,
                'user_id' => $sample['user']->id,
                'berat_kg' => $sample['berat_kg'],
                'harga_satuan' => $layanan->harga_per_kg,
                'total_harga' => $totalHarga,
                'status_pengerjaan' => $sample['status_pengerjaan'],
                'status_bayar' => $sample['status_bayar'],
                'tanggal_masuk' => $tanggalMasuk,
                'estimasi_selesai_at' => $tanggalMasuk->copy()->addHours($layanan->estimasi_jam),
                'tanggal_selesai' => $sample['tanggal_selesai'],
                'catatan' => $sample['catatan'],
            ]);
        }
    }
}
