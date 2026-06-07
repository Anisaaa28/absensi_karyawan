@extends('layouts.app')

@section('content')
<div
    x-data="{
        trend: @js($attendanceTrend),
        maxTrend: Math.max(...@js($attendanceTrend->pluck('count')->values()), 1),
        animatedPresent: 0,
        animatedLate: 0,
        animatedLeave: 0,
        animatedPending: 0,
        countersStarted: false,
        animateCounter(target, key, duration = 1200) {
            let start = 0;
            const step = target / (duration / 16);
            const tick = () => {
                start += step;
                if (start >= target) { this[key] = target; return; }
                this[key] = Math.floor(start);
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        },
        startCounters() {
            if (this.countersStarted) return;
            this.countersStarted = true;
            this.animateCounter({{ $attendancesToday }}, 'animatedPresent');
            this.animateCounter({{ $lateCount }}, 'animatedLate');
            this.animateCounter({{ $onLeaveToday }}, 'animatedLeave');
            this.animateCounter({{ $pendingRequests }}, 'animatedPending');
        }
    }"
    x-intersect:enter.once="startCounters()"
    class="space-y-6"
>
    {{-- ═══ WELCOME HERO ═══ --}}
    <section class="glass-panel relative overflow-hidden px-6 py-6 sm:px-8 sm:py-8">
        {{-- Decorative --}}
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-blue-500/[0.06] blur-[80px] pointer-events-none"></div>
        <div class="absolute -left-8 bottom-0 h-40 w-40 rounded-full bg-teal-500/[0.04] blur-[60px] pointer-events-none"></div>

        <div class="relative">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="section-title">Dashboard</p>
                    <h1 class="page-title">
                        Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 17 ? 'siang' : 'malam') }}, {{ auth()->user()->name }}
                    </h1>
                    
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('checkin.index') }}" class="quick-action rounded-xl border border-white/5 bg-white/[0.03] px-4 py-2.5">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z"/>
                        </svg>
                        <span class="text-sm font-medium">Buka Kiosk QR</span>
                    </a>
                    <a href="{{ route('leaves.index') }}" class="quick-action rounded-xl border border-white/5 bg-white/[0.03] px-4 py-2.5">
                        <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Review Approval</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ KPI STAT CARDS ═══ --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Present --}}
        <article class="kpi-card group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-[0.1em] text-slate-400">Hadir Hari Ini</p>
                    <p class="kpi-number mt-3" x-text="animatedPresent">0</p>
                    <p class="mt-2 text-xs text-emerald-400">{{ $attendanceRate }}% dari {{ $totalEmployees }} karyawan</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-slate-800/60">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-1000" style="width: {{ min($attendanceRate, 100) }}%"></div>
            </div>
        </article>

        {{-- Late --}}
        <article class="kpi-card group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-[0.1em] text-slate-400">Terlambat</p>
                    <p class="kpi-number mt-3" x-text="animatedLate">0</p>
                    <p class="mt-2 text-xs text-amber-400">Perlu follow-up</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 group-hover:scale-110 transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <span class="badge-late">Late</span>
                <span class="badge-present">Target SLA</span>
            </div>
        </article>

        {{-- On Leave --}}
        <article class="kpi-card group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-[0.1em] text-slate-400">Sedang Cuti</p>
                    <p class="kpi-number mt-3" x-text="animatedLeave">0</p>
                    <p class="mt-2 text-xs text-sky-400">Approved leave hari ini</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400 group-hover:scale-110 transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <span class="badge-onleave">Sakit / Ijin</span>
            </div>
        </article>

        {{-- Need Attention --}}
        <article class="kpi-card group">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-[0.1em] text-slate-400">Perlu Perhatian</p>
                    <p class="kpi-number mt-3" x-text="animatedPending">0</p>
                    <p class="mt-2 text-xs text-rose-400">{{ $alfaCount }} alfa &middot; {{ $overtimePending }} lembur pending</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-500/10 text-rose-400 group-hover:scale-110 transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <span class="badge-pending">Approval</span>
                <span class="badge-alfa">Alfa</span>
            </div>
        </article>
    </section>

    {{-- ═══ OVERVIEW PANELS ═══ --}}
    <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        {{-- Quick Summary --}}
        <div class="card-premium space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="section-title">Ringkasan Cepat</p>
                    <h2 class="page-title !text-xl">Status operasional hari ini</h2>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="panel-outline rounded-xl p-4">
                    <p class="text-[10px] font-medium uppercase tracking-[0.1em] text-slate-400">Cuti Pending</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ $leavePending }}</p>
                </div>
                <div class="panel-outline rounded-xl p-4">
                    <p class="text-[10px] font-medium uppercase tracking-[0.1em] text-slate-400">Jam Lembur</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($overtimeHours, 1) }}</p>
                </div>
                <div class="panel-outline rounded-xl p-4">
                    <p class="text-[10px] font-medium uppercase tracking-[0.1em] text-slate-400">Payroll Draft</p>
                    <p class="mt-2 text-lg font-bold text-white">Rp {{ number_format($payrollEstimate, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Live Clock & Quick Links --}}
        <div class="card-premium">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="section-title">Jam Sistem</p>
                    <p class="mt-2 text-4xl font-bold tabular-nums text-white" x-text="$root.clock"></p>
                    <p class="mt-1 text-sm text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/10 to-violet-500/10 text-blue-300 ring-1 ring-white/5">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="mt-6 space-y-2">
                <a href="{{ route('location-tracking.index') }}" class="quick-action">
                    <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <span>Pantau GPS Karyawan</span>
                    <svg class="ml-auto h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
                <a href="{{ route('payrolls.index') }}" class="quick-action">
                    <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                    </svg>
                    <span>Buka Payroll Draft</span>
                    <svg class="ml-auto h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══ ATTENDANCE CHART ═══ --}}
    <section class="card-premium">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="section-title">Trend Kehadiran</p>
                <h2 class="page-title !text-xl">7 hari terakhir</h2>
            </div>
            <a href="{{ route('attendances.index') }}" class="btn-pill gap-2">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Lihat Semua Log
            </a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            {{-- Chart --}}
            <div class="rounded-2xl border border-white/5 bg-slate-950/40 p-5">
                <div class="flex h-64 items-end gap-2.5">
                    <template x-for="(item, idx) in trend" :key="item.date">
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="flex h-full w-full items-end">
                                <div class="w-full rounded-t-xl bg-gradient-to-t from-blue-600 via-blue-500 to-cyan-400 transition-all duration-700 ease-out"
                                    :style="`height:${Math.max((item.count / maxTrend) * 100, 8)}%; transition-delay: ${idx * 100}ms`"></div>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-semibold tabular-nums text-white" x-text="item.count"></p>
                                <p class="mt-0.5 text-[10px] text-slate-500" x-text="item.date"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Workforce Breakdown --}}
            <div class="space-y-3">
                @forelse($employeesByType as $type)
                    <div class="panel-outline rounded-xl p-4 hover:border-blue-500/10 transition-colors">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $type->type }}</p>
                                <p class="mt-0.5 text-[10px] uppercase tracking-[0.1em] text-slate-500">Active Workforce</p>
                            </div>
                            <p class="text-2xl font-bold tabular-nums text-white">{{ $type->total }}</p>
                        </div>
                        <div class="mt-3 h-1 overflow-hidden rounded-full bg-slate-800/50">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-400" style="width: {{ $totalEmployees > 0 ? round(($type->total / $totalEmployees) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state text-sm text-slate-400">Belum ada komposisi divisi.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ═══ LIVE TABLE + ACTIVITY LOG ═══ --}}
    <section class="grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
        {{-- Live Attendance Table --}}
        <article class="card-premium">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="section-title">Kehadiran Hari Ini</p>
                    <h2 class="page-title !text-xl">Live attendance</h2>
                </div>
                <a href="{{ route('checkin.index') }}" class="btn-pill gap-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z"/>
                    </svg>
                    Open Kiosk
                </a>
            </div>

            <div class="premium-scrollbar mt-6 -mx-6 overflow-x-auto px-6">
                <table class="min-w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th>Karyawan</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>GPS</th>
                        </tr>
                    </thead>
                    <tbody class="table-body divide-y divide-slate-800/50">
                        @forelse($todayAttendanceRecords as $record)
                            <tr class="transition-colors hover:bg-blue-500/[0.03]">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-700/40 text-[10px] font-semibold text-slate-300">
                                            {{ strtoupper(substr($record->employee->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-white">{{ $record->employee->name ?? 'N/A' }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $record->employee->nik ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-slate-300 tabular-nums">{{ optional($record->clock_in)->format('H:i') ?? '-' }}</td>
                                <td class="text-slate-300 tabular-nums">{{ optional($record->clock_out)->format('H:i') ?? '-' }}</td>
                                <td>
                                    @if($record->status === 'Present')
                                        <span class="badge-present">Present</span>
                                    @elseif($record->status === 'Late')
                                        <span class="badge-late">Late</span>
                                    @elseif($record->status === 'Alfa')
                                        <span class="badge-alfa">Alfa</span>
                                    @else
                                        <span class="badge-onleave">{{ $record->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->latitude)
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                            Verified
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">No signal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">Belum ada absensi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Activity Log --}}
        <article class="card-premium">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="section-title">Aktivitas Terbaru</p>
                    <h2 class="page-title !text-xl">Audit trail</h2>
                </div>
                <a href="{{ route('activity-logs.index') }}" class="btn-pill">Semua</a>
            </div>

            <div class="mt-6 space-y-1">
                @forelse($recentActivities as $activity)
                    <div class="activity-item rounded-xl px-3">
                        <div class="activity-dot">
                            <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-200">{{ $activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)) }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">{{ optional($activity->user)->name ?? 'System' }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-state text-sm text-slate-400">Belum ada aktivitas tercatat.</div>
                @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
