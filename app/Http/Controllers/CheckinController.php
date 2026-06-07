<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckinController extends Controller
{
    public function show()
    {
        return view('checkin.index');
    }

    public function getEmployeeInfo($nik)
    {
        $employee = Employee::where('nik', $nik)->where('status', 'Active')->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Karyawan tidak ditemukan atau tidak aktif'], 404);
        }

        return response()->json([
            'id' => $employee->id,
            'nik' => $employee->nik,
            'name' => $employee->name,
            'type' => $employee->type
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|integer',
            'mode' => 'required|in:in,out',
        ]);

        $employee = Employee::where('nik', $data['nik'])->where('status', 'Active')->first();
        if (! $employee) {
            return Redirect::back()->withErrors(['nik' => 'Karyawan tidak ditemukan atau tidak aktif.']);
        }

        $today = today();
        $now = now();
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
        ]);

        if ($data['mode'] === 'in') {
            $attendance->clock_in = $now;
            $attendance->status = 'Present';
        } else {
            if (! $attendance->clock_in) {
                return Redirect::back()->withErrors(['mode' => 'Belum ada clock in hari ini.']);
            }
            $attendance->clock_out = $now;
            $attendance->status = $attendance->clock_in && $attendance->clock_in->diffInMinutes($attendance->clock_out) < 60 ? 'Early Out' : $attendance->status;
        }

        $attendance->shift_id = $this->resolveShiftId($employee, $now);
        $attendance->latitude = $data['latitude'];
        $attendance->longitude = $data['longitude'];
        $attendance->accuracy = $data['accuracy'];
        $attendance->created_by = auth()->id();
        $attendance->save();

        if (
            Schema::getConnection()->getDriverName() === 'pgsql'
            && $attendance->latitude !== null
            && $attendance->longitude !== null
        ) {
            DB::statement(
                'UPDATE attendances SET gps_point = ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography WHERE id = ?',
                [(float) $attendance->longitude, (float) $attendance->latitude, $attendance->id]
            );
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'attendance_' . $data['mode'],
            'description' => "{$employee->name} melakukan clock {$data['mode']}",
            'model_type' => Attendance::class,
            'model_id' => $attendance->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::back()->with('status', "Absensi berhasil: {$employee->name}. Mode: {$data['mode']}." );
    }

    /**
     * Tentukan shift_id untuk karyawan pada waktu scan tertentu.
     * Prioritas:
     *   1. Assignment manual admin untuk tanggal hari ini.
     *   2. Fallback: shift yang hari & jam-nya cocok dengan waktu scan.
     */
    private function resolveShiftId(Employee $employee, Carbon $now): ?int
    {
        $assignment = ShiftAssignment::where('employee_id', $employee->id)
            ->whereDate('work_date', $now->toDateString())
            ->first();
        if ($assignment) {
            return $assignment->shift_id;
        }

        $dayName = $this->indonesianDayName($now);
        $currentMinutes = $now->hour * 60 + $now->minute;

        $candidate = Shift::whereNotNull('days')
            ->get()
            ->first(function (Shift $shift) use ($dayName, $currentMinutes) {
                $days = $shift->days ?? [];
                if (! in_array($dayName, $days, true)) {
                    return false;
                }
                $start = $this->toMinutes($shift->start_time);
                $end = $this->toMinutes($shift->end_time);
                if ($start === null || $end === null) {
                    return false;
                }
                // Shift yang melewati tengah malam (mis. 23:00 → 07:00)
                if ($end <= $start) {
                    return $currentMinutes >= $start || $currentMinutes <= $end;
                }
                return $currentMinutes >= $start && $currentMinutes <= $end;
            });

        return $candidate?->id;
    }

    private function indonesianDayName(Carbon $date): string
    {
        return [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ][$date->format('l')] ?? '';
    }

    private function toMinutes(?string $time): ?int
    {
        if (! $time) {
            return null;
        }
        $parts = explode(':', $time);
        if (count($parts) < 2) {
            return null;
        }
        return ((int) $parts[0]) * 60 + (int) $parts[1];
    }
}
