@extends('layouts.app')

@section('content')
@php
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    $selectedDays = old('days', $shift->days ?? []);
@endphp
<div class="mx-auto grid max-w-3xl gap-8">
    <div class="glass-panel rounded-[32px] p-8 shadow-2xl ring-1 ring-white/10 animate-fade-up">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-title">Shift Configuration</p>
                <h1 class="text-3xl font-semibold text-white">Edit Shift</h1>
                <p class="mt-2 text-slate-400">Perbarui jadwal shift, hari aktif, atau lokasi penugasan.</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="glass-panel rounded-[32px] p-8 shadow-2xl ring-1 ring-white/10 animate-fade-up">
        <form action="{{ route('shifts.update', $shift) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-300">Nama Shift</label>
                    <input type="text" name="name" value="{{ old('name', $shift->name) }}" required class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $shift->location) }}" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-300">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time', \Illuminate\Support\Str::limit($shift->start_time, 5, '')) }}" required class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time', \Illuminate\Support\Str::limit($shift->end_time, 5, '')) }}" required class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Warna Label</label>
                    <input type="color" name="color" value="{{ old('color', $shift->color ?: '#10b981') }}" class="mt-3 h-[50px] w-full rounded-[24px] border border-slate-700 bg-slate-900 px-2 shadow-lg focus:border-sky-400" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Hari Aktif</label>
                <p class="mt-1 text-xs text-slate-500">Centang hari di mana shift ini berlaku. Kosongkan jika setiap hari.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-7">
                    @foreach($days as $day)
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/60 px-3 py-3 text-sm text-slate-200 transition hover:border-sky-400 has-[:checked]:border-sky-400 has-[:checked]:bg-sky-500/10">
                            <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays)) class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-sky-500 focus:ring-sky-400" />
                            <span>{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan Perubahan</button>
                <a href="{{ route('shifts.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
