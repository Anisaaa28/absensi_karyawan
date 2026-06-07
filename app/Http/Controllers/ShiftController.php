<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::with('employees')->latest()->get();

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $employeeFilter = $request->query('employee');

        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startGrid = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endGrid = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $assignmentsQuery = \App\Models\ShiftAssignment::with(['employee', 'shift'])
            ->whereBetween('work_date', [$startGrid->toDateString(), $endGrid->toDateString()]);

        if ($employeeFilter) {
            $assignmentsQuery->where('employee_id', $employeeFilter);
        }

        $assignments = $assignmentsQuery->get()->groupBy(fn ($a) => $a->work_date->toDateString());

        $attendanceMap = \App\Models\Attendance::whereBetween('attendance_date', [$startGrid->toDateString(), $endGrid->toDateString()])
            ->get(['employee_id', 'attendance_date', 'clock_in', 'clock_out'])
            ->mapWithKeys(function ($att) {
                $dateKey = \Illuminate\Support\Carbon::parse($att->attendance_date)->toDateString();
                return [$att->employee_id . '|' . $dateKey => [
                    'clock_in' => $att->clock_in,
                    'clock_out' => $att->clock_out,
                ]];
            })->all();

        return view('shifts.index', [
            'shifts' => $shifts,
            'month' => $month,
            'year' => $year,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'startGrid' => $startGrid,
            'endGrid' => $endGrid,
            'assignments' => $assignments,
            'attendanceMap' => $attendanceMap,
            'employees' => \App\Models\Employee::where('status', 'Active')->orderBy('name')->get(),
            'employeeFilter' => $employeeFilter,
        ]);
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateShift($request);

        $shift = Shift::create($data);
        $this->logActivity('create_shift', "Buat shift {$shift->name}", $shift);

        return Redirect::route('shifts.index')->with('status', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $data = $this->validateShift($request);

        $shift->update($data);
        $this->logActivity('update_shift', "Update shift {$shift->name}", $shift);

        return Redirect::route('shifts.index')->with('status', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $name = $shift->name;
        $shift->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_shift',
            'description' => "Hapus shift {$name}",
            'model_type' => Shift::class,
            'model_id' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('shifts.index')->with('status', 'Shift berhasil dihapus.');
    }

    private function validateShift(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days' => 'nullable|array',
            'days.*' => 'string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);
    }

    private function logActivity(string $action, string $description, Shift $shift): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => Shift::class,
            'model_id' => $shift->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
