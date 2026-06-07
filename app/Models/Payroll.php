<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'worked_days',
        'worked_hours',
        'ot_hours',
        'ot_pay',
        'allowance',
        'gross_pay',
        'late_penalty',
        'deductions',
        'tax',
        'net_pay',
        'present_days',
        'late_days',
        'absent_days',
        'status',
        'notes',
        'approved_by',
        'finalized_by',
        'pdf_path',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'worked_hours' => 'decimal:2',
        'ot_hours' => 'decimal:2',
        'ot_pay' => 'decimal:2',
        'allowance' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'late_penalty' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
