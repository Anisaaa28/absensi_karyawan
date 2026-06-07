@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-end">
            <div>
                <p class="section-title">Activity Log</p>
                <h1 class="page-title">Audit trail semua aksi penting untuk absensi, approval, dan data master.</h1>
                
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Log</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $logs->total() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Today</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $logs->getCollection()->filter(fn ($log) => $log->created_at?->isToday())->count() }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-primary justify-center">Back To KPI</a>
            </div>
        </div>
    </section>

    <section class="card-premium" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Audit Timeline</p>
                <h2 class="page-title text-2xl md:text-3xl">Riwayat aktivitas terbaru</h2>
            </div>
            
        </div>

        <div class="space-y-4 p-6">
            @forelse($logs as $log)
                <div class="flex gap-4 rounded-[24px] border border-white/10 bg-white/[0.03] p-4">
                    <div class="stat-orb flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-[16px] bg-sky-500/10 text-xs font-semibold text-sky-300">
                        {{ strtoupper(substr($log->action, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $log->description ?: ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ optional($log->user)->name ?? 'System' }} • {{ $log->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <span class="btn-pill">{{ $log->action }}</span>
                        </div>
                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div class="panel-outline rounded-[20px] p-3 text-xs text-slate-300">Model: {{ class_basename($log->model_type) ?: '-' }}</div>
                            <div class="panel-outline rounded-[20px] p-3 text-xs text-slate-300">IP: {{ $log->ip_address ?: '-' }}</div>
                            <div class="panel-outline rounded-[20px] p-3 text-xs text-slate-300">ID: {{ $log->model_id ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state text-slate-400">Belum ada aktivitas sistem.</div>
            @endforelse
        </div>
    </section>

    @if($logs->hasPages())
        <div class="flex items-center justify-center">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
