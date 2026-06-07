@extends('layouts.app')

@section('content')
<div class="grid min-h-[calc(100svh-56px)] place-items-center py-10 sm:py-12 lg:min-h-[calc(100svh-72px)]">
    <div class="relative w-full max-w-4xl overflow-hidden rounded-[28px] border border-white/10 bg-white/5 p-5 shadow-2xl backdrop-blur-xl sm:rounded-[40px] sm:p-8">
        <div class="absolute inset-0 opacity-40">
            <div class="hero-graphic">
                <svg viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="180" cy="170" r="120" fill="url(#paint0)" />
                    <circle cx="620" cy="120" r="100" fill="url(#paint1)" />
                    <circle cx="520" cy="450" r="160" fill="url(#paint2)" />
                    <defs>
                        <radialGradient id="paint0" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(180 170) rotate(90) scale(120)"><stop stop-color="#38bdf8" stop-opacity=".45"/><stop offset="1" stop-color="#38bdf8" stop-opacity="0"/></radialGradient>
                        <radialGradient id="paint1" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(620 120) rotate(90) scale(100)"><stop stop-color="#6366f1" stop-opacity=".35"/><stop offset="1" stop-color="#6366f1" stop-opacity="0"/></radialGradient>
                        <radialGradient id="paint2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(520 450) rotate(90) scale(160)"><stop stop-color="#10b981" stop-opacity=".28"/><stop offset="1" stop-color="#10b981" stop-opacity="0"/></radialGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <div class="relative grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:gap-10">
            <div class="space-y-6">
                <div class="rounded-[24px] border border-white/10 bg-slate-950/80 p-5 shadow-xl sm:rounded-[32px] sm:p-6">
                    <p class="text-sm uppercase tracking-[0.24em] text-sky-300">Daftar Sekarang</p>
                    <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">Buat akun HR dan akses absensi</h1>
                    <p class="mt-4 max-w-xl text-slate-300">Lengkapi data untuk mengelola karyawan, shift, cuti, lembur, dan payroll dalam satu platform.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-950/80 p-5 text-white shadow-lg sm:rounded-[28px]">
                        <div class="text-sm text-slate-400">Akses Penuh</div>
                        <div class="mt-3 text-2xl font-semibold"><span class="text-sky-400">HR</span> Dashboard</div>
                    </div>
                    <div class="rounded-[22px] bg-slate-950/80 p-5 text-white shadow-lg sm:rounded-[28px]">
                        <div class="text-sm text-slate-400">Security</div>
                        <div class="mt-3 text-2xl font-semibold">Login Aman</div>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] bg-slate-950/90 p-6 shadow-2xl ring-1 ring-white/10 sm:rounded-[32px] sm:p-8">
                <div class="mb-8 text-center">
                    <div class="inline-flex rounded-full bg-slate-800/90 p-4 text-sky-300 shadow-lg">🔐</div>
                    <h2 class="mt-6 text-2xl font-semibold text-white">Registrasi Akun</h2>
                    <p class="mt-2 text-sm text-slate-400">Masuk dan mulai mengelola absensi perusahaan.</p>
                </div>
                <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-[24px] px-4 py-3 shadow-lg" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-[24px] px-4 py-3 shadow-lg" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300">NIK (opsional)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" class="mt-2 w-full rounded-[24px] px-4 py-3 shadow-lg" />
                    </div>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-300">Password</label>
                            <input type="password" name="password" required class="mt-2 w-full rounded-[24px] px-4 py-3 shadow-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="mt-2 w-full rounded-[24px] px-4 py-3 shadow-lg" />
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full">Daftar Sekarang</button>
                </form>
                <p class="mt-6 text-center text-sm text-slate-400">Sudah punya akun? <a href="{{ route('login') }}" class="text-sky-300 hover:text-sky-200">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
