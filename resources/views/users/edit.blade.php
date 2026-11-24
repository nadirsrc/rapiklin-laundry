<x-app-layout title="Ubah Data Petugas" subtitle="Perbarui info akun atau ganti kata sandi milik {{ $user->name }}." scene="users">
    <div data-enter="1" class="max-w-3xl rounded-2xl border border-border bg-surface p-6 sm:p-8">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('users._form', ['user' => $user])
        </form>
    </div>
</x-app-layout>
