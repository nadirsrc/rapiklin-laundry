<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_layanan',
    'nama_layanan',
    'harga_per_kg',
    'estimasi_durasi',
    'estimasi_jam',
    'deskripsi',
    'is_active',
])]
class Layanan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'harga_per_kg' => 'integer',
            'estimasi_jam' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function formattedHarga(): string
    {
        return 'Rp '.number_format($this->harga_per_kg, 0, ',', '.').'/kg';
    }
}
