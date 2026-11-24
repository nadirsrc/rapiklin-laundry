<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 32)->unique();
            $table->foreignId('pelanggan_id')
                ->constrained('pelanggans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('layanan_id')
                ->constrained('layanans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('berat_kg', 8, 2);
            $table->unsignedInteger('harga_satuan');
            $table->unsignedInteger('total_harga');
            $table->string('status_pengerjaan', 20)->default('masuk');
            $table->string('status_bayar', 20)->default('belum_lunas');
            $table->dateTime('tanggal_masuk');
            $table->dateTime('estimasi_selesai_at')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status_pengerjaan');
            $table->index('status_bayar');
            $table->index('tanggal_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
