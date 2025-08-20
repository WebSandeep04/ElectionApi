<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'file_extension',
        'file_size',
        'mime_type',
        'description',
        'is_verified',
        'expiry_date'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expiry_date' => 'date'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scope for verified documents
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Scope for unverified documents
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    // Scope for expired documents
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    // Scope for valid documents
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }
}
