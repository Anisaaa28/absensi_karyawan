<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('search')) {
            $query->whereHas('employee', function ($employeeQuery) use ($request) {
                $employeeQuery->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('attendance_date', $request->date);
        }

        $attendances = $query->latest('attendance_date')->paginate(15)->withQueryString();

        return view('attendances.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_attendance',
            'description' => "Hapus absensi {$attendance->id}",
            'model_type' => Attendance::class,
            'model_id' => $attendance->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('attendances.index')->with('status', 'Absensi berhasil dihapus.');
    }
}
