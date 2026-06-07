<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'phone',
        'type',
        'photo_path',
        'joined_at',
        'status',
        'address',
        'base_salary',
        'allowance',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'base_salary' => 'decimal:2',
        'allowance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_shift')->withTimestamps()->withPivot(['effective_from', 'effective_until']);
    }
}
