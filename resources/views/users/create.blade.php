<x-app-layout title="Tambah Petugas" subtitle="Buat akun login baru buat Admin atau Kasir toko." scene="users">
    <div data-enter="1" class="max-w-3xl rounded-2xl border border-border bg-surface p-6 sm:p-8">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form')
        </form>
    </div>
</x-app-layout>
