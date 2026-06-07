<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('identifier');
        $user = User::where('email', $identifier)
            ->orWhere('nik', $identifier)
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['identifier' => 'Credential tidak valid'])->withInput();
        }

        if ($user->status !== 'Active') {
            return back()->withErrors(['identifier' => 'Akun tidak aktif'])->withInput();
        }

        Session::put('user_id', $user->id);
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'User berhasil login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => 'User logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Session::forget('user_id');
        return Redirect::route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nik' => 'nullable|string|unique:users,nik',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'password' => Hash::make($request->password),
            'role' => 'Employee',
        ]);

        return Redirect::route('login')->with('status', 'Akun berhasil dibuat. Silakan login.');
    }
}
