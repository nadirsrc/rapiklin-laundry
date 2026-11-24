<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $layanans = Layanan::query()
            ->withCount('transaksis')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nama_layanan', 'like', '%'.$search.'%')
                    ->orWhere('kode_layanan', 'like', '%'.$search.'%');
            })
            ->orderBy('harga_per_kg')
            ->get();

        return view('layanan.index', compact('layanans', 'search'));
    }

    public function create(): View
    {
        return view('layanan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateLayanan($request);
        Layanan::query()->create($data);

        return redirect()->route('layanan.index')
            ->with('success', 'Data layanan "'.$data['nama_layanan'].'" berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan): View
    {
        return view('layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        $data = $this->validateLayanan($request, $layanan);
        $layanan->update($data);

        return redirect()->route('layanan.index')
            ->with('success', 'Data layanan "'.$layanan->nama_layanan.'" berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan): RedirectResponse
    {
        if ($layanan->transaksis()->exists()) {
            return redirect()->route('layanan.index')
                ->with('error', 'Layanan "'.$layanan->nama_layanan.'" tidak dapat dihapus karena sudah memiliki riwayat transaksi. Nonaktifkan layanan jika tidak ingin dipakai lagi.');
        }

        $nama = $layanan->nama_layanan;
        $layanan->delete();

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan "'.$nama.'" berhasil dihapus.');
    }

    /**
     * Validate and normalize service payload for both create and update operations.
     *
     * @return array<string, mixed>
     */
    private function validateLayanan(Request $request, ?Layanan $layanan = null): array
    {
        $request->merge([
            'kode_layanan' => strtoupper(trim((string) $request->input('kode_layanan', ''))),
        ]);

        $validated = $request->validate([
            'kode_layanan' => [
                'required',
                'string',
                'max:20',
                'alpha_dash',
                Rule::unique('layanans', 'kode_layanan')->ignore($layanan?->id),
            ],
            'nama_layanan' => ['required', 'string', 'max:100'],
            'harga_per_kg' => ['required', 'integer', 'min:500', 'max:1000000'],
            'estimasi_durasi' => ['required', 'string', 'max:60'],
            'estimasi_jam' => ['required', 'integer', 'min:1', 'max:720'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'kode_layanan.required' => 'Kode layanan wajib diisi.',
            'kode_layanan.unique' => 'Kode layanan sudah digunakan.',
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'harga_per_kg.required' => 'Harga per kg wajib diisi.',
            'harga_per_kg.integer' => 'Harga per kg harus berupa angka bulat.',
            'estimasi_durasi.required' => 'Label estimasi selesai wajib diisi.',
            'estimasi_jam.required' => 'Estimasi jam pengerjaan wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
