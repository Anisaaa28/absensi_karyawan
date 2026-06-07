<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class ShiftAssignmentController extends Controller
{
    public function create(Request $request)
    {
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        $prefillDate = $request->query('date', today()->toDateString());
        $prefillEmployee = $request->query('employee');

        return view('shifts.assign', [
            'employees' => $employees,
            'shifts' => $shifts,
            'prefillDate' => $prefillDate,
            'prefillEmployee' => $prefillEmployee,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'work_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $assignment = ShiftAssignment::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'work_date' => $data['work_date']],
            ['shift_id' => $data['shift_id'], 'notes' => $data['notes'] ?? null, 'assigned_by' => auth()->id()]
        );

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'assign_shift',
            'description' => "Assign shift ke {$assignment->employee->name} untuk tanggal {$assignment->work_date->format('d M Y')}",
            'model_type' => ShiftAssignment::class,
            'model_id' => $assignment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'assignment' => $assignment]);
        }

        return Redirect::route('shifts.index', ['month' => $assignment->work_date->month, 'year' => $assignment->work_date->year])
            ->with('status', 'Assignment shift berhasil disimpan.');
    }

    public function destroy(Request $request, ShiftAssignment $assignment)
    {
        $workDate = $assignment->work_date;
        $employeeName = $assignment->employee->name;

        $assignment->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'unassign_shift',
            'description' => "Hapus assignment shift {$employeeName} tanggal {$workDate->format('d M Y')}",
            'model_type' => ShiftAssignment::class,
            'model_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return Redirect::route('shifts.index', ['month' => $workDate->month, 'year' => $workDate->year])
            ->with('status', 'Assignment shift berhasil dihapus.');
    }
}
