@extends('layouts.app')

@section('content')
@php
    $collection = $requests->getCollection();
@endphp
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr] xl:items-end">
            <div>
                <p class="section-title">Overtime Request</p>
                <h1 class="page-title">Assign & approval lembur tim dengan estimasi biaya otomatis.</h1>
                <p class="mt-2 text-sm text-slate-400">Klik <strong>Assign Tim</strong> untuk menambahkan request lembur, kemudian approve atau reject dari board di bawah.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Pending</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $collection->where('status', 'Pending')->count() }}</p>
                </div>
                <div class="panel-outline rounded-[24px] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Approved Hrs</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($collection->where('status', 'Approved')->sum('hours'), 1) }}</p>
                </div>
                <a href="{{ route('overtimes.create') }}" class="btn-primary justify-center">Assign Tim</a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Pending</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $collection->where('status', 'Pending')->count() }}</p>
            <p class="mt-2 text-sm text-amber-300">Menunggu approval</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Approved</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $collection->where('status', 'Approved')->count() }}</p>
            <p class="mt-2 text-sm text-emerald-300">Siap payroll</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Rejected</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $collection->where('status', 'Rejected')->count() }}</p>
            <p class="mt-2 text-sm text-rose-300">Tidak disetujui</p>
        </article>
        <article class="card-kpi" data-reveal>
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Cost Estimate</p>
            <p class="mt-4 text-3xl font-semibold text-white">Rp {{ number_format($collection->sum(fn ($item) => $item->hours * $item->rate), 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-violet-300">Total nilai lembur</p>
        </article>
    </section>

    <section class="card-premium overflow-hidden" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Overtime Board</p>
                <h2 class="page-title text-2xl md:text-3xl">Approval workflow per status</h2>
            </div>
            <a href="{{ route('overtimes.create') }}" class="btn-primary !py-2 !px-4 !text-xs">+ Request Lembur</a>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-3">
            @foreach([
                ['title' => 'Pending Review', 'status' => 'Pending', 'ring' => 'border-amber-400/20', 'badge' => 'badge-pending'],
                ['title' => 'Approved', 'status' => 'Approved', 'ring' => 'border-emerald-400/20', 'badge' => 'badge-approved'],
                ['title' => 'Rejected', 'status' => 'Rejected', 'ring' => 'border-rose-400/20', 'badge' => 'badge-rejected'],
            ] as $column)
                <article class="rounded-[24px] border {{ $column['ring'] }} bg-slate-950/30 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="section-title">{{ $column['status'] }}</p>
                            <h3 class="page-title text-xl md:text-2xl">{{ $column['title'] }}</h3>
                        </div>
                        <span class="{{ $column['badge'] }}">{{ $collection->where('status', $column['status'])->count() }} item</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($collection->where('status', $column['status'])->take(10) as $overtime)
                            <div class="rounded-[20px] border border-white/10 bg-white/[0.03] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-white">{{ $overtime->employee->name ?? 'N/A' }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $overtime->employee->nik ?? '-' }} • {{ $overtime->employee->type ?? '-' }}</p>
                                    </div>
                                    <span class="{{ $column['badge'] }}">{{ $overtime->date->format('d M Y') }}</span>
                                </div>

                                <div class="mt-3 grid gap-2 text-sm text-slate-300">
                                    <div class="panel-outline rounded-[16px] px-3 py-2 tabular-nums">
                                        <span class="opacity-60">Jam</span> {{ \Illuminate\Support\Str::limit($overtime->start_time, 5, '') }} – {{ \Illuminate\Support\Str::limit($overtime->end_time, 5, '') }}
                                        <span class="ml-2 font-semibold text-white">{{ number_format($overtime->hours, 2) }} jam</span>
                                    </div>
                                    <div class="panel-outline rounded-[16px] px-3 py-2 tabular-nums">
                                        <span class="opacity-60">Rate</span> Rp {{ number_format($overtime->rate, 0, ',', '.') }}/jam
                                        <span class="ml-2 text-emerald-300">= Rp {{ number_format($overtime->hours * $overtime->rate, 0, ',', '.') }}</span>
                                    </div>
                                    @if($overtime->reason)
                                        <div class="rounded-[16px] bg-slate-950/45 p-3 text-xs leading-5 text-slate-300">
                                            {{ \Illuminate\Support\Str::limit($overtime->reason, 120) }}
                                        </div>
                                    @endif
                                    @if($overtime->comments)
                                        <div class="rounded-[16px] border border-white/10 bg-white/[0.02] p-3 text-xs text-slate-400">
                                            <span class="text-slate-300">Catatan:</span> {{ \Illuminate\Support\Str::limit($overtime->comments, 100) }}
                                        </div>
                                    @endif
                                </div>

                                @if($overtime->status === 'Pending')
                                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                        <form action="{{ route('overtimes.approve', $overtime) }}" method="POST">
                                            @csrf
                                            <button class="btn-primary w-full justify-center !py-2 !text-xs">Approve</button>
                                        </form>
                                        <form action="{{ route('overtimes.reject', $overtime) }}" method="POST" class="space-y-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="comments" placeholder="Alasan reject (opsional)" class="w-full !text-xs" />
                                            <button class="btn-secondary w-full justify-center !py-2 !text-xs">Reject</button>
                                        </form>
                                    </div>
                                @elseif($overtime->approver)
                                    <div class="mt-3 text-[11px] text-slate-500">
                                        oleh {{ $overtime->approver->name }} • {{ $overtime->updated_at->diffForHumans() }}
                                    </div>
                                @endif

                                <div class="mt-3 flex justify-end">
                                    <form action="{{ route('overtimes.destroy', $overtime) }}" method="POST" onsubmit="return confirm('Hapus request lembur {{ $overtime->employee->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] text-slate-500 hover:text-rose-300">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[20px] border border-dashed border-white/10 p-6 text-center text-xs text-slate-500">
                                Tidak ada request {{ strtolower($column['status']) }}.
                            </div>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if($requests->hasPages())
        <div class="flex items-center justify-center">
            {{ $requests->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
