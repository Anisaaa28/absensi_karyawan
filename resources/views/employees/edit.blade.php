@extends('layouts.app')

@section('content')
<div class="mx-auto grid max-w-3xl gap-8">
    <div class="glass-panel rounded-[32px] p-8 shadow-2xl ring-1 ring-white/10 animate-fade-up">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-white">Edit Karyawan</h1>
                <p class="mt-2 text-slate-400">Perbarui informasi karyawan dengan tampilan yang lebih rapi.</p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn-secondary">Kembali ke Daftar</a>
        </div>
    </div>
    <div class="glass-panel rounded-[32px] p-8 shadow-2xl ring-1 ring-white/10 animate-fade-up">
        <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-300">NIK</label>
                <input type="text" value="{{ $employee->nik }}" disabled class="mt-3 w-full rounded-[24px] border border-slate-700 bg-slate-900 px-4 py-3 text-slate-300 shadow-lg" />
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-300">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Tipe</label>
                    <select name="type" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400">
                        <option value="Security" {{ $employee->type === 'Security' ? 'selected' : '' }}>Security</option>
                        <option value="Cleaning Service" {{ $employee->type === 'Cleaning Service' ? 'selected' : '' }}>Cleaning Service</option>
                        <option value="Helper" {{ $employee->type === 'Helper' ? 'selected' : '' }}>Helper</option>
                    </select>
                </div>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-300">No. Telp</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Tanggal Bergabung</label>
                    <input type="date" name="joined_at" value="{{ old('joined_at', $employee->joined_at?->format('Y-m-d')) }}" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Alamat</label>
                <textarea name="address" rows="4" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400">{{ old('address', $employee->address) }}</textarea>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-300">Gaji Pokok</label>
                    <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $employee->base_salary) }}" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300">Status</label>
                    <select name="status" class="mt-3 w-full rounded-[24px] border border-slate-700 px-4 py-3 text-slate-100 shadow-lg focus:border-sky-400">
                        <option value="Active" {{ $employee->status === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $employee->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full">Perbarui Karyawan</button>
        </form>
    </div>
</div>
@endsection
