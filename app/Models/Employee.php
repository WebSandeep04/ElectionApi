<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_type_id',
        'emp_name',
        'emp_email',
        'emp_password',
        'emp_phone',
        'emp_address',
        'emp_wages',
        'emp_date',
        'is_active',
        'emp_code',
        'emp_designation',
        'joining_date',
        'termination_date',
        'emp_status'
    ];

    protected $casts = [
        'emp_wages' => 'decimal:2',
        'emp_date' => 'date',
        'joining_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean'
    ];

    protected $hidden = [
        'emp_password'
    ];

    // Relationships
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    // Mutator for password hashing
    public function setEmpPasswordAttribute($value)
    {
        $this->attributes['emp_password'] = Hash::make($value);
    }

    // Accessor for formatted wages
    public function getFormattedWagesAttribute()
    {
        return $this->emp_wages ? '₹' . number_format($this->emp_wages, 2) : null;
    }

    // Scope for active employees
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('emp_status', 'active');
    }

    // Scope for inactive employees
    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhere('emp_status', '!=', 'active');
    }
}
