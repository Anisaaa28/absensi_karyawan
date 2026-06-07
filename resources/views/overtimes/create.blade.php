@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="section-title">Overtime Assignment</p>
                <h1 class="page-title">Assign lembur untuk karyawan tertentu.</h1>
                <p class="mt-2 text-sm text-slate-400">Pilih karyawan, tentukan tanggal dan jam, lalu request akan masuk ke kolom pending untuk di-approve.</p>
            </div>
            <a href="{{ route('overtimes.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </section>

    <section class="card-premium p-8" data-reveal>
        <form action="{{ route('overtimes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Karyawan</label>
                <select name="employee_id" required class="w-full">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                            {{ $employee->name }} — {{ $employee->nik }} ({{ $employee->type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Tanggal Lembur</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full" />
                </div>
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time', '17:00') }}" required class="w-full" />
                </div>
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time', '20:00') }}" required class="w-full" />
                </div>
            </div>

            <div class="rounded-[20px] border border-sky-500/20 bg-sky-500/5 p-4 text-sm text-sky-100">
                <p class="font-medium">Catatan:</p>
                <ul class="mt-2 list-disc list-inside space-y-1 text-xs">
                    <li>Durasi dihitung otomatis dari selisih jam (format 24 jam).</li>
                    <li>Shift malam (jam selesai &lt; jam mulai) tetap dihitung benar.</li>
                    <li>Rate lembur diestimasi dari gaji pokok ÷ 173 jam × 1.5 (bisa diubah setelah request dibuat).</li>
                </ul>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Alasan Lembur</label>
                <textarea name="reason" rows="3" placeholder="Misal: Penyelesaian laporan bulanan, event khusus, dll." class="w-full resize-none">{{ old('reason') }}</textarea>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Catatan Tambahan (opsional)</label>
                <textarea name="comments" rows="2" placeholder="Info tambahan seperti instruksi kerja, supervisor, dll." class="w-full resize-none">{{ old('comments') }}</textarea>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <button type="submit" class="btn-primary w-full justify-center">Simpan Request</button>
                <a href="{{ route('overtimes.index') }}" class="btn-secondary w-full justify-center">Batal</a>
            </div>
        </form>
    </section>
</div>
@endsection
