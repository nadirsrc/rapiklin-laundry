<x-app-layout title="Ubah Paket Cuci" subtitle="Sesuaikan tarif atau perkiraan waktu beres untuk paket {{ $layanan->nama_layanan }}." scene="layanan">
    <div data-enter="1" class="max-w-3xl rounded-2xl border border-border bg-surface p-6 sm:p-8">
        <form method="POST" action="{{ route('layanan.update', $layanan) }}">
            @csrf
            @method('PUT')
            @include('layanan._form', ['layanan' => $layanan])
        </form>
    </div>
</x-app-layout>
