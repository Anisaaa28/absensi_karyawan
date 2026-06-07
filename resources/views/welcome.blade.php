@extends('layouts.guest')

@section('content')
<div x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)" class="relative overflow-hidden">

    {{-- ═══ NAVBAR ═══ --}}
    <nav class="landing-nav fixed inset-x-0 top-0 z-50">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="/" class="flex items-center gap-3 group">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-xs font-bold text-white shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/30 transition-shadow">
                    HR
                </div>
                <span class="text-sm font-semibold text-white">Absensi Karyawan</span>
            </a>
            <a href="{{ route('login') }}" class="btn-secondary !py-2.5 !px-5 !text-sm gap-2 group">
                Masuk
                <svg class="h-4 w-4 text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </nav>

    {{-- ═══ HERO SECTION ═══ --}}
    <section class="relative min-h-screen flex items-center pt-20 pb-16">
        {{-- Background Orbs --}}
        <div class="absolute top-[15%] left-[8%] h-[500px] w-[500px] rounded-full bg-blue-600/[0.06] blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[5%] h-[400px] w-[400px] rounded-full bg-teal-500/[0.05] blur-[100px] pointer-events-none"></div>
        <div class="absolute top-[60%] left-[50%] h-[300px] w-[300px] rounded-full bg-indigo-500/[0.04] blur-[80px] pointer-events-none"></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid items-center gap-16 lg:grid-cols-[1.1fr_0.9fr]">
                {{-- Left: Text --}}
                <div>
                    {{-- Badge --}}
                    <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-100" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/[0.07] px-4 py-1.5 text-xs font-medium text-blue-300">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        </span>
                        Enterprise HR Platform
                    </div>

                    {{-- Heading --}}
                    <h1 x-show="shown" x-transition:enter="transition ease-out duration-700 delay-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-6 text-4xl font-bold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.5rem]">
                        Kelola Kehadiran Tim<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-teal-400">Lebih Cerdas & Efisien</span>
                    </h1>

                    {{-- Subtitle --}}
                    <p x-show="shown" x-transition:enter="transition ease-out duration-700 delay-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-6 max-w-lg text-base leading-relaxed text-slate-400 sm:text-lg">
                        Platform absensi terintegrasi dengan pelacakan GPS realtime, manajemen shift, payroll otomatis, dan workflow approval — dirancang untuk tim enterprise Indonesia.
                    </p>

                    {{-- CTA Buttons --}}
                    <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[400ms]" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-10 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('login') }}" class="btn-primary px-7 py-3.5 text-sm gap-2 group">
                            Mulai Sekarang
                            <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                        <a href="#features" class="btn-secondary px-7 py-3.5 text-sm">Lihat Fitur</a>
                    </div>

                    {{-- Stats --}}
                    <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-12 flex gap-8 border-t border-white/5 pt-8">
                        <div>
                            <p class="text-2xl font-bold text-white">500+</p>
                            <p class="mt-1 text-xs text-slate-400">Karyawan Terdaftar</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">99.9%</p>
                            <p class="mt-1 text-xs text-slate-400">Uptime SLA</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">&lt;2s</p>
                            <p class="mt-1 text-xs text-slate-400">Response Time</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Abstract Illustration --}}
                <div x-show="shown" x-transition:enter="transition ease-out duration-1000 delay-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative hidden lg:block">
                    <div class="relative mx-auto w-full max-w-md">
                        {{-- Decorative Grid --}}
                        <svg class="absolute inset-0 h-full w-full opacity-[0.04]" viewBox="0 0 400 400">
                            <defs>
                                <pattern id="heroGrid" width="32" height="32" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" fill="currentColor" class="text-slate-300"/>
                                </pattern>
                            </defs>
                            <rect width="400" height="400" fill="url(#heroGrid)"/>
                        </svg>

                        {{-- Floating Cards --}}
                        <div class="relative space-y-4">
                            {{-- Card 1: Clock In --}}
                            <div class="ml-8 rounded-2xl border border-white/[0.06] bg-slate-900/60 p-5 backdrop-blur-sm shadow-xl" style="animation: floaty 6s ease-in-out infinite;">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Clock In Berhasil</p>
                                        <p class="text-xs text-slate-400">08:02 WIB — GPS Verified</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: Stat --}}
                            <div class="mr-8 ml-auto max-w-[220px] rounded-2xl border border-white/[0.06] bg-slate-900/60 p-5 backdrop-blur-sm shadow-xl" style="animation: floaty 6s ease-in-out 1s infinite;">
                                <p class="text-[10px] uppercase tracking-[0.1em] text-slate-400">Hadir Hari Ini</p>
                                <p class="mt-2 text-3xl font-bold text-white">247</p>
                                <div class="mt-3 h-1.5 rounded-full bg-slate-800">
                                    <div class="h-full w-[87%] rounded-full bg-gradient-to-r from-blue-500 to-cyan-400"></div>
                                </div>
                                <p class="mt-2 text-xs text-emerald-300">↑ 87% kehadiran</p>
                            </div>

                            {{-- Card 3: Leave --}}
                            <div class="ml-12 max-w-[260px] rounded-2xl border border-white/[0.06] bg-slate-900/60 p-5 backdrop-blur-sm shadow-xl" style="animation: floaty 6s ease-in-out 2s infinite;">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-white">Cuti Diajukan</p>
                                            <p class="text-xs text-slate-400">3 pending review</p>
                                        </div>
                                    </div>
                                    <span class="badge-pending !text-[10px]">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ FEATURES SECTION ═══ --}}
    <section id="features" class="relative py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-title">Fitur Unggulan</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Semua yang Tim HR Anda Butuhkan</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-400">Solusi absensi lengkap yang mengintegrasikan kehadiran, tracking lokasi, dan payroll dalam satu dashboard interaktif.</p>
            </div>

            {{-- Feature Cards --}}
            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                x-data="{ visible: false }" x-intersect:enter="visible = true">
                {{-- Card 1 --}}
                <div class="feature-card glow-border"
                    x-show="visible" x-transition:enter="transition ease-out duration-600 delay-100" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="feature-icon flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-white">Absensi Realtime</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Rekam kedatangan dan kepulangan karyawan secara instan melalui QR Scan, input manual, atau deteksi otomatis.</p>
                </div>

                {{-- Card 2 --}}
                <div class="feature-card glow-border"
                    x-show="visible" x-transition:enter="transition ease-out duration-600 delay-200" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="feature-icon flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-white">Tracking GPS Presisi</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Validasi koordinat lokasi karyawan secara presisi dengan radius geofencing dan peta interaktif real-time.</p>
                </div>

                {{-- Card 3 --}}
                <div class="feature-card glow-border"
                    x-show="visible" x-transition:enter="transition ease-out duration-600 delay-300" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="feature-icon flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-white">Payroll & Cuti Otomatis</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Kelola pengajuan cuti, jadwal shift, lembur, dan otomasi slip gaji di satu platform dengan workflow approval.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="border-t border-white/5 py-8">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Absensi Karyawan. All rights reserved.</p>
                <p class="text-xs text-slate-500">Powered by Laravel & Supabase</p>
            </div>
        </div>
    </footer>
</div>
@endsection
