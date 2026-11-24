@props(['title' => 'Dashboard', 'subtitle' => null, 'scene' => 'dashboard'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'RapiKlin Laundry') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media (prefers-reduced-motion: no-preference) {
            @view-transition {
                navigation: auto;
            }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-canvas text-ink">
<div class="min-h-screen overflow-x-hidden lg:flex print:block print:min-h-0 print:overflow-visible">
    <!-- Desktop Sidebar -->
    <aside class="no-print vt-sidebar hidden lg:fixed lg:inset-y-0 lg:left-0 lg:flex lg:w-64 lg:flex-col lg:border-r lg:border-border lg:bg-surface">
        @include('components.sidebar-nav', ['isMobile' => false])
    </aside>

    <!-- Mobile Drawer Backdrop -->
    <div id="mobile-sidebar-backdrop" class="no-print fixed inset-0 z-40 hidden bg-ink/40 opacity-0 backdrop-blur-[1px] transition-opacity duration-200 lg:hidden"></div>

    <!-- Mobile Sidebar Drawer -->
    <aside id="mobile-sidebar" class="no-print fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full overflow-y-auto border-r border-border bg-surface transition-transform duration-200 ease-out lg:hidden">
        @include('components.sidebar-nav', ['isMobile' => true])
    </aside>

    <!-- Main Workspace -->
    <div class="flex min-w-0 flex-1 flex-col lg:pl-64 print:block print-reset">
        <!-- Mobile Header -->
        <header class="no-print sticky top-0 z-30 flex items-center justify-between border-b border-border bg-surface/95 px-4 py-3.5 backdrop-blur-sm lg:hidden">
            <button id="mobile-menu-open" type="button" class="btn-tactile inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-3 py-2 text-sm font-medium text-ink hover:bg-surface-muted">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Menu
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-sm font-semibold text-ink">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand text-white">
                    <x-drum-icon spin="slow" class="h-4 w-4" />
                </span>
                <span>RapiKlin</span>
            </a>
            <span class="rounded-md border border-border bg-surface-muted px-2.5 py-1 text-[11px] font-semibold uppercase">
                {{ auth()->user()->roleLabel() }}
            </span>
        </header>

        <main class="vt-main mx-auto w-full min-w-0 max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8 print-reset">
            <!-- Page Heading Banner with Animated Laundry SVG -->
            <div data-enter class="no-print mb-6 rounded-2xl border border-border bg-surface p-5 sm:p-6">
                <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        @if ($scene)
                            <div class="hidden shrink-0 rounded-xl border border-border/80 bg-canvas px-2 py-1 sm:block">
                                <x-laundry-scene :variant="$scene" />
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3 sm:hidden">
                                @if ($scene)
                                    <div class="rounded-xl border border-border/80 bg-canvas px-1.5 py-0.5">
                                        <x-laundry-scene :variant="$scene" class="h-12 w-20" />
                                    </div>
                                @endif
                                <h1 class="text-xl font-semibold tracking-tight text-ink">{{ $title }}</h1>
                            </div>
                            <h1 class="hidden text-xl font-semibold tracking-tight text-ink sm:block sm:text-2xl">{{ $title }}</h1>
                            @if ($subtitle)
                                <p class="mt-1.5 max-w-xl text-sm leading-relaxed text-ink-muted">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    @isset($actions)
                        <div class="flex shrink-0 flex-wrap items-center gap-2.5 border-t border-border pt-4 sm:flex-nowrap md:border-t-0 md:pt-0">
                            {{ $actions }}
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="no-print mb-5 flex items-start justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="no-print mb-5 flex items-start justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="no-print mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900">
                    <div class="font-semibold">Ada beberapa isian yang perlu dicek lagi:</div>
                    <ul class="mt-1.5 list-inside list-disc space-y-1 text-rose-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
