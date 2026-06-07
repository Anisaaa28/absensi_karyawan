@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ query: @js(request('search', '')), selectedType: @js(request('type', '')), selectedStatus: @js(request('status', '')), showQrModal: false, qrUrl: '', qrName: '' }">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-end">
            <div>
                <p class="section-title">Employee Management</p>
                <h1 class="page-title">Master data karyawan dengan panel roster, status, dan tindakan cepat.</h1>
                
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Total Data</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $employees->total() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Per Page</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $employees->count() }}</p>
                </div>
                <a href="{{ route('employees.create') }}" class="btn-primary justify-center">Tambah Karyawan</a>
            </div>
        </div>

        <form method="GET" action="{{ route('employees.index') }}" class="mt-8 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <div class="xl:col-span-2">
                <input x-model="query" type="text" name="search" placeholder="Cari cepat nama, NIK, atau nomor telepon..." class="w-full" />
            </div>
            <div>
                <select name="type" class="w-full" x-model="selectedType" @change="$el.closest('form').submit()">
                    <option value="">Semua Tipe</option>
                    <option value="Security">Security</option>
                    <option value="Cleaning Service">Cleaning Service</option>
                    <option value="Helper">Helper</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full" x-model="selectedStatus" @change="$el.closest('form').submit()">
                    <option value="">Semua Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="xl:col-span-4 flex justify-end">
                <button type="submit" class="btn-secondary">Terapkan Filter</button>
            </div>
        </form>
    </section>

    <section class="grid gap-6 2xl:grid-cols-[1.35fr_0.65fr]">
        <article class="card-premium overflow-hidden" data-reveal>
            <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="section-title">Employee Roster</p>
                    <h2 class="page-title text-2xl md:text-3xl">Tabel operasional karyawan</h2>
                </div>
                
            </div>

            <div class="premium-scrollbar overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th>Nama & NIK</th>
                            <th>Tipe</th>
                            <th>Kontak</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-body divide-y divide-slate-800">
                        @forelse($employees as $employee)
                            <tr x-show="
                                ('{{ strtolower($employee->name . ' ' . $employee->nik . ' ' . ($employee->phone ?? '')) }}'.includes(query.toLowerCase()))
                            ">
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-gradient-to-br from-sky-400/30 via-indigo-500/30 to-violet-500/30 font-semibold text-white">
                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $employee->name }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $employee->nik }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs font-medium text-slate-200">{{ $employee->type }}</span>
                                </td>
                                <td class="text-slate-300">{{ $employee->phone ?? '-' }}</td>
                                <td class="text-slate-300">{{ optional($employee->joined_at)->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if($employee->status === 'Active')
                                        <span class="badge-present">Active</span>
                                    @else
                                        <span class="badge-alfa">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="showQrModal = true; qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $employee->nik }}'; qrName = '{{ $employee->name }} - {{ $employee->nik }}'" class="btn-pill !border-sky-500/30 !text-sky-300 hover:!bg-sky-500/10">Lihat QR</button>
                                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ $employee->nik }}&download=1" download="QR-{{ $employee->nik }}.png" class="btn-pill !border-emerald-500/30 !text-emerald-300 hover:!bg-emerald-500/10">Download QR</a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn-pill">Edit</a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary px-4 py-2 text-sm" data-confirm="Hapus karyawan ini?" data-confirm-title="Hapus data karyawan">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="empty-state">
                                        <p class="text-lg font-semibold text-white">Belum ada karyawan</p>
                                        <p class="mt-2 text-sm text-slate-400">Mulai dari menambahkan roster pertama.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="space-y-6">
            

            <article class="card-premium" data-reveal>
                <p class="section-title">Quick Actions</p>
                <div class="mt-6 grid gap-3">
                    <a href="{{ route('employees.create') }}" class="btn-primary justify-center">Tambah Data</a>
                    <a href="{{ route('attendances.index') }}" class="btn-secondary justify-center">Lihat Absensi</a>
                    <a href="{{ route('shifts.index') }}" class="btn-secondary justify-center">Atur Shift</a>
                </div>
            </article>
        </aside>
    </section>

    @if($employees->hasPages())
        <div class="flex items-center justify-center">
            {{ $employees->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- QR Code Modal -->
    <div x-show="showQrModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" style="display: none;">
        <div @click.away="showQrModal = false" x-show="showQrModal" x-transition.opacity.duration.300ms class="w-full max-w-sm rounded-3xl border border-slate-700/50 bg-slate-900/95 p-8 shadow-2xl text-center">
            <h3 class="text-xl font-bold text-white mb-1">ID Card QR Code</h3>
            <p class="text-sm text-slate-400 mb-6" x-text="qrName"></p>
            
            <div class="rounded-2xl bg-white p-4 mx-auto inline-block">
                <img :src="qrUrl" alt="QR Code" class="h-48 w-48 object-contain" />
            </div>

            <p class="mt-6 text-xs text-slate-500 leading-relaxed">
                Karyawan tidak memerlukan akun. Cukup cetak QR ini (berisi teks NIK) dan tempel pada ID Card mereka untuk di-scan di halaman Check In.
            </p>

            <button @click="showQrModal = false" class="btn-secondary w-full justify-center mt-6">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection
