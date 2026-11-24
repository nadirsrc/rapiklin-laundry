<x-guest-layout title="Masuk Petugas">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid items-stretch gap-6 lg:grid-cols-12 lg:gap-8">
            <!-- Left Panel: Workshop Scene & Rates -->
            <div data-enter="1" class="flex flex-col justify-between rounded-2xl border border-border bg-surface p-6 sm:p-9 lg:col-span-7">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-2 rounded-full border border-border bg-canvas px-3 py-1 text-xs font-medium text-ink-muted">
                            <span class="h-2 w-2 rounded-full bg-brand"></span>
                            RapiKlin Laundry · Meja Kasir &amp; Operasional
                        </div>
                        <div class="flex items-center gap-2 text-xs text-ink-muted">
                            <x-status-badge status="masuk" />
                            <span class="h-1 w-1 rounded-full bg-border"></span>
                            <x-status-badge status="proses" />
                            <span class="h-1 w-1 rounded-full bg-border"></span>
                            <x-status-badge status="selesai" />
                        </div>
                    </div>

                    <h1 class="mt-5 text-2xl font-semibold tracking-tight text-ink sm:text-3xl">
                        Urusan cucian harian, beres di satu layar.
                    </h1>
                    <p class="mt-2.5 max-w-xl text-sm leading-relaxed text-ink-muted">
                        Catat cucian pelanggan yang baru datang, hitung tarif kiloan otomatis, pantau cucian yang lagi digarap sampai siap diambil, dan cek pemasukan toko kapan pun dibutuhkan.
                    </p>

                    <!-- Animated Laundry Workshop Scene SVG -->
                    <div class="mt-6">
                        <x-laundry-scene variant="login" />
                    </div>

                    <div class="mt-6">
                        <div class="mb-2.5 flex items-center justify-between">
                            <h2 class="text-xs font-semibold tracking-wider text-ink-muted uppercase">Harga Paket Cuci Saat Ini</h2>
                            <span class="text-xs text-ink-muted">Hitungan per kg</span>
                        </div>

                        <!-- Mobile Rate Cards (No horizontal scroll) -->
                        <div class="space-y-3 sm:hidden">
                            @forelse ($layanans as $layanan)
                                <div class="flex items-center justify-between rounded-xl border border-border bg-canvas p-4">
                                    <div>
                                        <div class="font-semibold text-ink">{{ $layanan->nama_layanan }}</div>
                                        <div class="mt-0.5 text-xs text-ink-muted">Durasi: {{ $layanan->estimasi_durasi }}</div>
                                    </div>
                                    <div class="font-mono text-sm font-semibold text-brand tabular-nums">
                                        Rp {{ number_format($layanan->harga_per_kg, 0, ',', '.') }}/kg
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-border bg-canvas p-4 text-center text-sm text-ink-muted">
                                    Belum ada paket cuci yang aktif.
                                </div>
                            @endforelse
                        </div>

                        <!-- Desktop Rate Table -->
                        <div class="hidden overflow-hidden rounded-xl border border-border sm:block">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-canvas text-xs font-semibold text-ink-muted">
                                    <tr>
                                        <th class="px-4 py-2.5">Paket Cuci</th>
                                        <th class="px-4 py-2.5">Perkiraan Selesai</th>
                                        <th class="px-4 py-2.5 text-right">Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border bg-surface">
                                    @forelse ($layanans as $layanan)
                                        <tr class="transition-colors hover:bg-canvas/50">
                                            <td class="px-4 py-3 font-medium text-ink">{{ $layanan->nama_layanan }}</td>
                                            <td class="px-4 py-3 text-ink-muted">{{ $layanan->estimasi_durasi }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-semibold text-ink tabular-nums">
                                                Rp {{ number_format($layanan->harga_per_kg, 0, ',', '.') }}/kg
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-center text-ink-muted">Belum ada paket cuci yang aktif.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-7 flex flex-col gap-3.5 border-t border-border pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold text-ink">Mau ambil cucian?</div>
                        <div class="text-xs text-ink-muted">Cek apakah cucianmu sudah selesai pakai nomor nota atau nomor HP.</div>
                    </div>
                    <a href="{{ route('cek-cucian') }}"
                       class="btn-tactile inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-canvas px-4 py-2.5 text-xs font-semibold text-ink hover:bg-surface-muted">
                        Cek Status Cucian
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Right Panel: Login Form -->
            <div data-enter="2" class="flex flex-col justify-between rounded-2xl border border-border bg-surface p-6 sm:p-9 lg:col-span-5">
                <div>
                    <div class="group flex items-center gap-3.5">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand text-white">
                            <x-drum-icon spin="slow" class="h-6 w-6" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-ink">Masuk Petugas</h2>
                            <p class="text-xs text-ink-muted">Pakai akun Admin atau Kasir yang terdaftar</p>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs text-emerald-900">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs text-rose-900">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-xs font-semibold text-ink">Alamat Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                   placeholder="admin@rapiklin.test"
                                   class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-muted/60 focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-ink">Kata Sandi</label>
                            <input id="password" name="password" type="password" required
                                   placeholder="Ketik kata sandi"
                                   class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-muted/60 focus:border-brand focus:ring-2 focus:ring-brand/15 focus:outline-none">
                        </div>

                        <div class="flex items-center justify-between pt-0.5">
                            <label class="inline-flex items-center gap-2 text-xs text-ink-muted">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-border text-brand focus:ring-brand">
                                Tetap masuk di perangkat ini
                            </label>
                        </div>

                        <button type="submit"
                                class="btn-tactile w-full rounded-xl bg-brand px-4 py-3 text-sm font-semibold text-white hover:bg-brand-hover focus:ring-2 focus:ring-brand/25 focus:outline-none">
                            Masuk ke Ruang Kerja
                        </button>
                    </form>
                </div>

                <!-- Demo Credential Quick Fill -->
                <div class="mt-8 rounded-xl border border-border bg-canvas p-4">
                    <div class="text-xs font-semibold text-ink">Mau langsung tes role?</div>
                    <p class="mt-0.5 text-[11px] text-ink-muted">Pilih salah satu akun contoh di bawah buat isi otomatis:</p>
                    <div class="mt-3.5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <button type="button" onclick="fillDemoLogin('admin@rapiklin.test', 'password')"
                                class="btn-tactile rounded-xl border border-border bg-surface px-3.5 py-2.5 text-left hover:border-brand">
                            <div class="text-xs font-semibold text-brand">Akun Admin</div>
                            <div class="truncate font-mono text-[11px] text-ink-muted">admin@rapiklin.test</div>
                        </button>
                        <button type="button" onclick="fillDemoLogin('kasir@rapiklin.test', 'password')"
                                class="btn-tactile rounded-xl border border-border bg-surface px-3.5 py-2.5 text-left hover:border-brand">
                            <div class="text-xs font-semibold text-ink">Akun Kasir</div>
                            <div class="truncate font-mono text-[11px] text-ink-muted">kasir@rapiklin.test</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillDemoLogin(email, password) {
            const emailEl = document.getElementById('email');
            const passEl = document.getElementById('password');
            emailEl.value = email;
            passEl.value = password;
            emailEl.classList.add('ring-2', 'ring-brand/20');
            setTimeout(() => emailEl.classList.remove('ring-2', 'ring-brand/20'), 280);
        }
    </script>
</x-guest-layout>
