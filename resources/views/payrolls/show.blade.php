<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $payroll->employee->name }} - {{ $payroll->period_start->format('M Y') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #e2e8f0; padding: 24px; }
        .toolbar { max-width: 800px; margin: 0 auto 16px; display: flex; gap: 8px; }
        .toolbar button, .toolbar a { background: #0ea5e9; color: white; border: 0; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .toolbar button:hover, .toolbar a:hover { background: #0284c7; }
        .toolbar .secondary { background: #334155; }
        .toolbar .secondary:hover { background: #475569; }
        .payslip { max-width: 800px; margin: 0 auto; background: white; color: #0f172a; padding: 40px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0ea5e9; padding-bottom: 20px; margin-bottom: 24px; }
        .company { font-size: 18px; font-weight: 700; color: #0ea5e9; }
        .company-tag { font-size: 12px; color: #64748b; margin-top: 4px; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 22px; font-weight: 800; color: #0f172a; }
        .doc-title p { font-size: 12px; color: #64748b; margin-top: 4px; }
        .doc-title .ref { font-size: 11px; color: #94a3b8; font-family: monospace; margin-top: 4px; }
        .info { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; padding: 16px 20px; background: #f1f5f9; border-radius: 12px; }
        .info-row { display: flex; justify-content: space-between; font-size: 13px; }
        .info-row .label { color: #64748b; font-weight: 500; }
        .info-row .value { font-weight: 600; color: #0f172a; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #0ea5e9; color: white; padding: 10px 12px; font-size: 12px; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; }
        .table th.right { text-align: right; }
        .table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
        .table td.right { text-align: right; font-variant-numeric: tabular-nums; }
        .table .section td { background: #f1f5f9; font-weight: 700; text-transform: uppercase; font-size: 11px; color: #475569; }
        .table .total td { background: #0f172a; color: white; font-weight: 700; font-size: 14px; }
        .table .total td.right { font-size: 15px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 11px; color: #64748b; }
        .sign { margin-top: 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; text-align: center; font-size: 12px; }
        .sign .line { margin-top: 60px; border-top: 1px solid #0f172a; padding-top: 6px; }
        .sign .role { font-weight: 600; }
        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none; }
            .payslip { box-shadow: none; border-radius: 0; padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">🖨 Print / Save as PDF</button>
        <a href="{{ route('payrolls.index') }}" class="secondary">← Kembali ke daftar payroll</a>
    </div>

    <div class="payslip">
        <div class="header">
            <div>
                <div class="company">Absensi Karyawan</div>
                <div class="company-tag">Sistem Manajemen Absensi & Payroll</div>
            </div>
            <div class="doc-title">
                <h1>SLIP GAJI</h1>
                <p>Periode {{ $payroll->period_start->format('d M Y') }} – {{ $payroll->period_end->format('d M Y') }}</p>
                <p class="ref">Ref: PAY-{{ str_pad($payroll->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="info">
            <div>
                <div class="info-row"><span class="label">Nama</span><span class="value">{{ $payroll->employee->name }}</span></div>
                <div class="info-row"><span class="label">NIK</span><span class="value">{{ $payroll->employee->nik }}</span></div>
                <div class="info-row"><span class="label">Tipe</span><span class="value">{{ $payroll->employee->type }}</span></div>
            </div>
            <div>
                <div class="info-row"><span class="label">Bergabung</span><span class="value">{{ $payroll->employee->joined_at?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Hari Kerja</span><span class="value">{{ $payroll->present_days }} P · {{ $payroll->late_days }} L · {{ $payroll->absent_days }} A</span></div>
                <div class="info-row"><span class="label">Jam Kerja</span><span class="value">{{ number_format($payroll->worked_hours, 2) }} jam</span></div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th>Detail</th>
                    <th class="right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section"><td colspan="3">PENDAPATAN</td></tr>
                <tr>
                    <td>Gaji Pokok (pro-rata)</td>
                    <td>{{ $payroll->present_days + $payroll->late_days }} hari × Rp {{ number_format($payroll->gross_pay / max(1, $payroll->present_days + $payroll->late_days), 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($payroll->gross_pay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Lembur ({{ number_format($payroll->ot_hours, 2) }} jam)</td>
                    <td>OvertimeRequest Approved</td>
                    <td class="right">{{ number_format($payroll->ot_pay, 0, ',', '.') }}</td>
                </tr>
                @if((float) $payroll->allowance > 0)
                <tr>
                    <td>Tunjangan / Allowance</td>
                    <td>Flat bulanan</td>
                    <td class="right">{{ number_format($payroll->allowance, 0, ',', '.') }}</td>
                </tr>
                @endif

                <tr class="section"><td colspan="3">POTONGAN</td></tr>
                @if((float) $payroll->late_penalty > 0)
                <tr>
                    <td>Late Penalty</td>
                    <td>{{ $payroll->late_days }} hari × Rp 25.000</td>
                    <td class="right text-rose">- {{ number_format($payroll->late_penalty, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if((float) $payroll->tax > 0)
                <tr>
                    <td>Pajak (PPh 5%)</td>
                    <td>5% × gaji pokok</td>
                    <td class="right text-rose">- {{ number_format($payroll->tax, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if((float) $payroll->late_penalty == 0 && (float) $payroll->tax == 0)
                <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:14px;">Tidak ada potongan</td></tr>
                @endif

                <tr class="total">
                    <td colspan="2">GAJI BERSIH (Take Home Pay)</td>
                    <td class="right">Rp {{ number_format($payroll->net_pay, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="sign">
            <div>
                <div class="role">Disetujui oleh,</div>
                <div class="line">{{ $payroll->approver->name ?? '—' }}</div>
            </div>
            <div>
                <div class="role">Difinalisasi oleh,</div>
                <div class="line">{{ $payroll->finalizer->name ?? '—' }}</div>
            </div>
        </div>

        <div class="footer">
            <div>Status: <strong>{{ $payroll->status }}</strong></div>
            <div>Dicetak: {{ now()->format('d M Y H:i') }} WIB</div>
        </div>
    </div>
</body>
</html>
