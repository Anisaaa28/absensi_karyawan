<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PayrollController extends Controller
{
    private const LATE_PENALTY_PER_DAY = 25000;

    private const TAX_PERCENT = 5;

    public function index()
    {
        $payrolls = Payroll::with(['employee', 'approver', 'finalizer'])
            ->latest('period_end')
            ->paginate(30)
            ->withQueryString();

        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('payrolls.index', compact('payrolls', 'employees'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $periodStart = \Illuminate\Support\Carbon::parse($data['period_start'])->startOfDay();
        $periodEnd = \Illuminate\Support\Carbon::parse($data['period_end'])->endOfDay();
        $periodDays = $periodStart->diffInDays($periodEnd) + 1;

        $employees = Employee::where('status', 'Active')->get();
        $created = 0;

        foreach ($employees as $employee) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get();

            $presentDays = $attendances->where('status', 'Present')->count();
            $lateDays = $attendances->where('status', 'Late')->count();
            $absentDays = max(0, $periodDays - $attendances->count());
            $workedDays = $attendances->count();

            $workedHours = $attendances
                ->filter(fn ($a) => $a->clock_in && $a->clock_out)
                ->reduce(function ($carry, $a) {
                    return $carry + ($a->clock_out->getTimestamp() - $a->clock_in->getTimestamp()) / 3600;
                }, 0);

            // Lembur dari OvertimeRequest Approved
            $approvedOt = OvertimeRequest::where('employee_id', $employee->id)
                ->where('status', 'Approved')
                ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get();

            $otHours = $approvedOt->sum('hours');
            $otPay = $approvedOt->sum(fn ($o) => $o->hours * $o->rate);

            // Perhitungan utama
            $baseSalary = (float) $employee->base_salary;
            $allowance = (float) $employee->allowance;
            $dailyRate = $baseSalary / 30; // asumsi 30 hari kerja standar

            $grossPay = round($dailyRate * $workedDays, 2);
            $latePenalty = $lateDays * self::LATE_PENALTY_PER_DAY;
            $tax = round($grossPay * (self::TAX_PERCENT / 100), 2);
            $netPay = round($grossPay + $otPay + $allowance - $latePenalty - $tax, 2);

            Payroll::create([
                'employee_id' => $employee->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'worked_days' => $workedDays,
                'worked_hours' => round($workedHours, 2),
                'ot_hours' => round($otHours, 2),
                'ot_pay' => $otPay,
                'allowance' => $allowance,
                'gross_pay' => $grossPay,
                'late_penalty' => $latePenalty,
                'deductions' => 0,
                'tax' => $tax,
                'net_pay' => $netPay,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'status' => 'Draft',
            ]);

            $created++;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'generate_payroll',
            'description' => "Generate payroll periode {$periodStart->format('d M Y')} - {$periodEnd->format('d M Y')} ({$created} karyawan)",
            'model_type' => Payroll::class,
            'model_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('payrolls.index')->with('status', "Payroll periode {$periodStart->format('d M Y')} – {$periodEnd->format('d M Y')} berhasil di-generate untuk {$created} karyawan.");
    }

    public function approve(Request $request, Payroll $payroll)
    {
        if ($payroll->status !== 'Draft') {
            return Redirect::route('payrolls.index')->with('error', 'Hanya payroll berstatus Draft yang bisa di-approve.');
        }

        $payroll->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_payroll',
            'description' => "Approve payroll #{$payroll->id} ({$payroll->employee->name})",
            'model_type' => Payroll::class,
            'model_id' => $payroll->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('payrolls.index')->with('status', 'Payroll disetujui.');
    }

    public function finalize(Request $request, Payroll $payroll)
    {
        if ($payroll->status !== 'Approved') {
            return Redirect::route('payrolls.index')->with('error', 'Payroll harus di-approve dulu sebelum di-finalize.');
        }

        $payroll->update([
            'status' => 'Finalized',
            'finalized_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'finalize_payroll',
            'description' => "Finalize payroll #{$payroll->id} ({$payroll->employee->name})",
            'model_type' => Payroll::class,
            'model_id' => $payroll->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('payrolls.index')->with('status', 'Payroll di-finalize dan siap diberikan ke karyawan.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'approver', 'finalizer']);

        return view('payrolls.show', compact('payroll'));
    }

    public function destroy(Request $request, Payroll $payroll)
    {
        if ($payroll->status === 'Finalized') {
            return Redirect::route('payrolls.index')->with('error', 'Payroll yang sudah di-finalize tidak bisa dihapus.');
        }

        $name = $payroll->employee->name;
        $payroll->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_payroll',
            'description' => "Hapus payroll #{$payroll->id} ({$name})",
            'model_type' => Payroll::class,
            'model_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('payrolls.index')->with('status', 'Payroll dihapus.');
    }
}
