<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(request('type'), fn ($query, $type) => $query->where('type', $type))
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|string|unique:employees,nik',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'type' => 'required|in:Security,Cleaning Service,Helper',
            'joined_at' => 'nullable|date',
            'status' => 'required|in:Active,Inactive',
            'address' => 'nullable|string',
            'base_salary' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::create($data);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_employee',
            'description' => "Buat karyawan {$employee->name}",
            'model_type' => Employee::class,
            'model_id' => $employee->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('employees.index')->with('status', 'Karyawan berhasil disimpan.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'type' => 'required|in:Security,Cleaning Service,Helper',
            'joined_at' => 'nullable|date',
            'status' => 'required|in:Active,Inactive',
            'address' => 'nullable|string',
            'base_salary' => 'nullable|numeric|min:0',
        ]);

        $employee->update($data);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_employee',
            'description' => "Update data karyawan {$employee->name}",
            'model_type' => Employee::class,
            'model_id' => $employee->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('employees.index')->with('status', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_employee',
            'description' => "Hapus karyawan {$employee->name}",
            'model_type' => Employee::class,
            'model_id' => $employee->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Redirect::route('employees.index')->with('status', 'Karyawan berhasil dihapus.');
    }
}
