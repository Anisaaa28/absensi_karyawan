@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr] xl:items-end">
            <div>
                <p class="section-title">Leave & Approval Workflow</p>
                <h1 class="page-title">Workflow cuti model approval board dengan konteks bukti, komentar, dan SLA review.</h1>
                
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Pending</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $requests->getCollection()->where('status', 'Pending')->count() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Approved</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $requests->getCollection()->where('status', 'Approved')->count() }}</p>
                </div>
                <a href="{{ route('leaves.create') }}" class="btn-primary justify-center">Ajukan Cuti</a>
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        @foreach([
            ['title' => 'Pending Review', 'status' => 'Pending', 'ring' => 'border-amber-400/20', 'badge' => 'badge-pending'],
            ['title' => 'Approved', 'status' => 'Approved', 'ring' => 'border-emerald-400/20', 'badge' => 'badge-approved'],
            ['title' => 'Rejected', 'status' => 'Rejected', 'ring' => 'border-rose-400/20', 'badge' => 'badge-rejected'],
        ] as $column)
            <article class="card-premium {{ $column['ring'] }}" data-reveal>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="section-title">{{ $column['status'] }}</p>
                        <h2 class="page-title text-2xl md:text-3xl">{{ $column['title'] }}</h2>
                    </div>
                    <span class="{{ $column['badge'] }}">{{ $requests->getCollection()->where('status', $column['status'])->count() }} item</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($requests->getCollection()->where('status', $column['status']) as $request)
                        <div class="rounded-[24px] border border-white/10 bg-white/[0.03] p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-white">{{ $request->employee->name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $request->employee->nik ?? '-' }} • {{ $request->type }}</p>
                                </div>
                                <span class="{{ $column['badge'] }}">{{ $request->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="mt-4 grid gap-3">
                                <div class="panel-outline rounded-[20px] p-3 text-sm text-slate-300">
                                    {{ $request->start_date->format('d M Y') }} - {{ $request->end_date->format('d M Y') }}
                                </div>
                                <div class="rounded-[20px] bg-slate-950/45 p-3 text-sm leading-6 text-slate-300">
                                    {{ $request->reason ?: 'Tidak ada alasan tambahan.' }}
                                </div>
                                @if($request->evidence_path)
                                    <div class="text-xs text-sky-300">Bukti terlampir: {{ basename($request->evidence_path) }}</div>
                                @endif
                            </div>

                            @if($request->status === 'Pending')
                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    <form action="{{ route('leaves.approve', $request) }}" method="POST">
                                        @csrf
                                        <button class="btn-primary w-full justify-center text-sm">Approve</button>
                                    </form>
                                    <form action="{{ route('leaves.reject', $request) }}" method="POST" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="comments" placeholder="Komentar penolakan" class="w-full text-sm" />
                                        <button class="btn-secondary w-full justify-center text-sm">Reject</button>
                                    </form>
                                </div>
                            @elseif($request->comments)
                                <div class="mt-4 rounded-[20px] border border-white/10 bg-white/[0.03] p-3 text-sm text-slate-300">
                                    Catatan: {{ $request->comments }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state text-slate-400">Tidak ada request {{ strtolower($column['status']) }}.</div>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
</div>
@endsection
