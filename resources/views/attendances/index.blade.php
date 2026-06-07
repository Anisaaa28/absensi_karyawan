@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-end">
            <div>
                <p class="section-title">Attendance Log & History</p>
                <h1 class="page-title">Riwayat absensi dengan filter tanggal, status, dan drill-down detail.</h1>
                
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Records</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $attendances->total() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Page</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $attendances->count() }}</p>
                </div>
                <a href="{{ route('checkin.index') }}" class="btn-primary justify-center">Open Kiosk</a>
            </div>
        </div>

        <form method="GET" action="{{ route('attendances.index') }}" class="mt-8 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <input type="text" value="{{ request('search') }}" name="search" placeholder="Cari nama atau NIK..." class="w-full" />
            <input type="date" value="{{ request('date') }}" name="date" class="w-full" />
            <select name="status" class="w-full">
                <option value="">Semua Status</option>
                @foreach(['Present', 'Late', 'Early Out', 'On Leave', 'Alfa'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Terapkan Filter</button>
        </form>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Present</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $attendances->where('status', 'Present')->count() }}</p>
            <p class="mt-2 text-sm text-emerald-300">Dalam halaman ini</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Late</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $attendances->where('status', 'Late')->count() }}</p>
            <p class="mt-2 text-sm text-amber-300">Perlu evaluasi</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Alfa</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $attendances->where('status', 'Alfa')->count() }}</p>
            <p class="mt-2 text-sm text-rose-300">Tanpa kehadiran</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">With GPS</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $attendances->filter(fn ($item) => $item->latitude && $item->longitude)->count() }}</p>
            <p class="mt-2 text-sm text-sky-300">Lokasi terekam</p>
        </article>
    </section>

    <section class="card-premium overflow-hidden" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Attendance Table</p>
                <h2 class="page-title text-2xl md:text-3xl">Riwayat absensi per karyawan</h2>
            </div>
        </div>

        <div class="premium-scrollbar overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama & NIK</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>GPS</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body divide-y divide-slate-800">
                    @forelse($attendances as $attendance)
                        @php
                            $duration = ($attendance->clock_in && $attendance->clock_out)
                                ? $attendance->clock_in->diff($attendance->clock_out)->format('%hh %im')
                                : '-';
                        @endphp
                        <tr>
                            <td class="text-slate-300">{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>
                                <div>
                                    <p class="font-semibold text-white">{{ $attendance->employee->name ?? 'Employee' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $attendance->employee->nik ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="text-slate-300">{{ optional($attendance->clock_in)->format('H:i') ?? '-' }}</td>
                            <td class="text-slate-300">{{ optional($attendance->clock_out)->format('H:i') ?? '-' }}</td>
                            <td class="text-slate-300">{{ $duration }}</td>
                            <td>
                                @if($attendance->status === 'Present')
                                    <span class="badge-present">Present</span>
                                @elseif($attendance->status === 'Late')
                                    <span class="badge-late">Late</span>
                                @elseif($attendance->status === 'Alfa')
                                    <span class="badge-alfa">Alfa</span>
                                @elseif($attendance->status === 'Early Out')
                                    <span class="badge-pending">Early Out</span>
                                @else
                                    <span class="badge-onleave">On Leave</span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-400">
                                {{ $attendance->latitude ? $attendance->latitude . ', ' . $attendance->longitude : 'No signal' }}
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('attendances.show', $attendance) }}" class="btn-pill">Detail</a>
                                    <form action="{{ route('attendances.destroy', $attendance) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary px-4 py-2 text-sm" data-confirm="Hapus data absensi ini?" data-confirm-title="Hapus data absensi">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">Belum ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($attendances->hasPages())
        <div class="flex items-center justify-center">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
