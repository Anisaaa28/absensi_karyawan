<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalEmployees = Employee::where('status', 'Active')->count();
        $today = today();
        $attendancesToday = Attendance::where('attendance_date', $today)->count();
        $lateCount = Attendance::where('attendance_date', $today)->where('status', 'Late')->count();
        $leavePending = LeaveRequest::where('status', 'Pending')->count();
        $overtimePending = OvertimeRequest::where('status', 'Pending')->count();
        $pendingRequests = $leavePending + $overtimePending;
        $overtimeHours = OvertimeRequest::where('status', 'Approved')->sum('hours');
        $payrollEstimate = Payroll::where('status', 'Draft')->sum('net_pay');
        $onLeaveToday = LeaveRequest::where('status', 'Approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();
        $alfaCount = max($totalEmployees - $attendancesToday - $onLeaveToday, 0);
        $attendanceRate = $totalEmployees > 0 ? round(($attendancesToday / $totalEmployees) * 100) : 0;

        $todayAttendanceRecords = Attendance::with('employee')
            ->whereDate('attendance_date', $today)
            ->orderByDesc('clock_in')
            ->limit(6)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $attendanceTrend = collect(range(6, 0))->map(function ($offset) use ($today) {
            $date = $today->copy()->subDays($offset);

            return [
                'date' => $date->format('d M'),
                'count' => Attendance::whereDate('attendance_date', $date)->count(),
            ];
        });

        $employeesByType = Employee::where('status', 'Active')
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->orderBy('type')
            ->get();

        return view('dashboard.index', compact(
            'totalEmployees',
            'attendancesToday',
            'lateCount',
            'pendingRequests',
            'leavePending',
            'overtimePending',
            'overtimeHours',
            'payrollEstimate',
            'onLeaveToday',
            'alfaCount',
            'attendanceRate',
            'todayAttendanceRecords',
            'recentActivities',
            'attendanceTrend',
            'employeesByType'
        ));
    }
}
