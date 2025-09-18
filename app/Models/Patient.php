<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'phone2',
        'file_number',
        'national_code',
        'birth_date',
        'registered_at',
        'gender',
        'address',
        'emergency_contact',
        'medical_history',
        'allergies',
        'insurance_type',
        'insurance_number',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registered_at' => 'date',
    ];

    protected $appends = ['full_name'];

    // روابط
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatmentPlans()
    {
        return $this->hasMany(TreatmentPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // متدهای کمکی
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        
        return $this->birth_date->age;
    }
}