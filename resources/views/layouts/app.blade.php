<!DOCTYPE html>
<html lang="id" x-data="appShell()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Absensi & HR Enterprise — Kelola absensi, shift, payroll, dan cuti karyawan secara efisien.">
    <title>{{ config('app.name', 'Absensi Karyawan') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js" defer></script>
    <script type="module">
        import * as Turbo from 'https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-esm.js';
        Turbo.setProgressBarDelay(50);
    </script>
    <style>
        .turbo-progress-bar {
            height: 3px;
            background: linear-gradient(to right, #3b82f6, #14b8a6);
        }
    </style>
</head>
<body class="min-h-screen gesture-surface" @touchstart.passive="onTouchStart($event)" @touchend.passive="onTouchEnd($event)">
    <div class="noise-overlay"></div>

    {{-- ═══ Toast Notifications ═══ --}}
    <div class="fixed right-5 top-5 z-[70] space-y-3 pointer-events-none">
        <template x-for="notif in window.AlpineUtilities?.notifications || []" :key="notif.id">
            <div x-show="notif.visible" x-transition.opacity.duration.300ms
                class="pointer-events-auto w-96 max-w-[calc(100vw-2rem)] rounded-2xl border p-4 shadow-2xl backdrop-blur-xl"
                :class="{
                    'bg-emerald-500/10 border-emerald-500/20 text-emerald-100': notif.type === 'success',
                    'bg-rose-500/10 border-rose-500/20 text-rose-100': notif.type === 'error',
                    'bg-amber-500/10 border-amber-500/20 text-amber-100': notif.type === 'warning',
                    'bg-sky-500/10 border-sky-500/20 text-sky-100': notif.type === 'info',
                }">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-sm"
                        x-text="notif.type === 'success' ? '✓' : notif.type === 'error' ? '✕' : notif.type === 'warning' ? '!' : 'i'"></span>
                    <p class="flex-1 pt-1 text-sm leading-6" x-text="notif.message"></p>
                </div>
            </div>
        </template>
    </div>

    @php
        $navigation = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'description' => 'KPI & live activity', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>'],
            ['label' => 'Scan QR', 'route' => 'checkin.index', 'description' => 'Kiosk check-in', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>'],
            ['label' => 'Karyawan', 'route' => 'employees.index', 'description' => 'Master employee', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'],
            ['label' => 'Absensi', 'route' => 'attendances.index', 'description' => 'Log & riwayat', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
            ['label' => 'Shift', 'route' => 'shifts.index', 'description' => 'Jadwal & kalender', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>'],
            ['label' => 'Cuti', 'route' => 'leaves.index', 'description' => 'Approval workflow', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>'],
            ['label' => 'Lembur', 'route' => 'overtimes.index', 'description' => 'Request overtime', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
            ['label' => 'Payroll', 'route' => 'payrolls.index', 'description' => 'Payslip & draft', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>'],
            ['label' => 'Tracking', 'route' => 'location-tracking.index', 'description' => 'GPS & peta', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>'],
            ['label' => 'Aktivitas', 'route' => 'activity-logs.index', 'description' => 'Audit trail', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>'],
        ];
    @endphp

    <div class="app-shell lg:flex">
        {{-- ═══ SIDEBAR (Desktop) ═══ --}}
        @if(auth()->check())
            <aside class="app-sidebar fixed inset-y-0 left-0 z-50 hidden w-[272px] lg:flex lg:flex-col">
                {{-- Brand --}}
                <div class="px-5 pt-6 pb-2">
                    <div class="flex items-center gap-3 px-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-sm font-bold text-white shadow-lg shadow-blue-500/20">
                            HR
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white">Absensi Karyawan</p>
                            <p class="text-[11px] text-slate-400">Enterprise Platform</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="premium-scrollbar mt-4 flex-1 space-y-1 overflow-y-auto px-4">
                    <p class="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Menu Utama</p>
                    @foreach($navigation as $item)
                        <a href="{{ route($item['route']) }}"
                            class="nav-link group flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs($item['route']) || (str_contains($item['route'], '.') && request()->routeIs(str_replace('.index', '.*', $item['route']))) ? 'nav-link-active' : 'hover:bg-white/[0.04]' }}">
                            <div class="nav-icon-wrap">
                                <svg class="h-[18px] w-[18px] text-slate-300 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $item['icon'] !!}
                                </svg>
                            </div>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-medium text-slate-200 group-hover:text-white">{{ $item['label'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </nav>

                {{-- User Panel --}}
                <div class="border-t border-white/5 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-xs font-semibold text-slate-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-[11px] text-slate-400">{{ auth()->user()->role }}</p>
                        </div>
                        <a href="{{ route('logout') }}" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-white/5 hover:text-white" title="Logout">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </aside>
        @endif

        {{-- ═══ MAIN CONTENT AREA ═══ --}}
        <div class="min-w-0 flex-1 {{ auth()->check() ? 'lg:pl-[272px]' : '' }}">
            {{-- ═══ TOPBAR (Authenticated) ═══ --}}
            @if(auth()->check())
                <div class="sticky top-0 z-40 px-4 pt-3 sm:px-6 lg:px-8">
                    <div class="topbar-shell rounded-2xl px-4 py-3 sm:px-5">
                        <div class="flex items-center justify-between gap-3">
                            {{-- Left: Mobile toggle + Breadcrumb --}}
                            <div class="flex items-center gap-3">
                                <button @click="mobileOpen = true" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white transition-colors lg:hidden">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                                    </svg>
                                </button>
                                <div class="hidden sm:flex items-center gap-2">
                                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.4)]"></span>
                                    <span class="text-xs text-slate-400">Sistem aktif</span>
                                </div>
                            </div>

                            {{-- Right: Clock, Theme, Actions --}}
                            <div class="flex items-center gap-2">
                                <div class="hidden md:flex items-center gap-2 rounded-lg bg-white/[0.03] px-3 py-1.5 text-xs text-slate-300 border border-white/5">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium tabular-nums" x-text="clock"></span>
                                </div>
                                <button @click="toggleTheme()" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white transition-colors" title="Toggle theme">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('logout') }}" class="btn-secondary !py-2 !px-4 !text-xs !rounded-lg sm:hidden">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══ MOBILE SIDEBAR SHEET ═══ --}}
            @if(auth()->check())
                <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileOpen = false"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    </div>
                    <aside class="app-sidebar mobile-sheet absolute inset-y-0 left-0 w-[85%] max-w-[300px] px-5 py-6"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-xs font-bold text-white">HR</div>
                                <p class="text-sm font-semibold text-white">Absensi Karyawan</p>
                            </div>
                            <button @click="mobileOpen = false" class="rounded-lg p-2 text-slate-400 hover:text-white transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <nav class="mt-6 space-y-1">
                            @foreach($navigation as $item)
                                <a href="{{ route($item['route']) }}" @click="mobileOpen = false"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-colors {{ request()->routeIs($item['route']) || (str_contains($item['route'], '.') && request()->routeIs(str_replace('.index', '.*', $item['route']))) ? 'nav-link-active text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </nav>
                    </aside>
                </div>
            @endif

            {{-- ═══ MAIN ═══ --}}
            <main class="px-4 pb-8 pt-5 sm:px-6 lg:px-8 lg:pb-10 lg:pt-6">
                @if(session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-200 backdrop-blur-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-200 backdrop-blur-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- ═══ Confirm Dialog ═══ --}}
    <dialog id="nativeConfirmDialog" class="native-dialog">
        <form method="dialog" class="p-6">
            <p class="text-[10px] uppercase tracking-[0.14em] text-slate-400">Konfirmasi</p>
            <h2 id="nativeConfirmTitle" class="mt-2 text-lg font-semibold text-white">Tindakan</h2>
            <p id="nativeConfirmMessage" class="mt-2 text-sm text-slate-300">Apakah Anda yakin?</p>
            <div class="mt-5 flex gap-3 justify-end">
                <button value="cancel" class="btn-secondary !py-2 !px-4 !text-sm">Batal</button>
                <button value="confirm" class="btn-primary !py-2 !px-4 !text-sm">Lanjutkan</button>
            </div>
        </form>
    </dialog>

    <script>
        function appShell() {
            return {
                darkMode: localStorage.getItem('theme') !== 'light',
                mobileOpen: false,
                clock: '',
                orientation: 'portrait',
                touchStartX: 0,
                notificationPermission: window.Notification ? Notification.permission : 'unsupported',
                init() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    this.updateOrientation();
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    window.addEventListener('resize', () => this.updateOrientation());
                    this.$watch('darkMode', (value) => {
                        document.documentElement.classList.toggle('dark', value);
                        localStorage.setItem('theme', value ? 'dark' : 'light');
                    });
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                },
                updateClock() {
                    this.clock = new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false,
                        timeZone: 'Asia/Jakarta',
                    });
                },
                updateOrientation() {
                    this.orientation = window.matchMedia('(orientation: landscape)').matches ? 'landscape' : 'portrait';
                    document.documentElement.dataset.orientation = this.orientation;
                },
                onTouchStart(event) {
                    this.touchStartX = event.changedTouches?.[0]?.screenX || 0;
                },
                onTouchEnd(event) {
                    const endX = event.changedTouches?.[0]?.screenX || 0;
                    const delta = endX - this.touchStartX;
                    if (!this.mobileOpen && this.touchStartX < 28 && delta > 72) {
                        this.mobileOpen = true;
                    }
                    if (this.mobileOpen && delta < -72) {
                        this.mobileOpen = false;
                    }
                },
                async enableSystemNotifications() {
                    if (!window.Notification) return;
                    const permission = await Notification.requestPermission();
                    this.notificationPermission = permission;
                    if (permission === 'granted') {
                        new Notification('Absensi Karyawan', {
                            body: 'Notifikasi native berhasil diaktifkan.',
                        });
                    }
                }
            }
        }
    </script>
</body>
</html>
