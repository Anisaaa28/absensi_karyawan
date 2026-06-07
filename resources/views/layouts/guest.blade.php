<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: true }" :class="darkMode ? 'dark' : ''">
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
</head>
<body class="min-h-screen gesture-surface">
    <div class="noise-overlay"></div>

    {{-- Toast Notifications --}}
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

    {{-- No sidebar, no topbar — just the content --}}
    <main>
        @if(session('status'))
            <div class="fixed top-5 left-1/2 -translate-x-1/2 z-50 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-6 py-3 text-sm text-emerald-200 backdrop-blur-sm">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="fixed top-5 left-1/2 -translate-x-1/2 z-50 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-6 py-3 text-sm text-rose-200 backdrop-blur-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
