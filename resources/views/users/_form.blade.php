@php
    $isEdit = isset($user);
    $isSelf = $isEdit && $user->id === auth()->id();
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="block text-xs font-semibold text-ink">Nama Petugas <span class="text-rose-600">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold text-ink">Alamat Email <span class="text-rose-600">*</span></label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>

    <div>
        <label class="block text-xs font-semibold text-ink">Bagian / Peran <span class="text-rose-600">*</span></label>
        <div class="mt-1.5">
            <x-custom-select
                id="role"
                name="role"
                :disabled="$isSelf"
                :selected="old('role', $user->role ?? 'kasir')"
                :options="[
                    ['value' => 'kasir', 'label' => 'Kasir'],
                    ['value' => 'admin', 'label' => 'Admin'],
                ]" />
        </div>
        @if ($isSelf)
            <p class="mt-1 text-[11px] text-ink-muted">Peran akunmu sendiri tidak bisa diganti dari sini.</p>
        @endif
    </div>

    <div class="flex items-end">
        <label class="inline-flex w-full items-center gap-2.5 rounded-xl border border-border bg-canvas px-4 py-2.5 text-sm font-medium text-ink">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $isEdit ? $user->is_active : true))
                   @disabled($isSelf)
                   class="h-4 w-4 rounded border-border text-brand focus:ring-brand">
            <span>Izinkan masuk ke aplikasi</span>
        </label>
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold text-ink">
            Kata Sandi {{ $isEdit ? '(Biarkan kosong kalau tidak diganti)' : '*' }}
        </label>
        <input id="password" name="password" type="password" {{ $isEdit ? '' : 'required' }}
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-semibold text-ink">Ulangi Kata Sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ $isEdit ? '' : 'required' }}
               class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-border pt-5 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('users.index') }}"
       class="btn-tactile rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-sm font-semibold text-ink hover:bg-surface-muted">
        Batal
    </a>
    <button type="submit"
            class="btn-tactile rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">
        {{ $isEdit ? 'Simpan Perubahan' : 'Buat Akun Petugas' }}
    </button>
</div>
