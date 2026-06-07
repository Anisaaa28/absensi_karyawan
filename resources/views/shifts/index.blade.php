@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    $monthName = $startOfMonth->translatedFormat('F');
    $prevMonth = $startOfMonth->copy()->subMonth();
    $nextMonth = $startOfMonth->copy()->addMonth();
    $today = now()->toDateString();
    $filterQuery = $employeeFilter ? ['employee' => $employeeFilter] : [];
@endphp
<div class="space-y-6" x-data="{ showAssignModal: false, assignDate: @js(today()->toDateString()), assignEmployee: '' }">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr] xl:items-end">
            <div>
                <p class="section-title">Shift & Interactive Calendar</p>
                <h1 class="page-title">Jadwal shift karyawan berdasarkan kalender dengan assignment harian.</h1>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Shift</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $shifts->count() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Assigned Bulan Ini</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $assignments->flatten()->count() }}</p>
                </div>
                <a href="{{ route('shifts.create') }}" class="btn-primary justify-center">+ Shift Baru</a>
            </div>
        </div>
    </section>

    <section class="card-premium" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Calendar Surface</p>
                <h2 class="page-title text-2xl md:text-3xl">Kalender Assignment Shift</h2>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('shifts.index', array_merge(['month' => $prevMonth->month, 'year' => $prevMonth->year], $filterQuery)) }}" class="btn-pill !px-3" title="Bulan sebelumnya">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="rounded-2xl border border-sky-500/30 bg-sky-500/10 px-5 py-2 text-center">
                    <p class="text-[10px] uppercase tracking-[0.18em] text-sky-300">{{ $monthName }}</p>
                    <p class="text-xl font-bold text-white tabular-nums">{{ $year }}</p>
                </div>
                <a href="{{ route('shifts.index', array_merge(['month' => $nextMonth->month, 'year' => $nextMonth->year], $filterQuery)) }}" class="btn-pill !px-3" title="Bulan berikutnya">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('shifts.index') }}" class="btn-secondary !py-2 !px-4 !text-xs">Hari Ini</a>

                <form method="GET" action="{{ route('shifts.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <select name="employee" onchange="this.form.submit()" class="rounded-xl border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-200">
                        <option value="">Semua Karyawan</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected($employeeFilter == $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </form>

                <button type="button" @click="showAssignModal = true; assignDate = @js(today()->toDateString()); assignEmployee = ''" class="btn-primary !py-2 !px-4 !text-xs">+ Assignment</button>
            </div>
        </div>

        <div class="premium-scrollbar overflow-x-auto p-6">
            <div class="min-w-[860px]">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-300">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-500/40 bg-amber-500/10 px-3 py-1 text-amber-200">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sudah Clock Out
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-emerald-200">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Clock In
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-rose-500/40 bg-rose-500/10 px-3 py-1 text-rose-200">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.732-3l-7-12a2 2 0 00-3.464 0l-7 12A2 2 0 005 19z"/></svg>
                            Belum scan
                        </span>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-7 gap-3">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-3 py-3 text-center text-sm font-semibold text-slate-300">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-3">
                    @php $cursor = $startGrid->copy(); @endphp
                    @while($cursor->lte($endGrid))
                        @php
                            $dateKey = $cursor->toDateString();
                            $dayAssignments = $assignments->get($dateKey, collect());
                            $isCurrentMonth = $cursor->month === $month;
                            $isToday = $dateKey === $today;
                        @endphp
                        <div class="min-h-32 rounded-[22px] border {{ $isCurrentMonth ? 'border-white/10 bg-slate-950/45' : 'border-white/5 bg-slate-950/20' }} p-3 {{ $isToday ? 'ring-2 ring-sky-500/60' : '' }}">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold {{ $isCurrentMonth ? 'text-white' : 'text-slate-600' }}">{{ $cursor->day }}</p>
                                @if($isCurrentMonth)
                                    <button type="button" @click="showAssignModal = true; assignDate = @js($dateKey); assignEmployee = ''" class="rounded-md p-1 text-slate-500 hover:bg-white/5 hover:text-sky-300" title="Tambah assignment di tanggal ini">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    </button>
                                @endif
                            </div>
                            <div class="mt-3 space-y-1.5">
                                @forelse($dayAssignments as $assignment)
                                    @php
                                        $key = $assignment->employee_id . '|' . $dateKey;
                                        $att = $attendanceMap[$key] ?? null;
                                        $hasIn = $att && ! empty($att['clock_in']);
                                        $hasOut = $att && ! empty($att['clock_out']);
                                        if ($hasOut) {
                                            $chipBg = 'rgba(245, 158, 11, 0.18)';
                                            $chipBorder = 'rgba(245, 158, 11, 0.55)';
                                            $chipText = 'text-amber-100';
                                            $state = 'out';
                                        } elseif ($hasIn) {
                                            $chipBg = 'rgba(16, 185, 129, 0.18)';
                                            $chipBorder = 'rgba(16, 185, 129, 0.55)';
                                            $chipText = 'text-emerald-100';
                                            $state = 'in';
                                        } else {
                                            $chipBg = 'rgba(244, 63, 94, 0.18)';
                                            $chipBorder = 'rgba(244, 63, 94, 0.55)';
                                            $chipText = 'text-rose-100';
                                            $state = 'none';
                                        }
                                        $tipText = $assignment->employee->name . ' - ' . $assignment->shift->name
                                            . ($hasIn ? ' • In ' . \Illuminate\Support\Carbon::parse($att['clock_in'])->format('H:i') : '')
                                            . ($hasOut ? ' • Out ' . \Illuminate\Support\Carbon::parse($att['clock_out'])->format('H:i') : '');
                                    @endphp
                                    <div class="group flex flex-col gap-1 rounded-lg px-2 py-1.5 text-[11px] font-medium {{ $chipText }}" style="background: {{ $chipBg }}; border: 1px solid {{ $chipBorder }};" title="{{ $tipText }}">
                                        <div class="flex items-center justify-between gap-1">
                                            <div class="flex min-w-0 flex-1 items-center gap-1.5">
                                                @if($state === 'out')
                                                    <span class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-amber-500/30 text-amber-200">
                                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                    </span>
                                                @elseif($state === 'in')
                                                    <span class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500/30 text-emerald-200">
                                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-rose-500/30 text-rose-200">
                                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.732-3l-7-12a2 2 0 00-3.464 0l-7 12A2 2 0 005 19z"/></svg>
                                                    </span>
                                                @endif
                                                <p class="truncate text-[11px] font-semibold">{{ $assignment->employee->name }}</p>
                                            </div>
                                            <form action="{{ route('shifts.assign.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Hapus assignment {{ $assignment->employee->name }} untuk tanggal ini?')" class="opacity-0 transition group-hover:opacity-100">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-300 hover:text-white" title="Hapus">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                        <p class="truncate text-[10px] opacity-80">{{ $assignment->shift->name }}</p>
                                        <div class="flex items-center gap-2 text-[10px] tabular-nums opacity-90">
                                            <span class="inline-flex items-center gap-1">
                                                <span class="opacity-60">In</span>
                                                <span class="font-semibold">{{ $hasIn ? \Illuminate\Support\Carbon::parse($att['clock_in'])->format('H:i') : '—' }}</span>
                                            </span>
                                            <span class="opacity-40">·</span>
                                            <span class="inline-flex items-center gap-1">
                                                <span class="opacity-60">Out</span>
                                                <span class="font-semibold">{{ $hasOut ? \Illuminate\Support\Carbon::parse($att['clock_out'])->format('H:i') : '—' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    @if($isCurrentMonth)
                                        <p class="text-[10px] italic text-slate-600">— belum ada —</p>
                                    @endif
                                @endforelse
                            </div>
                        </div>
                        @php $cursor->addDay(); @endphp
                    @endwhile
                </div>
            </div>
        </div>
    </section>

    <section class="card-premium overflow-hidden" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Shift List</p>
                <h2 class="page-title text-2xl md:text-3xl">Roster shift aktif</h2>
            </div>
            <p class="text-sm text-slate-400">Daftar shift yang digunakan untuk assignment kalender di atas.</p>
        </div>

        <div class="premium-scrollbar overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th>Shift</th>
                        <th>Jam</th>
                        <th>Hari Aktif</th>
                        <th>Lokasi</th>
                        <th>Assigned</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body divide-y divide-slate-800">
                    @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 rounded-full" style="background: {{ $shift->color ?: '#38bdf8' }}"></span>
                                    <p class="font-semibold text-white">{{ $shift->name }}</p>
                                </div>
                            </td>
                            <td class="text-slate-300">{{ \Illuminate\Support\Str::limit($shift->start_time, 5, '') }} - {{ \Illuminate\Support\Str::limit($shift->end_time, 5, '') }}</td>
                            <td class="text-slate-300">{{ $shift->days ? implode(', ', $shift->days) : '-' }}</td>
                            <td class="text-slate-300">{{ $shift->location ?: '-' }}</td>
                            <td class="text-slate-300">{{ $shift->employees->pluck('name')->take(3)->implode(', ') ?: 'Belum ada assignment' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('shifts.edit', $shift) }}" class="btn-pill">Edit</a>
                                    <form action="{{ route('shifts.destroy', $shift) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary px-4 py-2 text-sm" data-confirm="Hapus shift ini?" data-confirm-title="Hapus shift">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-lg font-semibold text-white">Belum ada shift terdaftar</p>
                                <p class="mt-2 text-sm text-slate-400">Buat shift pagi, siang, atau malam untuk memulai.</p>
                                <a href="{{ route('shifts.create') }}" class="btn-primary mt-4 inline-flex">Tambah Shift Pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal Tambah Assignment --}}
    <div x-show="showAssignModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" style="display: none;" @keydown.escape.window="showAssignModal = false">
        <div @click.away="showAssignModal = false" x-show="showAssignModal" x-transition.opacity.duration.300ms class="w-full max-w-md rounded-3xl border border-slate-700/50 bg-slate-900/95 p-7 shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="section-title">Quick Assignment</p>
                    <h3 class="mt-1 text-xl font-semibold text-white">Assign shift ke karyawan</h3>
                </div>
                <button @click="showAssignModal = false" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('shifts.assign.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-slate-400">Karyawan</label>
                    <select name="employee_id" x-model="assignEmployee" required class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-sky-400">
                        <option value="">— Pilih karyawan —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} — {{ $emp->nik }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-slate-400">Shift</label>
                    <select name="shift_id" required class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-sky-400">
                        <option value="">— Pilih shift —</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ \Illuminate\Support\Str::limit($shift->start_time, 5, '') }}–{{ \Illuminate\Support\Str::limit($shift->end_time, 5, '') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-slate-400">Tanggal</label>
                    <input type="date" name="work_date" x-model="assignDate" required class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-sky-400" />
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-slate-400">Catatan (opsional)</label>
                    <input type="text" name="notes" placeholder="Mis. Tukar shift dengan B" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-sky-400" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1 justify-center">Simpan Assignment</button>
                    <button type="button" @click="showAssignModal = false" class="btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
