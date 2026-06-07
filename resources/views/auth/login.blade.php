@extends('layouts.app')

@section('content')
<div x-data="{ shown: false, loading: false, showPassword: false }" x-init="setTimeout(() => shown = true, 100)" class="relative min-h-[calc(100svh-40px)] overflow-hidden">

    {{-- ═══ MINI NAVBAR ═══ --}}
    <div class="absolute inset-x-0 top-0 z-50">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8">
            <a href="/" class="flex items-center gap-3 group">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-[10px] font-bold text-white shadow-lg shadow-blue-500/20">HR</div>
                <span class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">Absensi Karyawan</span>
            </a>
            <a href="/" class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Background Orbs --}}
    <div class="absolute top-[20%] left-[5%] h-[450px] w-[450px] rounded-full bg-blue-600/[0.05] blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[15%] right-[10%] h-[350px] w-[350px] rounded-full bg-teal-500/[0.04] blur-[100px] pointer-events-none"></div>

    {{-- ═══ SPLIT LAYOUT ═══ --}}
    <div class="relative flex min-h-screen items-center justify-center px-4 py-20 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl">
            <div class="grid items-center gap-12 lg:grid-cols-[1fr_1.1fr] lg:gap-20">

                {{-- ═══ LEFT: Welcome Panel ═══ --}}
                <div class="hidden lg:block">
                    <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-100" x-transition:enter-start="opacity-0 -translate-x-6" x-transition:enter-end="opacity-100 translate-x-0">
                        <p class="section-title">Selamat Datang</p>
                        <h1 class="mt-3 text-3xl font-bold tracking-tight text-white xl:text-4xl">
                            Masuk ke Sistem<br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Absensi & HR</span>
                        </h1>
                        <p class="mt-5 max-w-sm text-sm leading-relaxed text-slate-400">
                            Kelola kehadiran tim, tracking GPS, shift kerja, dan payroll dalam satu dashboard yang powerful.
                        </p>

                        {{-- Trust Indicators --}}
                        <div class="mt-10 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">Enkripsi End-to-End</p>
                                    <p class="text-xs text-slate-500">Data terproteksi standar enterprise</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">Verifikasi GPS</p>
                                    <p class="text-xs text-slate-500">Lokasi divalidasi saat check-in</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">Response Instan</p>
                                    <p class="text-xs text-slate-500">Rata-rata &lt;2 detik waktu respons</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ RIGHT: Login Form Card ═══ --}}
                <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-200" x-transition:enter-start="opacity-0 translate-y-6 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    <div class="login-form-card rounded-2xl p-7 sm:p-9">
                        {{-- Header --}}
                        <div class="text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <h2 class="mt-5 text-xl font-semibold text-white">Masuk ke Dashboard</h2>
                            <p class="mt-2 text-sm text-slate-400">Gunakan Email atau NIK untuk login</p>
                        </div>

                        {{-- Form --}}
                        <form action="{{ route('login.submit') }}" method="POST" class="mt-8 space-y-5" @submit="loading = true">
                            @csrf

                            {{-- Identifier --}}
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-2">Email atau NIK</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="identifier" value="{{ old('identifier') }}" required
                                        class="login-input pl-10" placeholder="nama@email.com atau NIK001" />
                                </div>
                            </div>

                            {{-- Password --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-slate-400">Password</label>
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                        </svg>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required
                                        class="login-input pl-10 pr-11" placeholder="••••••••" />
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-slate-300 transition-colors">
                                        <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember --}}
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember"
                                    class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/30 focus:ring-offset-0 cursor-pointer">
                                <label for="remember" class="ml-2.5 text-sm text-slate-400 cursor-pointer select-none">Ingat sesi saya</label>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn-primary w-full !py-3.5 relative group overflow-hidden"
                                :disabled="loading" :class="{ 'opacity-75 cursor-not-allowed': loading }">
                                <span x-show="!loading" class="relative z-10 flex items-center justify-center gap-2">
                                    Masuk Sekarang
                                    <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </span>
                                <span x-show="loading" x-cloak class="relative z-10 flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memverifikasi...
                                </span>
                            </button>
                        </form>

                        {{-- Footer --}}
                        <div class="mt-7 border-t border-white/5 pt-6 text-center">
                            <p class="text-sm text-slate-400">
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">Daftar sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
