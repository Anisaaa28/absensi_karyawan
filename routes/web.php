<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftAssignmentController;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\OfficeLocation;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.submit');
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.submit');

Route::middleware(['session.auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('checkin', [CheckinController::class, 'show'])->name('checkin.index');
    Route::post('checkin', [CheckinController::class, 'store'])->name('checkin.submit');
    Route::get('checkin/employee/{nik}', [CheckinController::class, 'getEmployeeInfo'])->name('checkin.employee');

    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::put('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    Route::resource('shifts', ShiftController::class)->except(['show']);
    Route::get('shifts-assign/create', [ShiftAssignmentController::class, 'create'])->name('shifts.assign.create');
    Route::post('shifts-assign', [ShiftAssignmentController::class, 'store'])->name('shifts.assign.store');
    Route::delete('shifts-assign/{assignment}', [ShiftAssignmentController::class, 'destroy'])->name('shifts.assign.destroy');

    Route::get('overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('overtimes/create', [OvertimeController::class, 'create'])->name('overtimes.create');
    Route::post('overtimes', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::post('overtimes/{overtime}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::put('overtimes/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject');
    Route::delete('overtimes/{overtime}', [OvertimeController::class, 'destroy'])->name('overtimes.destroy');

    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::post('payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::post('payrolls/{payroll}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
    Route::post('payrolls/{payroll}/finalize', [PayrollController::class, 'finalize'])->name('payrolls.finalize');
    Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');

    Route::get('location-tracking', function () {
        return view('location-tracking.index', [
            'locations' => Attendance::with('employee')
                ->whereDate('attendance_date', today())
                ->whereNotNull('latitude')
                ->orderByDesc('updated_at')
                ->get(),
            'offices' => OfficeLocation::all(),
        ]);
    })->name('location-tracking.index');

    Route::get('activity-logs', function () {
        return view('activity-logs.index', [
            'logs' => ActivityLog::with('user')->latest()->paginate(20),
        ]);
    })->name('activity-logs.index');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});
