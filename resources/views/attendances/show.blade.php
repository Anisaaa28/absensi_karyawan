@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="glass-panel rounded-[32px] p-8 shadow-2xl ring-1 ring-white/10 animate-fade-up">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-white">Detail Absensi</h1>
                <p class="mt-2 text-slate-400">Semua informasi absensi karyawan tercatat di sini.</p>
            </div>
            <a href="{{ route('attendances.index') }}" class="btn-secondary">Kembali</a>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            <div class="rounded-[28px] bg-slate-950/85 p-6 shadow-xl ring-1 ring-white/10">
                <p class="text-sm uppercase tracking-[0.24em] text-sky-300">Tanggal</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $attendance->attendance_date->format('Y-m-d') }}</p>
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-sm text-slate-400">Karyawan</p>
                        <p class="mt-2 text-lg font-medium text-slate-100">{{ $attendance->employee->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Status</p>
                        <p class="mt-2 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-sm font-semibold text-emerald-200">{{ $attendance->status }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-[28px] bg-slate-950/85 p-6 shadow-xl ring-1 ring-white/10">
                <div class="grid gap-5">
                    <div>
                        <p class="text-sm text-slate-400">Clock In</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ optional($attendance->clock_in)->format('H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Clock Out</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ optional($attendance->clock_out)->format('H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Lokasi</p>
                        <p class="mt-2 text-sm text-slate-200">{{ $attendance->latitude ? $attendance->latitude . ', ' . $attendance->longitude : 'Tidak ada data GPS' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
