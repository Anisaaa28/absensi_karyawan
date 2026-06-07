<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with('employee')->latest()->paginate(15);

        return view('leaves.index', compact('requests'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:Sakit,Ijin,Alfa',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'comments' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('evidence')) {
            $data['evidence_path'] = $request->file('evidence')->store('leave-evidence', 'public');
        }

        unset($data['evidence']);

        $leave = LeaveRequest::create($data);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_leave_request',
            'description' => "Ajukan cuti untuk employee {$leave->employee_id}",
            'model_type' => LeaveRequest::class,
            'model_id' => $leave->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('leaves.index')->with('status', 'Permintaan cuti berhasil dikirim.');
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_leave_request',
            'description' => "Approve cuti {$leave->id}",
            'model_type' => LeaveRequest::class,
            'model_id' => $leave->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('leaves.index')->with('status', 'Cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['comments' => 'nullable|string']);

        $leave->update([
            'status' => 'Rejected',
            'approved_by' => auth()->id(),
            'comments' => $request->comments,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_leave_request',
            'description' => "Reject cuti {$leave->id}",
            'model_type' => LeaveRequest::class,
            'model_id' => $leave->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('leaves.index')->with('status', 'Cuti ditolak.');
    }
}
