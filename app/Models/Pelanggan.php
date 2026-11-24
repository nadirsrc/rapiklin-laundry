<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'no_hp', 'alamat'])]
class Pelanggan extends Model
{
    use HasFactory;

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Normalize phone numbers to avoid duplicate customer records when formatted with spaces or dashes.
     */
    public static function normalizePhone(string $rawPhone): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', trim($rawPhone)) ?? '';

        if (str_starts_with($cleaned, '+62')) {
            return '0'.substr($cleaned, 3);
        }

        if (str_starts_with($cleaned, '62') && strlen($cleaned) > 9) {
            return '0'.substr($cleaned, 2);
        }

        return $cleaned;
    }
}
