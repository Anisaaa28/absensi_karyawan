<?php

namespace Database\Seeders;

// use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@absensi.local',
        ], [
            'name' => 'Super Admin',
            'nik' => 'ADMIN001',
            'phone' => '081234567890',
            'role' => 'Super Admin',
            'status' => 'Active',
            'password' => Hash::make('password123'),
        ]);

        Employee::updateOrCreate([
            'nik' => 'ADMIN001',
        ], [
            'user_id' => $admin->id,
            'name' => 'Super Admin',
            'phone' => '081234567890',
            'type' => 'Helper',
            'joined_at' => now()->format('Y-m-d'),
            'status' => 'Active',
            'address' => 'Head Office',
            'base_salary' => 0,
        ]);
    }
}
