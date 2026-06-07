@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="glass-panel px-6 py-7 sm:px-8" data-reveal>
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr] xl:items-end">
            <div>
                <p class="section-title">Payroll Management</p>
                <h1 class="page-title">Hitung gaji karyawan otomatis dengan komponen lembur, tunjangan, pajak, dan penalty.</h1>
                <p class="mt-2 text-sm text-slate-400">Pilih periode → Generate → review breakdown → Approve → Finalize → cetak slip.</p>
            </div>
            <form action="{{ route('payrolls.generate') }}" method="POST" class="panel-outline rounded-[24px] p-4">
                @csrf
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Generate Payroll Baru</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs text-slate-400">Periode Mulai</label>
                        <input type="date" name="period_start" value="{{ old('period_start', now()->startOfMonth()->toDateString()) }}" required class="mt-1 w-full !text-xs" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Periode Selesai</label>
                        <input type="date" name="period_end" value="{{ old('period_end', now()->endOfMonth()->toDateString()) }}" required class="mt-1 w-full !text-xs" />
                    </div>
                </div>
                <button type="submit" class="btn-primary mt-3 w-full justify-center !py-2 !text-xs">Generate Payroll</button>
            </form>
        </div>
    </section>

    @if(session('status'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ session('error') }}</div>
    @endif

    <section class="card-premium overflow-hidden" data-reveal>
        <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="section-title">Payroll Table</p>
                <h2 class="page-title text-2xl md:text-3xl">Daftar Payroll</h2>
            </div>
        </div>

        <div class="premium-scrollbar overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Hari (P/L/A)</th>
                        <th class="text-right">Gaji Pokok</th>
                        <th class="text-right">OT</th>
                        <th class="text-right">Penalty</th>
                        <th class="text-right">Pajak</th>
                        <th class="text-right">Net Pay</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body divide-y divide-slate-800">
                    @forelse($payrolls as $payroll)
                        <tr>
                            <td>
                                <p class="font-semibold text-white">{{ $payroll->employee->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $payroll->employee->nik ?? '-' }}</p>
                            </td>
                            <td class="text-slate-300 tabular-nums">
                                {{ $payroll->period_start->format('d M') }} – {{ $payroll->period_end->format('d M Y') }}
                            </td>
                            <td class="text-slate-300 tabular-nums">
                                <span class="font-semibold text-emerald-300">{{ $payroll->present_days }}</span>
                                <span class="text-slate-500">/</span>
                                <span class="font-semibold text-amber-300">{{ $payroll->late_days }}</span>
                                <span class="text-slate-500">/</span>
                                <span class="font-semibold text-rose-300">{{ $payroll->absent_days }}</span>
                            </td>
                            <td class="text-right text-slate-300 tabular-nums">Rp {{ number_format($payroll->gross_pay, 0, ',', '.') }}</td>
                            <td class="text-right text-emerald-300 tabular-nums">Rp {{ number_format($payroll->ot_pay, 0, ',', '.') }}</td>
                            <td class="text-right text-rose-300 tabular-nums">Rp {{ number_format($payroll->late_penalty, 0, ',', '.') }}</td>
                            <td class="text-right text-amber-300 tabular-nums">Rp {{ number_format($payroll->tax, 0, ',', '.') }}</td>
                            <td class="text-right font-bold text-white tabular-nums">Rp {{ number_format($payroll->net_pay, 0, ',', '.') }}</td>
                            <td>
                                @if($payroll->status === 'Draft')
                                    <span class="badge-pending">Draft</span>
                                @elseif($payroll->status === 'Approved')
                                    <span class="badge-approved">Approved</span>
                                @else
                                    <span class="rounded-full border border-sky-400/30 bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-200">Finalized</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payrolls.show', $payroll) }}" class="btn-pill !text-xs">Slip</a>

                                    @if($payroll->status === 'Draft')
                                        <form action="{{ route('payrolls.approve', $payroll) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-pill !border-emerald-500/30 !text-emerald-300 hover:!bg-emerald-500/10 !text-xs">Approve</button>
                                        </form>
                                    @elseif($payroll->status === 'Approved')
                                        <form action="{{ route('payrolls.finalize', $payroll) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-pill !border-sky-500/30 !text-sky-300 hover:!bg-sky-500/10 !text-xs">Finalize</button>
                                        </form>
                                    @endif

                                    @if($payroll->status !== 'Finalized')
                                        <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" onsubmit="return confirm('Hapus payroll ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary !px-3 !py-1.5 !text-xs">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <p class="text-lg font-semibold text-white">Belum ada payroll</p>
                                <p class="mt-2 text-sm text-slate-400">Pilih periode di atas lalu klik <strong>Generate Payroll</strong>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div class="flex items-center justify-center border-t border-white/10 px-6 py-4">
                {{ $payrolls->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
