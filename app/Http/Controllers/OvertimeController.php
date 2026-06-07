<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OvertimeController extends Controller
{
    public function index()
    {
        $requests = OvertimeRequest::with(['employee', 'approver'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('overtimes.index', compact('requests'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('overtimes.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
            'comments' => 'nullable|string|max:1000',
        ]);

        $hours = $this->calculateHours($data['start_time'], $data['end_time']);
        if ($hours <= 0) {
            return Redirect::back()->withInput()->withErrors(['end_time' => 'Jam selesai harus setelah jam mulai.']);
        }

        $employee = Employee::findOrFail($data['employee_id']);
        $rate = $this->estimateRate($employee);

        $overtime = OvertimeRequest::create([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'reason' => $data['reason'] ?? null,
            'comments' => $data['comments'] ?? null,
            'status' => 'Pending',
            'hours' => $hours,
            'rate' => $rate,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_overtime',
            'description' => "Assign lembur untuk {$employee->name} ({$hours} jam)",
            'model_type' => OvertimeRequest::class,
            'model_id' => $overtime->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('overtimes.index')->with('status', "Request lembur untuk {$employee->name} berhasil dibuat dan menunggu approval.");
    }

    public function approve(Request $request, OvertimeRequest $overtime)
    {
        $overtime->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_overtime',
            'description' => "Approve lembur #{$overtime->id} ({$overtime->employee->name})",
            'model_type' => OvertimeRequest::class,
            'model_id' => $overtime->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('overtimes.index')->with('status', 'Lembur disetujui.');
    }

    public function reject(Request $request, OvertimeRequest $overtime)
    {
        $request->validate(['comments' => 'nullable|string|max:500']);

        $overtime->update([
            'status' => 'Rejected',
            'approved_by' => auth()->id(),
            'comments' => $request->comments ?? $overtime->comments,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_overtime',
            'description' => "Reject lembur #{$overtime->id} ({$overtime->employee->name})",
            'model_type' => OvertimeRequest::class,
            'model_id' => $overtime->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('overtimes.index')->with('status', 'Lembur ditolak.');
    }

    public function destroy(Request $request, OvertimeRequest $overtime)
    {
        $name = $overtime->employee->name;
        $overtime->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_overtime',
            'description' => "Hapus request lembur {$name}",
            'model_type' => OvertimeRequest::class,
            'model_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('overtimes.index')->with('status', 'Request lembur dihapus.');
    }

    private function calculateHours(string $start, string $end): float
    {
        $startMin = $this->toMinutes($start);
        $endMin = $this->toMinutes($end);
        if ($endMin <= $startMin) {
            // Shift malam: melewati tengah malam
            $endMin += 24 * 60;
        }
        return round(($endMin - $startMin) / 60, 2);
    }

    private function estimateRate(Employee $employee): float
    {
        $base = (float) $employee->base_salary;
        if ($base <= 0) {
            return 0;
        }
        // Asumsi 173 jam kerja per bulan, rate lembur 1.5x
        $hourly = $base / 173;
        return round($hourly * 1.5, 2);
    }

    private function toMinutes(string $time): int
    {
        [$h, $m] = array_pad(explode(':', $time), 2, '0');
        return ((int) $h) * 60 + (int) $m;
    }
}
