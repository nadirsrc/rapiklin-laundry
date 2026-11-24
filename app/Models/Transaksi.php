<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'kode_transaksi',
    'pelanggan_id',
    'layanan_id',
    'user_id',
    'berat_kg',
    'harga_satuan',
    'total_harga',
    'status_pengerjaan',
    'status_bayar',
    'tanggal_masuk',
    'estimasi_selesai_at',
    'tanggal_selesai',
    'catatan',
])]
class Transaksi extends Model
{
    use HasFactory;

    public const STATUS_MASUK = 'masuk';
    public const STATUS_PROSES = 'proses';
    public const STATUS_SELESAI = 'selesai';

    public const BAYAR_BELUM_LUNAS = 'belum_lunas';
    public const BAYAR_LUNAS = 'lunas';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_MASUK => 'Masuk',
        self::STATUS_PROSES => 'Proses',
        self::STATUS_SELESAI => 'Selesai',
    ];

    /** @var array<string, string> */
    public const BAYAR_LABELS = [
        self::BAYAR_BELUM_LUNAS => 'Belum Lunas',
        self::BAYAR_LUNAS => 'Lunas',
    ];

    protected function casts(): array
    {
        return [
            'berat_kg' => 'float',
            'harga_satuan' => 'integer',
            'total_harga' => 'integer',
            'tanggal_masuk' => 'datetime',
            'estimasi_selesai_at' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status_pengerjaan] ?? ucfirst((string) $this->status_pengerjaan);
    }

    public function bayarLabel(): string
    {
        return self::BAYAR_LABELS[$this->status_bayar] ?? ucfirst((string) $this->status_bayar);
    }

    public function isLunas(): bool
    {
        return $this->status_bayar === self::BAYAR_LUNAS;
    }

    public function isSelesai(): bool
    {
        return $this->status_pengerjaan === self::STATUS_SELESAI;
    }

    public function nextStatus(): ?string
    {
        return match ($this->status_pengerjaan) {
            self::STATUS_MASUK => self::STATUS_PROSES,
            self::STATUS_PROSES => self::STATUS_SELESAI,
            default => null,
        };
    }

    public function nextStatusLabel(): ?string
    {
        $next = $this->nextStatus();

        return match ($next) {
            self::STATUS_PROSES => 'Mulai Proses',
            self::STATUS_SELESAI => 'Tandai Selesai',
            default => null,
        };
    }

    public static function hitungTotal(float $beratKg, int $hargaPerKg): int
    {
        return (int) round($beratKg * $hargaPerKg);
    }

    public static function generateKodeTransaksi(): string
    {
        $prefix = 'LND-'.now()->format('Ymd').'-';
        $latest = static::query()
            ->where('kode_transaksi', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('kode_transaksi');

        $nextNumber = 1;
        if ($latest) {
            $suffix = (int) Str::afterLast($latest, '-');
            $nextNumber = $suffix + 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
