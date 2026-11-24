@php
    $user = auth()->user();
    $navItems = [
        [
            'group' => 'Meja Kasir',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'active' => request()->routeIs('dashboard'),
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
                [
                    'label' => 'Transaksi Laundry',
                    'route' => 'transaksi.index',
                    'active' => request()->routeIs('transaksi.*'),
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                ],
                [
                    'label' => 'Data Pelanggan',
                    'route' => 'pelanggan.index',
                    'active' => request()->routeIs('pelanggan.*'),
                    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                ],
            ],
        ],
        [
            'group' => 'Paket & Keuangan',
            'items' => [
                [
                    'label' => 'Data Layanan',
                    'route' => 'layanan.index',
                    'active' => request()->routeIs('layanan.*'),
                    'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                ],
                [
                    'label' => 'Rekap Transaksi',
                    'route' => 'laporan.index',
                    'active' => request()->routeIs('laporan.*'),
                    'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                ],
            ],
        ],
    ];

    if ($user && $user->isAdmin()) {
        $navItems[] = [
            'group' => 'Pengaturan Toko',
            'items' => [
                [
                    'label' => 'Akun Petugas',
                    'route' => 'users.index',
                    'active' => request()->routeIs('users.*'),
                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                ],
            ],
        ];
    }
@endphp

<div class="flex h-full flex-col justify-between">
    <div>
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand text-white transition-transform duration-150 group-hover:scale-[1.04]">
                    <x-drum-icon spin="hover" class="h-5 w-5" />
                </div>
                <div>
                    <div class="text-sm font-semibold tracking-tight text-ink">RapiKlin</div>
                    <div class="text-[11px] font-medium text-ink-muted">Kasir &amp; Laundry</div>
                </div>
            </a>
            @if ($isMobile ?? false)
                <button id="mobile-menu-close" type="button"
                        class="rounded-xl p-2 text-ink-muted hover:bg-surface-muted hover:text-ink"
                        aria-label="Tutup menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        <div class="px-4 py-4">
            <a href="{{ route('transaksi.create') }}"
               class="btn-tactile flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Terima Cucian Baru
            </a>
        </div>

        <nav class="space-y-5 px-3.5 pb-4">
            @foreach ($navItems as $section)
                <div>
                    <div class="mb-2 px-2.5 text-[11px] font-semibold tracking-wider text-ink-muted uppercase">
                        {{ $section['group'] }}
                    </div>
                    <div class="space-y-1.5">
                        @foreach ($section['items'] as $item)
                            <a href="{{ route($item['route']) }}"
                               class="nav-link-slide flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm {{ $item['active'] ? 'bg-brand-soft font-semibold text-brand' : 'font-medium text-ink-muted hover:bg-surface-muted hover:text-ink' }}">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                <div class="mb-2 px-2.5 text-[11px] font-semibold tracking-wider text-ink-muted uppercase">
                    Halaman Pelanggan
                </div>
                <a href="{{ route('cek-cucian') }}" target="_blank"
                   class="nav-link-slide flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-muted hover:bg-surface-muted hover:text-ink">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Cek Status Cucian</span>
                </a>
            </div>
        </nav>
    </div>

    @if ($user)
        <div class="border-t border-border p-4">
            <div class="mb-3.5 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-ink">{{ $user->name }}</div>
                    <div class="truncate text-xs text-ink-muted">{{ $user->email }}</div>
                </div>
                <span class="shrink-0 rounded-md border px-2 py-0.5 text-[11px] font-semibold uppercase {{ $user->isAdmin() ? 'border-brand/25 bg-brand-soft text-brand' : 'border-border bg-surface-muted text-ink' }}">
                    {{ $user->roleLabel() }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="btn-tactile flex w-full items-center justify-center gap-2 rounded-xl border border-border bg-surface px-3 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Sesi
                </button>
            </form>
        </div>
    @endif
</div>
