<x-app-layout title="Akun Petugas" subtitle="Atur akun Admin dan Kasir yang bertugas jaga di toko." scene="users">
    <x-slot:actions>
        <a href="{{ route('users.create') }}"
           class="btn-tactile inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover sm:w-auto">
            + Tambah Petugas
        </a>
    </x-slot:actions>

    <!-- Mobile Cards -->
    <div data-enter="1" class="space-y-4 md:hidden">
        @foreach ($users as $user)
            <div class="card-interactive rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="text-base font-semibold text-ink">
                            {{ $user->name }}
                            @if ($user->id === auth()->id())
                                <span class="ml-1 rounded bg-canvas px-1.5 py-0.5 text-[11px] font-medium text-ink-muted">Akunmu</span>
                            @endif
                        </div>
                        <div class="mt-0.5 truncate font-mono text-xs text-ink-muted">{{ $user->email }}</div>
                    </div>
                    <span class="shrink-0 rounded-md border px-2 py-0.5 text-[11px] font-semibold uppercase {{ $user->isAdmin() ? 'border-brand/25 bg-brand-soft text-brand' : 'border-border bg-canvas text-ink' }}">
                        {{ $user->roleLabel() }}
                    </span>
                </div>

                <div class="mt-3.5 flex items-center justify-between rounded-xl bg-canvas px-3.5 py-2.5 text-xs">
                    <span class="text-ink-muted">Mencatat <strong class="font-mono text-ink">{{ $user->transaksis_count }} nota</strong></span>
                    @if ($user->is_active)
                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-medium text-emerald-800">Aktif</span>
                    @else
                        <span class="rounded-full border border-stone-200 bg-stone-100 px-2.5 py-0.5 text-[11px] font-medium text-stone-700">Nonaktif</span>
                    @endif
                </div>

                <div class="mt-4 flex gap-3 border-t border-border pt-4">
                    <a href="{{ route('users.edit', $user) }}"
                       class="btn-tactile flex-1 rounded-xl border border-border bg-surface px-4 py-2.5 text-center text-xs font-semibold text-ink hover:bg-surface-muted">
                        Ubah Data
                    </a>
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.toggle', $user) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn-tactile w-full rounded-xl border border-border bg-canvas px-4 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop Table -->
    <div data-enter="1" class="hidden min-w-0 overflow-hidden rounded-2xl border border-border bg-surface md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-canvas text-xs font-semibold text-ink-muted">
                    <tr>
                        <th class="px-5 py-3.5">Nama Petugas</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">Peran</th>
                        <th class="px-5 py-3.5 text-center">Nota Dicatat</th>
                        <th class="px-5 py-3.5">Status Akun</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($users as $user)
                        <tr class="transition-colors hover:bg-canvas/50">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-ink">
                                {{ $user->name }}
                                @if ($user->id === auth()->id())
                                    <span class="ml-1.5 rounded bg-canvas px-1.5 py-0.5 text-[11px] font-medium text-ink-muted">Akunmu</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-ink-muted">{{ $user->email }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="rounded-md border px-2.5 py-0.5 text-xs font-semibold uppercase {{ $user->isAdmin() ? 'border-brand/25 bg-brand-soft text-brand' : 'border-border bg-canvas text-ink' }}">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-mono tabular-nums">{{ $user->transaksis_count }}</td>
                            <td class="px-5 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full border border-stone-200 bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2.5">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="btn-tactile rounded-lg border border-border bg-surface px-3.5 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                        Ubah
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn-tactile rounded-lg border border-border bg-canvas px-3.5 py-1.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
