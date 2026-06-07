@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="section-title">Leave Request Form</p>
                <h1 class="page-title">Form cuti dengan proof upload dan alasan terstruktur.</h1>
                
            </div>
            <a href="{{ route('leaves.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </section>

    <section class="card-premium p-8" data-reveal>
        <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Karyawan</label>
                <select name="employee_id" required class="w-full">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach(\App\Models\Employee::orderBy('name')->get() as $employee)
                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }} ({{ $employee->nik }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Tipe</label>
                    <select name="type" required class="w-full">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Sakit" @selected(old('type') === 'Sakit')>Sakit</option>
                        <option value="Ijin" @selected(old('type') === 'Ijin')>Ijin</option>
                        <option value="Alfa" @selected(old('type') === 'Alfa')>Alfa</option>
                    </select>
                </div>
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full" />
                </div>
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-300">Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full" />
                </div>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Alasan / Keterangan</label>
                <textarea name="reason" rows="4" placeholder="Tulis alasan pengajuan..." class="w-full resize-none">{{ old('reason') }}</textarea>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Bukti Pendukung</label>
                <input type="file" name="evidence" class="w-full" />
                <p class="mt-2 text-xs text-slate-400">Format: JPG, PNG, PDF. Maksimal 2 MB.</p>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-slate-300">Komentar Tambahan</label>
                <textarea name="comments" rows="3" placeholder="Opsional" class="w-full resize-none">{{ old('comments') }}</textarea>
            </div>

            <div class="rounded-[24px] border border-sky-400/15 bg-sky-500/8 p-4 text-sm leading-6 text-sky-100">
                Workflow approval: request masuk ke kolom pending, reviewer dapat approve/reject langsung dari board, dan bukti tetap terikat di record.
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <button type="submit" class="btn-primary w-full justify-center">Ajukan Cuti</button>
                <a href="{{ route('leaves.index') }}" class="btn-secondary w-full justify-center">Batal</a>
            </div>
        </form>
    </section>
</div>
@endsection
