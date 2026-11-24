<x-app-layout title="Tambah Paket Cuci" subtitle="Buat pilihan paket cuci kiloan baru lengkap dengan harga dan perkiraan selesainya." scene="layanan">
    <div data-enter="1" class="max-w-3xl rounded-2xl border border-border bg-surface p-6 sm:p-8">
        <form method="POST" action="{{ route('layanan.store') }}">
            @csrf
            @include('layanan._form')
        </form>
    </div>
</x-app-layout>
