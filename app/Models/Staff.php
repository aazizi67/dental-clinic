<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceRecord;

class Staff extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'national_id',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'role',
        'hourly_rate',
        'hire_date',
        'birth_date',
        'is_active'
    ];
    
    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];
    
    // Accessor to get full name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    // Relationship with attendance records
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
    
    // Scope for active staff
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Scope for specific roles
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}