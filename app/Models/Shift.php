<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'days',
        'location',
        'color',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_shift')->withTimestamps()->withPivot(['effective_from', 'effective_until']);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
